<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Http\ServerRequest;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSeo;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use App\Validator\Api\PostCommentSendValidator;
use App\Validator\Api\PostCommentChangeStatusValidator;
use App\Validator\Api\PostCommentDeleteValidator;

class PostCommentsController extends AppController
{
    public function beforeFilter(Event $event)
    {
        $purpleGlobal = new PurpleProjectGlobal();
        $databaseInfo   = $purpleGlobal->databaseInfo();
        if ($databaseInfo == 'default') {
            throw new NotFoundException(__('Page not found'));
        }
        else {
            $purpleSettings = new PurpleProjectSettings();
            $maintenance    = $purpleSettings->maintenanceMode();
            $userLoggedIn   = $purpleSettings->checkUserLoggedIn();

            if ($maintenance == 'enable' && $userLoggedIn == false) {
                throw new NotFoundException(__('Page not found'));
            }
        }
    }
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Basic' => [
                    'fields'    => ['username' => 'username', 'password' => 'api_key'],
                    'userModel' => 'Admins',
                ],
            ],
            'authorize'            => 'Controller',
            'authError'            => 'Unauthorized access',
            'storage'              => 'Memory',
            'unauthorizedRedirect' => false,
            'loginAction'          => [
                '_name' => 'adminLoginApi',
            ]
        ]);

        // Allow GET method and send 
        $this->Auth->allow(['view', 'send']);

        $this->loadModel('Comments');
        $this->loadModel('Blogs');
        $this->loadModel('Notifications');
        $this->loadModel('Admins');
        $this->loadModel('Settings');
        $this->loadModel('Histories');

        $this->viewBuilder()->enableAutoLayout(false);

        $purpleGlobal = new PurpleProjectGlobal();
		$protocol     = $purpleGlobal->protocol();

        $data = [
            'baseUrl' => $protocol . $this->request->host() . $this->request->getAttribute("webroot")
        ];

        $this->set($data);
    }
    public function isAuthorized($user)
    {
        // Only admins can access admin functions
        if (isset($user['level']) && $user['level'] == '1') {
            return true;
        }

        // Default deny
        return false;
    }
    public function view($blogId) 
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $blogId = trim($blogId);

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $blogs = $this->Blogs->find('all', [
                    'order' => ['Blogs.created' => 'DESC']
                    ])
                    ->contain('BlogCategories', function (Query $q) {
                        return $q
                            ->select(['id', 'name', 'slug', 'admin_id']);  
                    })
                    ->contain('Admins', function (Query $q) {
                        return $q
                            ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                    })
                    ->where([
                        'Blogs.status' => '1',
                        'Blogs.id' => $blogId 
                    ]);

                if ($blogs->count() > 0) {
                    $blog = $blogs->first();

                    // Query string for additional condition
                    $orderBy = $this->request->getQuery('order_by');
                    $order   = $this->request->getQuery('order');
                    if ($order !== NULL && $orderBy !== NULL) {
                        /**
                         * Order By : created
                         * Order : ASC, DESC
                         */
                        $availableOrderBy = ['created'];
                        $availableOrder   = ['asc', 'desc'];
                        
                        if (in_array($orderBy, $availableOrderBy) && in_array($order, $availableOrder)) {
                            $orderQuery = ['Comments.' . $orderBy => strtoupper($order)];
                        }
                        else {
                            $orderQuery = ['Comments.created' => 'ASC'];
                            $error      = "Invalid query string. Please read the documentation for available query string.";
                        }
                    }
                    else {
                        $orderQuery = ['Comments.created' => 'ASC'];
                    }

                    $comments = $this->Comments->find('all', [
                        'order' => $orderQuery
                    ])->contain(['Admins'])->where(['Comments.status <>' => '0', 'Comments.blog_id' => $blog->id, 'Comments.admin_id IS' => NULL]);

                    if ($comments->count() > 0) {
                        foreach ($comments as $comment) {
                            $totalReplies = $this->Comments->replyComments($comment->blog_id, $comment->id, 'count');
                            $comment->total_replies = $totalReplies;
                            if ($totalReplies > 0) {
                                $commentReplies = $this->Comments->find('all', [
                                    'order' => ['Comments.created' => 'ASC']
                                ])->contain('Admins', function (Query $q) {
                                    return $q
                                        ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                                })->where(['Comments.blog_id' => $comment->blog_id, 'Comments.reply' => $comment->id]);
                                $comment->comment_replies = $commentReplies;
                            }
                        }

                        $return = [
                            'status'   => 'ok',
                            'total'    => $comments->count(),
                            'comments' => $comments,
                            'post'     => $blog,
                            'error'    => $error
                        ];
                    }
                    else {
                        $return = [
                            'status'   => 'ok',
                            'total'    => 0,
                            'comments' => NULL,
                            'error'    => $error
                        ];
                    }
                }
                else {
                    $return = [
                        'status'   => 'ok',
                        'comments' => NULL,
                        'error'    => 'Post not found'
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
	        throw new NotFoundException(__('Page not found'));
        }
    }
    public function send()
    {
        if ($this->request->is('post') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $postCommentSendValidator = new PostCommentSendValidator();
                $errorValidate            = $postCommentSendValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $purpleApi = new PurpleProjectApi();
                    $verifyEmail = $purpleApi->verifyEmail($this->request->getData('email'));

                    if ($verifyEmail == true) {
                        $comment = $this->Comments->newEntity();
                        $comment = $this->Comments->patchEntity($comment, $this->request->getData());
                        $comment->status = '0';
                        $comment->reply  = '0';

                        if ($this->Comments->save($comment)) {
                            $recordId = $comment->id;

                            /**
                             * Save data to Notifications Table
                             */
                            $notification = $this->Notifications->newEntity();
                            $notification->type       = 'comment';
                            $notification->content    = $this->request->getData('name').' sent a comment to your post. Click to view the comment.';
                            $notification->comment_id = $recordId;
                            $notification->blog_id    = $this->request->getData('blog_id');

                            // Send Email to User to Notify author
                            $blog   = $this->Blogs->get($this->request->getData('blog_id'));
                            $author = $this->Admins->get($blog->admin_id);
                            $key    = $this->Settings->settingsPublicApiKey();
                            $dashboardLink = $this->request->getData('ds');
                            $userData      = array(
                                'sitename'    => $this->Settings->settingsSiteName(),
                                'email'       => $author->email,
                                'displayName' => $author->display_name,
                                'level'       => $author->level
                            );
                            $post          = $blog->title;
                            $commentData   = array(
                                'name'   => $this->request->getData('name'),
                                'email'  => $this->request->getData('email'),
                                'blogId' => $this->request->getData('blog_id'),
                                'domain' => $this->request->domain()
                            );
                            $notifyUser = $purpleApi->sendEmailPostComment($key, $dashboardLink, json_encode($userData), $post, json_encode($commentData));

                            if ($notifyUser == true) {
                                $emailNotification = true;
                            }
                            else {
                                $emailNotification = false;
                            }

                            if ($this->Notifications->save($notification)) {
                                $return = [
                                    'status'       => 'ok',
                                    'notification' => $emailNotification,
                                    'error'        => $error
                                ];
                            }
                            else {
                                $return = [
                                    'status'       => 'ok',
                                    'notification' => $emailNotification,
                                    'error'        => "Comment has been sent but can't send the notification"
                                ];
                            }
                        }
                        else {
                            $return = [
                                'status' => 'error',
                                'error'  => "Can't send comment"
                            ];
                        }
                    }
                    else {
                        $return = [
                            'status' => 'error',
                            'error'  => 'Email is not valid'
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => $errorValidate
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            if (Configure::read('debug')) {
                $return['data'] = $this->request->getData();
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
    }
    public function changeStatus()
    {
        if (($this->request->is('patch') || $this->request->is('post')) && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $postCommentChangeStatusValidator = new PostCommentChangeStatusValidator();
                $errorValidate                    = $postCommentChangeStatusValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $checkExist = $this->Comments->find()->where(['id' => $this->request->getData('id')]);
                    if ($checkExist->count() == 1) {
                        $comment = $this->Comments->get($this->request->getData('id'));
                        $this->Comments->patchEntity($comment, $this->request->getData());

                        if ($this->Comments->save($comment)) {
                            $recordId = $comment->id;

                            $comment  = $this->Comments->get($recordId);
                            if ($comment->status == '0') {
                                $status = 'unpublish';
                            }
                            elseif ($comment->status == '1') {
                                $status = 'publish';
                            }

                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */

                            $options = [
                                'title'    => '(API) Change Status of a Comment',
                                'detail'   => ' change comment status from '.$comment->name.' to '.$status.'.',
                                'admin_id' => $this->Auth->user('id')
                            ];
        
                            $saveActivity   = $this->Histories->saveActivity($options);
        
                            if ($saveActivity == true) {
                                $activity = true;
                            }
                            else {
                                $activity = false;
                            }

                            $return = [
                                'status'   => 'ok',
                                'activity' => $activity,
                                'error'    => $error
                            ];
                        }
                        else {
                            $return = [
                                'status' => 'error',
                                'error'  => "Can't change status"
                            ];
                        }
                    }
                    else {
                        $return = [
                            'status' => 'error',
                            'error'  => "Post comment not found"
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => $errorValidate
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            if (Configure::read('debug')) {
                $return['data'] = $this->request->getData();
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
	        throw new NotFoundException(__('Page not found'));
        }
    }
    public function delete()
    {
        if ($this->request->is('delete') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $postCommentDeleteValidator = new PostCommentDeleteValidator();
                $errorValidate              = $postCommentDeleteValidator->validate()->errors($this->request->getData());
                if (empty($errorValidate)) {
                    $checkExist = $this->Comments->find()->where(['id' => $this->request->getData('id')]);
                    if ($checkExist->count() == 1) {
                        $comment = $this->Comments->get($this->request->getData('id'));
                        $name    = $comment->name;
                        $email   = $comment->email;

                        $result = $this->Comments->delete($comment);

                        if ($result) {
                            /**
                             * Delete all comment reply under master comment
                             */
                            
                            $commentReplies = $this->Comments->find()->where(['reply' => $this->request->getData('id')]);
                            if ($commentReplies->count() > 0) {
                                foreach ($commentReplies as $reply) {
                                    $commentReply = $this->Comments->get($reply->id);
                                    $deleteReply  = $this->Comments->delete($commentReply);
                                }
                            }

                            /**
                             * Save user activity to histories table
                             * array $options => title, detail, admin_id
                             */
                            
                            $options = [
                                'title'    => '(API) Deletion of a Comment',
                                'detail'   => ' delete a comment from '.$name.'('.$email.').',
                                'admin_id' => $this->Auth->user('id')
                            ];

                            $saveActivity   = $this->Histories->saveActivity($options);

                            if ($saveActivity == true) {
                                $activity = true;
                            }
                            else {
                                $activity = false;
                            }

                            $return = [
                                'status'   => 'ok',
                                'activity' => $activity,
                                'error'    => $error
                            ];
                        }
                        else {
                            $return = [
                                'status' => 'error',
                                'error'  => "Can't delete data"
                            ];
                        }
                    }
                    else {
                        $return = [
                            'status' => 'error',
                            'error'  => "Post comment not found"
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => $errorValidate
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid access key'
                ];
            }

            if (Configure::read('debug')) {
                $return['data'] = $this->request->getData();
            }

            $json = json_encode($return, JSON_PRETTY_PRINT);

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
        }
        else {
	        throw new NotFoundException(__('Page not found'));
        }
    }
}