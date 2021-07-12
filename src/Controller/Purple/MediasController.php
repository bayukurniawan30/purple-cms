<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Text;
use Cake\Filesystem\File;
use Cake\Cache\Cache;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\MediaImageModalForm;
use App\Form\Purple\MediaImageDeleteForm;
use App\Form\Purple\MediaDocumentModalForm;
use App\Form\Purple\MediaDocumentDeleteForm;
use App\Form\Purple\MediaVideoModalForm;
use App\Form\Purple\MediaVideoDeleteForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;
use Carbon\Carbon;
use Bulletproof;
use Gregwar\Image\Image;
use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

class MediasController extends AppController
{
	public $imagesLimit = 30;

	public function beforeFilter(Event $event)
	{
		parent::beforeFilter($event);
		
		/**
		 * Check if Purple CMS has been setup or not
		 * If not, redirect to Purple Setup
		 */
	    $purpleGlobal = new PurpleProjectGlobal();
		$databaseInfo   = $purpleGlobal->databaseInfo();
		if ($databaseInfo == 'default') {
			return $this->redirect(
	            ['prefix' => false, 'controller' => 'Setup', 'action' => 'index']
	        );
		}

		/**
		 * Check if user is signed in
		 * If not, redirect to login page
		 */
		$session     = $this->getRequest()->getSession();
		$sessionHost = $session->read('Admin.host');

		if ($this->request->getEnv('HTTP_HOST') != $sessionHost || !$session->check('Admin.id')) {
			return $this->redirect(
				['_name' => 'adminLogin']
			);
		}
	}
	public function initialize()
	{
		parent::initialize();

		// Get Admin Session data
		$session = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');
		$sessionPassword = $session->read('Admin.password');

		$this->loadComponent('Paginator');

		$this->viewBuilder()->setLayout('dashboard');
		$this->loadModel('Admins');
		$this->loadModel('MediaDocs');
		$this->loadModel('MediaVideos');
		$this->loadModel('Settings');

		if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
			$cakeDebug = 'on';
		} 
		else {
			$cakeDebug = 'off';
		}

		$queryAdmin      = $this->Admins->signedInUser($sessionID, $sessionPassword);
		$queryFavicon    = $this->Settings->fetch('favicon');
		$queryDateFormat = $this->Settings->fetch('dateformat');
		$queryTimeFormat = $this->Settings->fetch('timeformat');

		$rowCount = $queryAdmin->count();
		if ($rowCount > 0) {
			$adminData = $queryAdmin->first();
			
			$dashboardSearch = new SearchForm();

			$purpleGlobal = new PurpleProjectGlobal();
			$protocol     = $purpleGlobal->protocol();
			
			// Plugins List
			$purplePlugins 	= new PurpleProjectPlugins();
			$plugins		= $purplePlugins->purplePlugins();
			$this->set('plugins', $plugins);
			
			$data = [
				'sessionHost'        => $sessionHost,
				'sessionID'          => $sessionID,
				'sessionPassword'    => $sessionPassword,
				'cakeDebug'          => $cakeDebug,
				'adminName'          => ucwords($adminData->display_name),
				'adminLevel'         => $adminData->level,
				'adminEmail'         => $adminData->email,
				'adminPhoto'         => $adminData->photo,
				'greeting'           => '',
				'dashboardSearch'    => $dashboardSearch,
				'title'              => 'Medias | Purple CMS',
				'pageTitle'          => 'Medias',
				'pageTitleIcon'      => 'mdi-folder-image',
				'pageBreadcrumb'     => 'Media',
				'appearanceFavicon'  => $queryFavicon,
				'settingsDateFormat' => $queryDateFormat->value,
				'settingsTimeFormat' => $queryTimeFormat->value,
				'protocol' 			 => $protocol
			];
			$this->set($data);
		}
		else {
			return $this->redirect(
				['controller' => 'Authenticate', 'action' => 'login']
			);
		}
	}
	public function documents()
	{
        $mediaDocumentModal  = new MediaDocumentModalForm();
        $mediaDocumentDelete = new MediaDocumentDeleteForm();

		$data = [
			'pageTitle'           => 'Documents',
			'pageBreadcrumb'      => 'Medias::Documents',
            'mediaDocumentModal'  => $mediaDocumentModal,
            'mediaDocumentDelete' => $mediaDocumentDelete
		];

        $docs = $this->MediaDocs->find('all', ['contain' => ['Admins']])->order(['MediaDocs.id' => 'DESC']);
    	$this->set(compact('docs'));
        $this->set($data);
	}
	public function images($id = 1)
	{
		$mediaImageModal  = new MediaImageModalForm();
		$mediaImageDelete = new MediaImageDeleteForm();

		$medias = $this->Medias->find('all', [
            'order' => ['Medias.id' => 'DESC']])->contain('Admins');

		$data = [
			'pageTitle'         => 'Images',
			'pageBreadcrumb'    => 'Medias::Images',
			'mediaImageModal'   => $mediaImageModal,
            'mediaImageDelete'  => $mediaImageDelete,
            'mediaImageTotal'   => $medias->count(),
            'mediaImageLimit'   => $this->imagesLimit
		];

		$this->paginate = [
			'limit' => $this->imagesLimit,
			'page'  => $id
		];
		$mediasList = $this->paginate($medias);
	    $this->set('medias', $mediasList);
		// $this->set(compact('medias'));
		
		if ($this->request->getQuery('id') !== NULL) {
			$detail = $this->Medias->find('all', [
				'order' => ['Medias.id' => 'DESC']])->contain('Admins')->where(['Medias.id' => $this->request->getQuery('id')]);
			if ($detail->count() > 0) {
				$this->set('detail', $detail->first());
			}
			else {
				$this->set('detail', NULL);
			}
		}

    	$this->set($data);
	}
	public function videos()
	{
        $mediaVideoModal  = new MediaVideoModalForm();
        $mediaVideoDelete = new MediaVideoDeleteForm();

		$data = [
			'pageTitle'         => 'Videos',
			'pageBreadcrumb'    => 'Medias::Videos',
            'mediaVideoModal'   => $mediaVideoModal,
            'mediaVideoDelete'  => $mediaVideoDelete
		];

        $videos = $this->MediaVideos->find('all', ['contain' => ['Admins']])->order(['MediaVideos.id' => 'DESC']);
    	$this->set(compact('videos'));
    	$this->set($data);
	}
	public function ajaxBrowseImages()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		if ($this->request->is('ajax') || $this->request->is('post')) {
			$medias = $this->Medias->find('all', [
	            'order' => ['Medias.id' => 'DESC']])->contain('Admins');

			if ($this->request->getData('multiple') == 'true') {
				$multiSelect = true;
			}
			elseif ($this->request->getData('multiple') == 'false') {
				$multiSelect = false;
			}

			if (strpos($this->request->getData('images'), ',') !== false) {
				$imageArray = explode(',', $this->request->getData('images')); 
			}
			else {
				$imageArray = [$this->request->getData('images')];
			}
			
			$data = [
				'mediaImageTotal' => $medias->count(),
				'mediaImageLimit' => $this->request->getData('limit'),
				'multiSelect'     => $multiSelect,
				'imageArray'      => $imageArray
			];

			$this->paginate = [
				'limit' => $this->request->getData('limit'),
				'page'  => $this->request->getData('page')
			];
			$mediasList = $this->paginate($medias);
		    $this->set('medias', $mediasList);
	        // $this->set(compact('medias'));
	    	$this->set($data);
	    }
	    else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxUploadImages()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		if ($this->request->is('post')) {
			if (!empty($this->request->getData('file'))) {
				$session   = $this->getRequest()->getSession();
				$sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

			    $purpleSettings = new PurpleProjectSettings();
			    $timezone       = $purpleSettings->timezone();
				$date                   = Carbon::now($timezone);

				$uploadPath             = TMP . 'uploads' . DS . 'images' . DS;
				$fileName               = $this->request->getData('file.name');

				$image = new Bulletproof\Image($this->request->getData());
				if ($image["file"]){
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
							$fullSize 			= Image::open($image->getFullPath())->save($fullSizeImage . $generatedName, 'guess', 90);
							$thumbnailSquare 	= Image::open($image->getFullPath())
											    	->zoomCrop(300, 300)
											    	->save($uploadedThumbnailSquare . $generatedName, 'guess', 90);
						    $thumbnailLandscape = Image::open($image->getFullPath())
											    	->zoomCrop(480, 270)
											    	->save($uploadedThumbnailLandscape . $generatedName, 'guess', 90);

							$media = $this->Medias->newEntity();

							$media->name     = $generatedName;
							$media->title    = $fileOnlyName;
							$media->size     = $imageSize;
							$media->admin_id = $sessionID;

							if ($this->Medias->save($media)) {
								$readImageFile   = new File($image->getFullPath());
								$deleteImage     = $readImageFile->delete();

								// Check for media storage
								$mediaStorage   = $this->Settings->fetch('mediastorage');

								// If media storage is Amazon AWS S3
								if ($mediaStorage->value == 'awss3') {
									$awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
									$awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
									$awsS3Region    = $this->Settings->fetch('awss3region');
									$awsS3Bucket    = $this->Settings->fetch('awss3bucket');

									$s3Client = new S3Client([
										'region'  => $awsS3Region->value,
										'version' => 'latest',
										'credentials' => [
											'key'      => $awsS3AccessKey->value,
											'secret'   => $awsS3SecretKey->value,
										]
									]);

									// Path
									$originalFilePath           = $fullSizeImage . $generatedName;
									$thumbnailSquareFilePath    = $uploadedThumbnailSquare . $generatedName;
									$thumbnailLandscapeFilePath = $uploadedThumbnailLandscape . $generatedName;

									// Key
									$originalKey           = 'images/original/' . basename($originalFilePath);
									$thumbnailSquareKey    = 'images/thumbnails/300x300/' . basename($thumbnailSquareFilePath);
									$thumbnailLandscapeKey = 'images/thumbnails/480x270/' . basename($thumbnailLandscapeFilePath);
									
									// Send original image
									try {
										$result = $s3Client->putObject([
											'Bucket'     => $awsS3Bucket->value,
											'Key'        => $originalKey,
											'SourceFile' => $originalFilePath,
											'ACL'        => 'public-read',
										]);

										$readImageFile = new File($fullSizeImage . $generatedName);
										$readImageFile->delete();
									} 
									catch (AwsException $e) {
										$this->log($e->getMessage(), 'debug');
									}

									// Send thumbnail 300x300 image
									try {
										$result = $s3Client->putObject([
											'Bucket'     => $awsS3Bucket->value,
											'Key'        => $thumbnailSquareKey,
											'SourceFile' => $thumbnailSquareFilePath,
											'ACL'        => 'public-read',
										]);

										$readImageSquare = new File($uploadedThumbnailSquare . $generatedName);
										$readImageSquare->delete();
									} 
									catch (AwsException $e) {
										$this->log($e->getMessage(), 'debug');
									}

									// Send thumbnail 480x270 image
									try {
										$result = $s3Client->putObject([
											'Bucket'     => $awsS3Bucket->value,
											'Key'        => $thumbnailLandscapeKey,
											'SourceFile' => $thumbnailLandscapeFilePath,
											'ACL'        => 'public-read',
										]);
										
										$readImageLandscape = new File($uploadedThumbnailLandscape . $generatedName);
										$readImageLandscape->delete();
									} 
									catch (AwsException $e) {
										$this->log($e->getMessage(), 'debug');
									}

									$path = $s3Client->getObjectUrl($awsS3Bucket->value, $originalKey);;
								}
								else {
									$baseUrl = Router::url([
										'_name' => 'home'
									], true);

									$path = $baseUrl . 'uploads/images/original/' . $generatedName;
								}

								// Tell system for new event
								$event = new Event('Model.Media.afterSave', $this, ['media' => $generatedName, 'admin' => $admin, 'type' => 'image', 'save' => 'new']);
								$this->getEventManager()->dispatch($event);

								$json = json_encode(['status' => 'ok', 'name' => $generatedName, 'path' => $path, 'size' => $imageSize, 'activity' => $event->getResult()]);
							}
							else {
								$json = json_encode(['status' => 'error', 'error' => "Can't save image file. Please try again."]);
							}
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't upload image file. Please try again."]);
						}
					}
					else {
	            		$json = json_encode(['status' => 'error', 'error' => $image->getError()]);
	            	}
				}
			}
            $this->set(['json' => $json]);
		}
		else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxImagesDetail()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		if ($this->request->is('ajax') || $this->request->is('post')) {
			$medias = $this->Medias->find('all', [
                'order' => ['Medias.id' => 'DESC']])->contain('Admins');

			$data = [
	            'mediaImageTotal'   => $medias->count(),
	            'mediaImageLimit'   => $this->imagesLimit
			];

			$this->paginate = [
				'limit' => $this->imagesLimit,
				'page'  => 1
			];
			$mediasList = $this->paginate($medias);
		    $this->set('medias', $mediasList);
	    	$this->set($data);
	        // $this->set(compact('medias'));
			$this->render();
		}
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
    public function ajaxUpdateImages()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        $mediaImageModal  = new MediaImageModalForm();
		if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($mediaImageModal->execute($this->request->getData())) {
            	$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
                
                $media = $this->Medias->get($this->request->getData('id'));

                $media->title       = $this->request->getData('title');
                $media->description = $this->request->getData('description');

                if ($this->Medias->save($media)) {
					// Tell system for new event
					$event = new Event('Model.Media.afterSave', $this, ['media' => $media->name, 'admin' => $admin, 'type' => 'image', 'save' => 'update']);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't update data. Please try again."]);
                }
            }
			else {
            	$errors = $mediaImageModal->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxDeleteImages()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        $mediaImageDelete = new MediaImageDeleteForm();
		if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($mediaImageDelete->execute($this->request->getData())) {
            	$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                $media      = $this->Medias->get($this->request->getData('id'));
                $imagePath  = $media->name;

                $fullSizeImage              = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS . $imagePath;
                $uploadedThumbnailSquare    = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '300x300' . DS . $imagePath;
                $uploadedThumbnailLandscape = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '480x270' . DS . $imagePath;

                $readImageFile      = new File($fullSizeImage);
                $readImageSquare    = new File($uploadedThumbnailSquare);
                $readImageLandscape = new File($uploadedThumbnailLandscape);

                $entity = $this->Medias->get($this->request->getData('id'));
				$result = $this->Medias->delete($entity);
				
				if ($result) {
					// Delete color extractor cache
					$baseName = pathinfo($imagePath, PATHINFO_FILENAME);
					$colorExtractorCache = Cache::read('color_extract_' . $baseName);
					if ($colorExtractorCache !== false) {
						Cache::delete('color_extract_' . $baseName);
					}
					
					// Check for media storage
					$mediaStorage   = $this->Settings->fetch('mediastorage');

					// If media storage is Amazon AWS S3
					if ($mediaStorage->value == 'awss3') {
						$deleteOriginal = false;
						$delete300x300  = false;
						$delete480x270  = false;

						$awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
						$awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
						$awsS3Region    = $this->Settings->fetch('awss3region');
						$awsS3Bucket    = $this->Settings->fetch('awss3bucket');

						$s3Client = new S3Client([
							'region'  => $awsS3Region->value,
							'version' => 'latest',
							'credentials' => [
								'key'      => $awsS3AccessKey->value,
								'secret'   => $awsS3SecretKey->value,
							]
						]);

						// Path
						$originalFilePath           = $fullSizeImage;
						$thumbnailSquareFilePath    = $uploadedThumbnailSquare;
						$thumbnailLandscapeFilePath = $uploadedThumbnailLandscape;

						// Key
						$originalKey           = 'images/original/' . basename($originalFilePath);
						$thumbnailSquareKey    = 'images/thumbnails/300x300/' . basename($thumbnailSquareFilePath);
						$thumbnailLandscapeKey = 'images/thumbnails/480x270/' . basename($thumbnailLandscapeFilePath);
						
						$imagePath = $s3Client->getObjectUrl($awsS3Bucket->value, $originalKey);

						// Delete object
						try {
							$result = $s3Client->deleteObject([
								'Bucket' => $awsS3Bucket->value,
								'Key'    => $originalKey
							]);

							$deleteOriginal = true;
						} 
						catch (AwsException $e) {
							$this->log($e->getMessage(), 'debug');
						}

						// Delete object
						try {
							$result = $s3Client->deleteObject([
								'Bucket' => $awsS3Bucket->value,
								'Key'    => $thumbnailSquareKey,
							]);

							$delete300x300 = true;
						} 
						catch (AwsException $e) {
							$this->log($e->getMessage(), 'debug');
						}

						// Delete object
						try {
							$result = $s3Client->deleteObject([
								'Bucket' => $awsS3Bucket->value,
								'Key'    => $thumbnailLandscapeKey,
							]);

							$delete480x270 = true;
						} 
						catch (AwsException $e) {
							$this->log($e->getMessage(), 'debug');
						}

						if ($deleteOriginal && $delete300x300 && $delete480x270) {
							// Tell system for new event
							$event = new Event('Model.Media.afterDelete', $this, ['media' => $imagePath, 'admin' => $admin, 'type' => 'image']);
							$this->getEventManager()->dispatch($event);
		
							$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
						}
					}
					else {
						if ($readImageFile->delete() && $readImageSquare->delete() && $readImageLandscape->delete()) {
							// Tell system for new event
							$event = new Event('Model.Media.afterDelete', $this, ['media' => $imagePath, 'admin' => $admin, 'type' => 'image']);
							$this->getEventManager()->dispatch($event);
		
							$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
						}
					}
				}
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $mediaImageDelete->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxGetImageColors()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		if ($this->request->is('ajax') || $this->request->is('post')) {
			$imageName = $this->request->getData('image');
            $this->set(['image' => $imageName]);
		}
		else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
    public function ajaxUploadDocuments()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		if ($this->request->is('post')) {
            $session   = $this->getRequest()->getSession();
            $sessionID = $session->read('Admin.id');
			$admin     = $this->Admins->get($sessionID);

            $purpleSettings = new PurpleProjectSettings();
            $timezone       = $purpleSettings->timezone();

            $date          = Carbon::now($timezone);
            $fileName      = $this->request->getData('file.name');
            $newName       = Text::slug($fileName, ['preserve' => '.']);
            $explodeNewName  = explode(".", $newName);
			$fileExtension   = end($explodeNewName);
            $acceptedExt   = ['txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'md', 'pdf'];
            if (in_array($fileExtension, $acceptedExt)) {
                $fileOnlyName    = str_replace('.'.$fileExtension, '', $newName);
                $dateSlug        = Text::slug($date);
                $generatedName   = $fileOnlyName . '_' . $dateSlug . '.' . $fileExtension;
                $uploadFile      = WWW_ROOT . 'uploads' . DS .'documents' . DS . $generatedName;
                if (move_uploaded_file($this->request->getData('file.tmp_name'), $uploadFile)) {
                    $readDocumentFile = new File($uploadFile);
                    $fileSize         = $readDocumentFile->size();

                    $doc       = $this->MediaDocs->newEntity();

                    $doc->name     = $generatedName;
                    $doc->title    = $fileOnlyName;
                    $doc->size     = $fileSize;
                    $doc->admin_id = $sessionID;

                    if ($this->MediaDocs->save($doc)) {
						// Check for media storage
						$mediaStorage   = $this->Settings->fetch('mediastorage');

						// If media storage is Amazon AWS S3
						if ($mediaStorage->value == 'awss3') {
							$awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
							$awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
							$awsS3Region    = $this->Settings->fetch('awss3region');
							$awsS3Bucket    = $this->Settings->fetch('awss3bucket');

							$s3Client = new S3Client([
								'region'  => $awsS3Region->value,
								'version' => 'latest',
								'credentials' => [
									'key'      => $awsS3AccessKey->value,
									'secret'   => $awsS3SecretKey->value,
								]
							]);

							// Path
							$filePath = $uploadFile;

							// Key
							$key = 'documents/' . basename($filePath);
							try {
								$result = $s3Client->putObject([
									'Bucket'     => $awsS3Bucket->value,
									'Key'        => $key,
									'SourceFile' => $filePath,
									'ACL'        => 'public-read',
								]);

								$readFile = new File($uploadFile);
								$readFile->delete();
							} 
							catch (AwsException $e) {
								$this->log($e->getMessage(), 'debug');
							}

							$path = $s3Client->getObjectUrl($awsS3Bucket->value, $key);;
						}
						else {
							$baseUrl = Router::url([
								'_name' => 'home'
							], true);

							$path = $baseUrl . 'uploads/documents/' . $generatedName;
						}

						// Tell system for new event
						$event = new Event('Model.Media.afterSave', $this, ['media' => $generatedName, 'admin' => $admin, 'type' => 'document', 'save' => 'new']);
						$this->getEventManager()->dispatch($event);

						$json = json_encode(['status' => 'ok', 'name' => $generatedName, 'path' => $path, 'size' => $fileSize, 'extension' => $fileExtension, 'activity' => $event->getResult()]);
                    }
                    else {
                        $json = json_encode(['status' => 'error', 'error' => "Can't save document file. Please try again."]);
                    }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't upload document file. Please try again."]);
                }
            }
            else {
                $json = json_encode(['status' => 'error', 'error' => "File extension is not allowed. Please try again with another file."]);
            }
            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxDocumentsDetail()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		if ($this->request->is('ajax') || $this->request->is('post')) {
            $docs = $this->MediaDocs->find('all', ['contain' => ['Admins']])->order(['MediaDocs.id' => 'DESC']);
            $this->set(compact('docs'));
			$this->render();
		}
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
    public function ajaxUpdateDocuments()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        $mediaDocumentModal  = new MediaDocumentModalForm();
		if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($mediaDocumentModal->execute($this->request->getData())) {
            	$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                $doc       = $this->MediaDocs->get($this->request->getData('id'));

                $doc->title       = $this->request->getData('title');
                $doc->description = $this->request->getData('description');

                if ($this->MediaDocs->save($doc)) {
					// Tell system for new event
					$event = new Event('Model.Media.afterSave', $this, ['media' => $doc->name, 'admin' => $admin, 'type' => 'document', 'save' => 'update']);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't update data. Please try again."]);
                }
            }
            else {
            	$errors = $mediaDocumentModal->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxDeleteDocuments()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        $mediaDocumentDelete = new MediaDocumentDeleteForm();
		if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($mediaDocumentDelete->execute($this->request->getData())) {
            	$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                $doc       = $this->MediaDocs->get($this->request->getData('id'));
                $filePath  = $doc->name;

                $fullPath  = WWW_ROOT . 'uploads' . DS .'documents' . DS . $filePath;

                $readDocumentFile = new File($fullPath);

                $query = $this->MediaDocs->query()
                    ->delete()
                    ->where(['id' => $this->request->getData('id')]);
				$result = $query->execute();
				
				if ($result) {
					// Check for media storage
					$mediaStorage   = $this->Settings->fetch('mediastorage');

					// If media storage is Amazon AWS S3
					if ($mediaStorage->value == 'awss3') {
						$deleteFile = false;

						$awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
						$awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
						$awsS3Region    = $this->Settings->fetch('awss3region');
						$awsS3Bucket    = $this->Settings->fetch('awss3bucket');

						$s3Client = new S3Client([
							'region'  => $awsS3Region->value,
							'version' => 'latest',
							'credentials' => [
								'key'      => $awsS3AccessKey->value,
								'secret'   => $awsS3SecretKey->value,
							]
						]);

						// Path
						$filePath = $fullPath;

						// Key
						$key = 'documents/' . basename($filePath);

						$filePath = $s3Client->getObjectUrl($awsS3Bucket->value, $key);
						
						// Delete object
						try {
							$result = $s3Client->deleteObject([
								'Bucket' => $awsS3Bucket->value,
								'Key'    => $key
							]);

							$deleteFile = true;
						} 
						catch (AwsException $e) {
							$this->log($e->getMessage(), 'debug');
						}

						if ($deleteFile) {
							// Tell system for new event
							$event = new Event('Model.Media.afterDelete', $this, ['media' => $filePath, 'admin' => $admin, 'type' => 'document']);
							$this->getEventManager()->dispatch($event);
		
							$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
						}
					}
					else {
						if ($readDocumentFile->delete()) {
							// Tell system for new event
							$event = new Event('Model.Media.afterDelete', $this, ['media' => $filePath, 'admin' => $admin, 'type' => 'document']);
							$this->getEventManager()->dispatch($event);
		
							$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
						}
					}
				}
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $mediaDocumentDelete->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxUploadVideos()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		if ($this->request->is('post')) {
            $session   = $this->getRequest()->getSession();
            $sessionID = $session->read('Admin.id');
			$admin     = $this->Admins->get($sessionID);

            $purpleSettings = new PurpleProjectSettings();
            $timezone       = $purpleSettings->timezone();

            $date          = Carbon::now($timezone);
            $fileName      = $this->request->getData('file.name');
            $newName       = Text::slug($fileName, ['preserve' => '.']);
            $explodeNewName  = explode(".", $newName);
			$fileExtension   = end($explodeNewName);
            $acceptedExt   = ['mp4', 'm4v', 'ogv', 'webm'];
            if (in_array($fileExtension, $acceptedExt)) {
                $fileOnlyName    = str_replace('.'.$fileExtension, '', $newName);
                $dateSlug        = Text::slug($date);
                $generatedName   = $fileOnlyName . '_' . $dateSlug . '.' . $fileExtension;
                $uploadFile      = WWW_ROOT . 'uploads' . DS .'videos' . DS . $generatedName;
                if (move_uploaded_file($this->request->getData('file.tmp_name'), $uploadFile)) {
                    $readVideoFile = new File($uploadFile);
                    $fileSize      = $readVideoFile->size();

                    $video       = $this->MediaVideos->newEntity();

                    $video->name     = $generatedName;
                    $video->title    = $fileOnlyName;
                    $video->size     = $fileSize;
                    $video->admin_id = $sessionID;

                    if ($this->MediaVideos->save($video)) {
						// Check for media storage
						$mediaStorage   = $this->Settings->fetch('mediastorage');

						// If media storage is Amazon AWS S3
						if ($mediaStorage->value == 'awss3') {
							$awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
							$awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
							$awsS3Region    = $this->Settings->fetch('awss3region');
							$awsS3Bucket    = $this->Settings->fetch('awss3bucket');

							$s3Client = new S3Client([
								'region'  => $awsS3Region->value,
								'version' => 'latest',
								'credentials' => [
									'key'      => $awsS3AccessKey->value,
									'secret'   => $awsS3SecretKey->value,
								]
							]);

							// Path
							$filePath = $uploadFile;

							// Key
							$key = 'videos/' . basename($filePath);
							try {
								$result = $s3Client->putObject([
									'Bucket'     => $awsS3Bucket->value,
									'Key'        => $key,
									'SourceFile' => $filePath,
									'ACL'        => 'public-read',
								]);

								$readFile = new File($uploadFile);
								$readFile->delete();
							} 
							catch (AwsException $e) {
								$this->log($e->getMessage(), 'debug');
							}

							$path = $s3Client->getObjectUrl($awsS3Bucket->value, $key);;
						}
						else {
							$baseUrl = Router::url([
								'_name' => 'home'
							], true);

							$path = $baseUrl . 'uploads/videos/' . $generatedName;
						}

						// Tell system for new event
						$event = new Event('Model.Media.afterSave', $this, ['media' => $generatedName, 'admin' => $admin, 'type' => 'video', 'save' => 'new']);
						$this->getEventManager()->dispatch($event);

						$json = json_encode(['status' => 'ok', 'name' => $generatedName, 'path' => $path, 'size' => $fileSize, 'extension' => $fileExtension, 'activity' => $event->getResult()]);
                    }
                    else {
                        $json = json_encode(['status' => 'error', 'error' => "Can't save video file. Please try again."]);
                    }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't upload video file. Please try again."]);
                }
            }
            else {
                $json = json_encode(['status' => 'error', 'error' => "File extension is not allowed. Please try again with another file."]);
            }
            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxVideosDetail()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		if ($this->request->is('ajax') || $this->request->is('post')) {
            $videos = $this->MediaVideos->find('all', ['contain' => ['Admins']])->order(['MediaVideos.id' => 'DESC']);
            $this->set(compact('videos'));
			$this->render();
		}
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
    public function ajaxUpdateVideos()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        $mediaVideoModal  = new MediaVideoModalForm();
		if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($mediaVideoModal->execute($this->request->getData())) {
            	$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                $video = $this->MediaVideos->get($this->request->getData('id'));

                $video->title       = $this->request->getData('title');
                $video->description = $this->request->getData('description');

                if ($this->MediaVideos->save($video)) {
					// Tell system for new event
					$event = new Event('Model.Media.afterSave', $this, ['media' => $video->name, 'admin' => $admin, 'type' => 'video', 'save' => 'update']);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't update data. Please try again."]);
                }
            }
            else {
            	$errors = $mediaVideoModal->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxDeleteVideos()
	{
		$this->viewBuilder()->enableAutoLayout(false);

        $mediaVideoDelete = new MediaVideoDeleteForm();
		if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($mediaVideoDelete->execute($this->request->getData())) {
            	$session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

                $video    = $this->MediaVideos->get($this->request->getData('id'));
                $filePath = $video->name;
                $fullPath = WWW_ROOT . 'uploads' . DS .'videos' . DS . $filePath;

                $readVideoFile = new File($fullPath);

                $query = $this->MediaVideos->query()
                    ->delete()
                    ->where(['id' => $this->request->getData('id')]);
				$result = $query->execute();
				
				if ($result) {
					// Check for media storage
					$mediaStorage   = $this->Settings->fetch('mediastorage');

					// If media storage is Amazon AWS S3
					if ($mediaStorage->value == 'awss3') {
						$deleteFile = false;

						$awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
						$awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
						$awsS3Region    = $this->Settings->fetch('awss3region');
						$awsS3Bucket    = $this->Settings->fetch('awss3bucket');

						$s3Client = new S3Client([
							'region'  => $awsS3Region->value,
							'version' => 'latest',
							'credentials' => [
								'key'      => $awsS3AccessKey->value,
								'secret'   => $awsS3SecretKey->value,
							]
						]);

						// Path
						$filePath = $fullPath;

						// Key
						$key = 'videos/' . basename($filePath);

						$filePath = $s3Client->getObjectUrl($awsS3Bucket->value, $key);
						
						// Delete object
						try {
							$result = $s3Client->deleteObject([
								'Bucket' => $awsS3Bucket->value,
								'Key'    => $key
							]);

							$deleteFile = true;
						} 
						catch (AwsException $e) {
							$this->log($e->getMessage(), 'debug');
						}

						if ($deleteFile) {
							// Tell system for new event
							$event = new Event('Model.Media.afterDelete', $this, ['media' => $filePath, 'admin' => $admin, 'type' => 'document']);
							$this->getEventManager()->dispatch($event);
		
							$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
						}
					}
					else {
						if ($readVideoFile->delete()) {
							// Tell system for new event
							$event = new Event('Model.Media.afterDelete', $this, ['media' => $filePath, 'admin' => $admin, 'type' => 'video']);
							$this->getEventManager()->dispatch($event);
		
							$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
						}
					}
				}
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $mediaVideoDelete->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
}