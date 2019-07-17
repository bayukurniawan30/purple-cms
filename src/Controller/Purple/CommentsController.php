<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\CommentReplyForm;
use App\Form\Purple\CommentStatusForm;
use App\Form\Purple\CommentDeleteForm;
use App\Form\Purple\SearchForm;
use Cake\Utility\Text;
use Cake\I18n\Time;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;

class CommentsController extends AppController
{
	public $commentsLimit = 10;

	public function beforeFilter(Event $event)
	{
	    parent::beforeFilter($event);
	    $purpleGlobal = new PurpleProjectGlobal();
		$databaseInfo   = $purpleGlobal->databaseInfo();
		if ($databaseInfo == 'default') {
			return $this->redirect(
	            ['prefix' => false, 'controller' => 'Setup', 'action' => 'index']
	        );
		}
	}
	public function initialize()
	{
		parent::initialize();
        $this->loadComponent('RequestHandler');
		$session = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');
		$sessionPassword = $session->read('Admin.password');

		if ($this->request->getEnv('HTTP_HOST') != $sessionHost || !$session->check('Admin.id')) {
			return $this->redirect(
	            ['controller' => 'Authenticate', 'action' => 'login']
	        );
		}
		else {
	    	$this->viewBuilder()->setLayout('dashboard');
	    	$this->loadModel('Admins');
			$this->loadModel('Blogs');
			$this->loadModel('Settings');
			$this->loadModel('Histories');

            if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
                $cakeDebug = 'on';
            } 
            else {
                $cakeDebug = 'off';
            }

			$queryAdmin   = $this->Admins->find()->where(['id' => $sessionID, 'password' => $sessionPassword])->limit(1);
            $queryFavicon = $this->Settings->find()->where(['name' => 'favicon'])->first();

			$rowCount = $queryAdmin->count();
			if ($rowCount > 0) {
				$adminData = $queryAdmin->first();

				$dashboardSearch = new SearchForm();
				
				// Plugins List
				$purplePlugins 	= new PurpleProjectPlugins();
				$plugins		= $purplePlugins->purplePlugins();
	        	$this->set('plugins', $plugins);

				$data = [
					'sessionHost'       => $sessionHost,
					'sessionID'         => $sessionID,
					'sessionPassword'   => $sessionPassword,
                    'cakeDebug'         => $cakeDebug,
					'adminName' 	    => ucwords($adminData->display_name),
					'adminLevel' 	    => $adminData->level,
					'adminEmail' 	    => $adminData->email,
					'adminPhoto' 	    => $adminData->photo,
                    'greeting'          => '',
					'dashboardSearch'   => $dashboardSearch,
					'title'             => 'Comment | Purple CMS',
					'pageTitle'         => 'Comment',
					'pageTitleIcon'     => 'mdi-comment-multiple-outline',
					'pageBreadcrumb'    => 'Comment',
                    'appearanceFavicon' => $queryFavicon
		    	];
	        	$this->set($data);
			}
			else {
				return $this->redirect(
		            ['controller' => 'Authenticate', 'action' => 'login']
		        );
			}
	    }
	}
	public function index($blogid, $id = 1)
	{
		$commentStatus = new CommentStatusForm();
		$commentDelete = new CommentDeleteForm();

        $comments = $this->Comments->find('all')->contain('Admins')->where(['Comments.blog_id' => $blogid, 'Comments.admin_id IS' => NULL])->order(['Comments.id' => 'DESC']);

		$data = [
			'commentStatus' => $commentStatus,
			'commentDelete' => $commentDelete,
			'commentsTotal' => $comments->count(),
			'commentsLimit' => $this->commentsLimit
		];

		$this->paginate = [
			'limit' => $this->commentsLimit,
			'page'  => $id
		];
		$commentsList = $this->paginate($comments);
	    $this->set('comments', $commentsList);
        // $this->set(compact('comments'));
		$this->set($data);
	}
	public function detail($blogid, $id)
	{
		$session   = $this->getRequest()->getSession();
        $sessionID = $session->read('Admin.id');
        
		$commentReply  = new CommentReplyForm();
		$commentStatus = new CommentStatusForm();
		$commentDelete = new CommentDeleteForm();
		$commentDeleteReply = new CommentDeleteForm();

        $comments = $this->Comments->find('all')->contain('Admins')->where(['Comments.blog_id' => $blogid, 'Comments.id' => $id]);
        if ($comments->count() == 1) {
        	$comment = $comments->first();
	        $replies = $this->Comments->find('all')->contain('Admins')->where(['Comments.blog_id' => $blogid, 'Comments.reply' => $id])->order(['Comments.created' => 'ASC']);
	        $this->set(compact('replies'));

	        // Update comment to read
	        $getComment = $this->Comments->get($comment->id);
	        $getComment->is_read = '1';

			if ($this->Comments->save($getComment)) {
				/**
				 * Save user activity to histories table
				 * array $options => title, detail, admin_id
				 */

				$options = [
					'title'    => 'Read Post Comment',
					'detail'   => ' read comment from '.$getComment->name.'.',
					'admin_id' => $sessionID
				];

                $saveActivity   = $this->Histories->saveActivity($options);
			}
        }
        else {
	        throw new NotFoundException(__('Page not found'));
        }

		$data = [
			'pageBreadcrumb'     => 'Comment::Detail',
			'commentReply'       => $commentReply,
			'commentStatus'      => $commentStatus,
			'commentDelete'      => $commentDelete,
			'commentDeleteReply' => $commentDeleteReply,
			'comment'            => $comment
		];

		$this->set($data);
	}
	public function ajaxChangeStatus()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$commentStatus = new CommentStatusForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($commentStatus->execute($this->request->getData())) {
				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $comment = $this->Comments->get($this->request->getData('id'));

				$this->Comments->patchEntity($comment, $this->request->getData());

				if ($this->Comments->save($comment)) {
					$record_id = $comment->id;

					$comment  = $this->Comments->get($record_id);
					if ($comment->status == '0') {
						$status = 'unpublish';
					}
					elseif ($comment->status == '1') {
						$status = 'publish';
					}

					/**
					 * Send email to author and tell his/her comment is published 
					 */

					/**
					 * Save user activity to histories table
					 * array $options => title, detail, admin_id
					 */

					$options = [
						'title'    => 'Change Status of a Comment',
						'detail'   => ' change comment status from '.$comment->name.' to '.$status.'.',
						'admin_id' => $sessionID
					];

                    $saveActivity   = $this->Histories->saveActivity($options);

					if ($saveActivity == true) {
	                    $json = json_encode(['status' => 'ok', 'activity' => true]);
	                }
	                else {
	                    $json = json_encode(['status' => 'ok', 'activity' => false]);
	                }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
                }
			}
			else {
				$errors = $commentStatus->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
			}

			$this->set(['json' => $json]);
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxReply()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$commentReply = new CommentReplyForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
			if ($commentReply->execute($this->request->getData())) {

				$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $comment = $this->Comments->newEntity();
                $comment = $this->Comments->patchEntity($comment, $this->request->getData());
                $comment->status   = '1';
                $comment->admin_id = $sessionID;
			
                if ($this->Comments->save($comment)) {
					$comment = $this->Comments->get($this->request->getData('reply'));
					$blog    = $this->Blogs->get($this->request->getData('blog_id'));

					/**
					 * Send email to author and tell his/her comment is replied 
					 */
					
					/**
					 * Save user activity to histories table
					 * array $options => title, detail, admin_id
					 */

					$options = [
						'title'    => 'Reply Comment from '.$comment->name,
						'detail'   => ' reply comment from '.$comment->name.' in '.$blog->title.'.',
						'admin_id' => $sessionID
					];

                    $saveActivity   = $this->Histories->saveActivity($options);

					if ($saveActivity == true) {
	                    $json = json_encode(['status' => 'ok', 'activity' => true]);
	                }
	                else {
	                    $json = json_encode(['status' => 'ok', 'activity' => false]);
	                }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
                }
			}
			else {
				$errors = $commentReply->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
			}

			$this->set(['json' => $json]);
		}
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxDelete()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		$commentDelete = new CommentDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($commentDelete->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
                
				$commentType = $this->request->getData('type');
				$comment     = $this->Comments->get($this->request->getData('id'));
				$name        = $comment->name;
				$email       = $comment->email;
				
				$result      = $this->Comments->delete($comment);

                if ($result) {
                	/**
                	 * Delete all comment reply under master comment
                	 */
                	
                	if ($commentType == 'master') {
                		$commentReplies = $this->Comments->find()->where(['reply' => $this->request->getData('id')]);
                		if ($commentReplies->count() > 0) {
                			foreach ($commentReplies as $reply) {
                				$commentReply = $this->Comments->get($reply->id);
								$deleteReply  = $this->Comments->delete($commentReply);
                			}
                		}
                	}

                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Deletion of a Comment',
                        'detail'   => ' delete a comment from '.$name.'('.$email.').',
                        'admin_id' => $sessionID
                    ];

                    $saveActivity   = $this->Histories->saveActivity($options);

                    if ($saveActivity == true) {
                        $json = json_encode(['status' => 'ok', 'activity' => true]);
                    }
                    else {
                        $json = json_encode(['status' => 'ok', 'activity' => false]);
                    }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $commentDelete->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
}