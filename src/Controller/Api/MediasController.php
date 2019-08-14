<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\Utility\Text;
use Cake\Filesystem\File;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Http\ServerRequest;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSeo;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use Carbon\Carbon;
use Bulletproof;
use Gregwar\Image\Image;

class MediasController extends AppController
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

        // Allow GET method
        $this->Auth->allow(['view', 'detail']);

        $this->loadModel('Medias');
        $this->loadModel('MediaVideos');
        $this->loadModel('MediaDocs');
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
    public function view($type)
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            // Check if paging and limit query string is exist
            $paging = $this->request->getQuery('paging');
            $limit  = $this->request->getQuery('limit');
            if ($paging !== NULL && $limit !== NULL) {
                $this->loadComponent('Paginator');
            }

            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $type   = trim($type);

            $availableType = ['images', 'videos', 'documents'];
            if (in_array($type, $availableType)) {
                if ($type == 'images') {
                    $mediasModel = 'Medias';
                    $loadModel   = $this->Medias;
                }
                elseif ($type == 'videos') {
                    $mediasModel = 'MediaVideos';
                    $loadModel   = $this->MediaVideos;
                }
                elseif ($type == 'documents') {
                    $mediasModel = 'MediaDocs';
                    $loadModel   = $this->MediaDocs;
                }

                $apiAccessKey = $this->Settings->settingsApiAccessKey();

                $error = NULL;

                if ($apiAccessKey == $apiKey) {
                    // Query string for additional condition
                    $orderBy = $this->request->getQuery('order_by');
                    $order   = $this->request->getQuery('order');
                    if ($order !== NULL && $orderBy !== NULL) {
                        /**
                         * Order By : title, created
                         * Order : ASC, DESC
                         */
                        $availableOrderBy = ['name', 'title', 'created', 'size'];
                        $availableOrder   = ['asc', 'desc'];
                        
                        if (in_array($orderBy, $availableOrderBy) && in_array($order, $availableOrder)) {
                            $orderQuery = [$mediasModel . '.' . $orderBy => strtoupper($order)];
                        }
                        else {
                            $orderQuery = [$mediasModel . '.created' => 'DESC'];
                            $error      = "Invalid query string. Please read the documentation for available query string.";
                        }
                    }
                    else {
                        $orderQuery = [$mediasModel . '.created' => 'DESC'];
                    }
                    $medias = $loadModel->find('all', [
                        'order' => $orderQuery])->contain('Admins', function (Query $q) {
                            return $q
                                ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                        });

                    if ($medias->count() > 0) {
                        $return = [
                            'status' => 'ok',
                            'type'   => $type,
                            'total'  => $medias->count(),
                            'error'  => $error
                        ];

                        if ($paging !== NULL && $limit !== NULL && filter_var($paging, FILTER_VALIDATE_INT) && filter_var($limit, FILTER_VALIDATE_INT)) {
                            $this->paginate = [
                                'limit' => $limit,
                                'page'  => $paging
                            ];
                            $mediasList = $this->paginate($medias);
                            $return['medias'] = $mediasList;
                            $return['page']   = $paging;
                            $return['limit']  = $limit;
                        }
                        else {
                            $return['medias'] = $medias;
                        }
                    }
                    else {
                        $return = [
                            'status' => 'ok',
                            'type'   => $type,
                            'total'  => 0,
                            'medias' => NULL,
                            'error'  => $error
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => 'Invalid access key'
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid media type. Please read the documentation for available media type.'
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
    public function detail($type, $id)
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $type   = trim($type);
            $id     = trim($id);

            $availableType = ['images', 'videos', 'documents'];
            if (in_array($type, $availableType)) {
                if ($type == 'images') {
                    $mediasModel = 'Medias';
                    $loadModel   = $this->Medias;
                }
                elseif ($type == 'videos') {
                    $mediasModel = 'MediaVideos';
                    $loadModel   = $this->MediaVideos;
                }
                elseif ($type == 'documents') {
                    $mediasModel = 'MediaDocs';
                    $loadModel   = $this->MediaDocs;
                }

                $apiAccessKey = $this->Settings->settingsApiAccessKey();

                $error = NULL;

                if ($apiAccessKey == $apiKey) {
                    $medias = $loadModel->find('all')->contain('Admins', function (Query $q) {
                            return $q
                                ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                        })
                        ->where([$mediasModel . '.id' => $id])->limit(1);

                    if ($medias->count() > 0) {
                        $media = $medias->first();

                        $return = [
                            'status' => 'ok',
                            'type'   => $type,
                            'media'  => $media,
                            'error'  => $error
                        ];
                    }
                    else {
                        $return = [
                            'status' => 'ok',
                            'type'   => $type,
                            'media'  => NULL,
                            'error'  => $error
                        ];
                    }
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => 'Invalid access key'
                    ];
                }
            }
            else {
                $return = [
                    'status' => 'error',
                    'error'  => 'Invalid media type. Please read the documentation for available media type.'
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
    public function uploadImages($file, $adminId)
	{
		$this->viewBuilder()->enableAutoLayout(false);

        if (!empty($file)) {
            $purpleSettings = new PurpleProjectSettings();
            $timezone       = $purpleSettings->timezone();
            $date           = Carbon::now($timezone);

            $uploadPath     = TMP . 'uploads' . DS . 'images' . DS;
            $fileName       = $file['name'];

            $image = new Bulletproof\Image($file);
            $newName         = Text::slug($fileName, ['preserve' => '.']);
            $explodeNewName  = explode(".", $newName);
            $fileExtension   = end($explodeNewName);
            $fileOnlyName    = str_replace('.'.$fileExtension, '', $newName);
            $dateSlug        = Text::slug($date);
            $generatedName   = $fileOnlyName . '_' . $dateSlug . '.' . $fileExtension;

            $image->setName($generatedName)
                    ->setMime(array('jpeg', 'jpg', 'png'))
                    ->setSize(100, 3145728)
                    ->setLocation($uploadPath);
            if ($image->upload()) {
                $fullSizeImage              = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS;
                $uploadedThumbnailSquare    = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '300x300' . DS;
                $uploadedThumbnailLandscape = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '480x270' . DS;
                if (file_exists($image->getFullPath())) {
                    $readImageFile   = new File($image->getFullPath());
                    $imageSize       = $readImageFile->size();
                    
                    $fullSize           = Image::open($image->getFullPath())->save($fullSizeImage . $generatedName, 'guess', 90);
                    $thumbnailSquare    = Image::open($image->getFullPath())
                                        ->zoomCrop(300, 300)
                                        ->save($uploadedThumbnailSquare . $generatedName, 'guess', 90);
                    $thumbnailLandscape = Image::open($image->getFullPath())
                                        ->zoomCrop(480, 270)
                                        ->save($uploadedThumbnailLandscape . $generatedName, 'guess', 90);

                    $media = $this->Medias->newEntity();

                    $media->name     = $generatedName;
                    $media->title    = $fileOnlyName;
                    $media->size     = $imageSize;
                    $media->admin_id = $adminId;

                    if ($this->Medias->save($media)) {
                        $readImageFile   = new File($image->getFullPath());
                        $deleteImage     = $readImageFile->delete();
                        
                        return $generatedName;
                    }
                    else {
                        return false;
                    }
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }
    public function uploadDocuments($file, $adminId)
    {
        $this->viewBuilder()->enableAutoLayout(false);
        
        $purpleSettings = new PurpleProjectSettings();
        $timezone       = $purpleSettings->timezone();

        $date          = Carbon::now($timezone);
        $fileName      = $file['name'];
        $newName       = Text::slug($fileName, ['preserve' => '.']);
        $explodeNewName  = explode(".", $newName);
        $fileExtension   = end($explodeNewName);
        $acceptedExt   = ['txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'md', 'pdf'];
        if (in_array($fileExtension, $acceptedExt)) {
            $fileOnlyName    = str_replace('.'.$fileExtension, '', $newName);
            $dateSlug        = Text::slug($date);
            $generatedName   = $fileOnlyName . '_' . $dateSlug . '.' . $fileExtension;
            $uploadFile      = WWW_ROOT . 'uploads' . DS .'documents' . DS . $generatedName;
            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                $readDocumentFile = new File($uploadFile);
                $fileSize         = $readDocumentFile->size();

                $doc       = $this->MediaDocs->newEntity();

                $doc->name     = $generatedName;
                $doc->title    = $fileOnlyName;
                $doc->size     = $fileSize;
                $doc->admin_id = $adminId;

                if ($this->MediaDocs->save($doc)) {
                    return $generatedName;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }
    public function uploadVideos($file, $adminId)
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $purpleSettings = new PurpleProjectSettings();
        $timezone       = $purpleSettings->timezone();

        $date          = Carbon::now($timezone);
        $fileName      = $file['name'];
        $newName       = Text::slug($fileName, ['preserve' => '.']);
        $explodeNewName  = explode(".", $newName);
        $fileExtension   = end($explodeNewName);
        $acceptedExt   = ['mp4', 'm4v', 'ogv', 'webm'];
        if (in_array($fileExtension, $acceptedExt)) {
            $fileOnlyName    = str_replace('.'.$fileExtension, '', $newName);
            $dateSlug        = Text::slug($date);
            $generatedName   = $fileOnlyName . '_' . $dateSlug . '.' . $fileExtension;
            $uploadFile      = WWW_ROOT . 'uploads' . DS .'videos' . DS . $generatedName;
            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                $readVideoFile = new File($uploadFile);
                $fileSize      = $readVideoFile->size();

                $video           = $this->MediaVideos->newEntity();

                $video->name     = $generatedName;
                $video->title    = $fileOnlyName;
                $video->size     = $fileSize;
                $video->admin_id = $adminId;

                if ($this->MediaVideos->save($video)) {
                    return $generatedName;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }
        }
        else {
            return false;
        }
    }
    public function add($type)
    {
        if ($this->request->is('post') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                if ($type == 'images') {
                    $upload = $this->uploadImages($this->request->getData('file'), $this->Auth->user('id'));
                    $mediasName  = 'Images';
                    $mediasModel = 'Medias';
                    $loadModel   = $this->Medias;
                }
                elseif ($type == 'videos') {
                    $upload = $this->uploadVideos($this->request->getData('file'), $this->Auth->user('id'));
                    $mediasModel = 'Videos';
                    $mediasModel = 'MediaVideos';
                    $loadModel   = $this->MediaVideos;
                }
                elseif ($type == 'documents') {
                    $upload = $this->uploadDocuments($this->request->getData('file'), $this->Auth->user('id'));
                    $mediasModel = 'Documents';
                    $mediasModel = 'MediaDocs';
                    $loadModel   = $this->MediaDocs;
                }

                if ($upload) {
                    $medias = $loadModel->find('all')->contain('Admins', function (Query $q) {
                        return $q
                            ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                    })
                    ->where([$mediasModel . '.name' => $upload])->limit(1);
                    $media  = $medias->first();

                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */

                    $options = [
                        'title'    => '(API) Addition of New Media ' . $mediasModel,
                        'detail'   => ' add '.$upload.' to media ' . $type .'.',
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
                        'type'     => $type,
                        'media'    => $media,
                        'activity' => $activity,
                        'error'    => $error
                    ];
                }
                else {
                    $return = [
                        'status' => 'error',
                        'error'  => "Can't save data"
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
    public function update($type)
    {
        
    }
    public function delete($type)
    {
        if ($this->request->is('delete') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                if ($type == 'images') {
                    $mediasName   = 'Images';
                    $mediasModel  = 'Medias';
                    $loadModel    = $this->Medias;
                }
                elseif ($type == 'videos') {
                    $mediasModel  = 'Videos';
                    $mediasModel  = 'MediaVideos';
                    $loadModel    = $this->MediaVideos;
                }
                elseif ($type == 'documents') {
                    $mediasModel  = 'Documents';
                    $mediasModel  = 'MediaDocs';
                    $loadModel    = $this->MediaDocs;
                }
                $checkExist = $loadModel->find()->where(['id' => $this->request->getData('id')]);
                if ($checkExist->count() == 1) {
                    $media = $loadModel->get($this->request->getData('id'));
                    $name  = $media->name;

                    if ($type == 'images') {
                        $fullSizeImage              = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS . $name;
                        $uploadedThumbnailSquare    = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '300x300' . DS . $name;
                        $uploadedThumbnailLandscape = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '480x270' . DS . $name;
                        $readFile           = new File($fullSizeImage);
                        $readImageSquare    = new File($uploadedThumbnailSquare);
                        $readImageLandscape = new File($uploadedThumbnailLandscape);

                        $readImageSquare->delete();
                        $readImageLandscape->delete();
                    }
                    else {
                        $fullPath = WWW_ROOT . 'uploads' . DS . strtolower($mediasName) . DS . $filePath;
                        $readFile = new File($fullPath);
                    }

                    $result = $loadModel->delete($media);
                    if ($result && $readFile->delete()) {
                        /**
                         * Save user activity to histories table
                         * array $options => title, detail, admin_id
                         */
                        
                        $options = [
                            'title'    => '(API) Deletion of a Media ' . $mediasModel,
                            'detail'   => ' delete '.$name.' from media ' . $type . '.',
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
                        'error'  => "Post not found."
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