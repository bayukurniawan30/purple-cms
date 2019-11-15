<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Utility\Text;
use Cake\Filesystem\File;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\AppearanceDeleteForm;
use App\Form\Purple\FooterEditForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectPlugins;
use Carbon\Carbon;
use Bulletproof;
use Gregwar\Image\Image;
use Particle\Filter\Filter;
use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

class AppearanceController extends AppController
{
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

		$this->viewBuilder()->setLayout('dashboard');
		$this->loadModel('Admins');
		$this->loadModel('Medias');
		$this->loadModel('Settings');

		if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
			$cakeDebug = 'on';
		} 
		else {
			$cakeDebug = 'off';
		}

		$queryAdmin   = $this->Admins->signedInUser($sessionID, $sessionPassword);
		$queryFavicon = $this->Settings->fetch('favicon');

		$rowCount = $queryAdmin->count();
		if ($rowCount > 0) {
			$adminData = $queryAdmin->first();
			
			$dashboardSearch = new SearchForm();
			
			// Plugins List
			$purplePlugins 	= new PurpleProjectPlugins();
			$plugins		= $purplePlugins->purplePlugins();
			$this->set('plugins', $plugins);
			
			if ($adminData->level == 1) {
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
					'dashboardSearch'	=> $dashboardSearch,
					'title'             => 'Appearance | Purple CMS',
					'pageTitle'         => 'Appearance',
					'pageTitleIcon'     => 'mdi-image',
					'pageBreadcrumb'    => 'Appearance',
					'appearanceFavicon' => $queryFavicon
				];
				$this->set($data);
			}
			else {
				return $this->redirect(
					['controller' => 'Dashboard', 'action' => 'index']
				);
			}
		}
		else {
			return $this->redirect(
				['controller' => 'Authenticate', 'action' => 'login']
			);
		}
	}
    public function favicon() {
        $appearanceDelete  = new AppearanceDeleteForm();
        
		$favicon       = $this->Settings->find()->where(['name' => 'favicon'])->first();
        
        $data = [
			'pageTitle'        => 'Favicon',
			'pageBreadcrumb'   => 'Appearance::Favicon',
			'favicon'          => $favicon,
			'appearanceDelete' => $appearanceDelete
		];		
        
        $this->set($data);
    }
    public function logo() {
        $appearanceDelete  = new AppearanceDeleteForm();
        
		$logo          = $this->Settings->find()->where(['name' => 'websitelogo'])->first();
        
        $data = [
			'pageTitle'        => 'Logo',
			'pageBreadcrumb'   => 'Appearance::Logo',
			'logo'             => $logo,
			'appearanceDelete' => $appearanceDelete
		];		
        
        $this->set($data);
    }
    public function footer() {
        $sfooterEdit    = new FooterEditForm();

		$sfooter        = $this->Settings->find()->where(['name' => 'secondaryfooter'])->first();
		$explodeSfooter = explode('::', $sfooter->value);
        
        $data = [
			'pageTitle'        => 'Footer',
			'pageBreadcrumb'   => 'Appearance::Footer',
			'footerId'         => $sfooter->id,
			'leftSfooter'      => $explodeSfooter[0],
			'rightSfooter'     => $explodeSfooter[1],
			'sfooterEdit' 	   => $sfooterEdit
		];		
        
        $this->set($data);
    }
    public function ajaxImagesUpload() 
	{
		$this->viewBuilder()->enableAutoLayout(false);

		if ($this->request->is('post')) {
			if (!empty($this->request->getData('file'))) {
				$session   = $this->getRequest()->getSession();
				$sessionID = $session->read('Admin.id');

			    $purpleSettings = new PurpleProjectSettings();
			    $timezone       = $purpleSettings->timezone();

				$date   = Carbon::now($timezone);
                $uploadPath = TMP . 'uploads' . DS . 'images' . DS;
				$fileName   = $this->request->getData('file.name');

				$image = new Bulletproof\Image($this->request->getData());
				if ($image["file"]){
					$newName         = Text::slug($fileName, ['preserve' => '.']);
					$explodeNewName  = explode(".", $newName);
					$fileExtension   = end($explodeNewName);
					$fileOnlyName    = str_replace('.'.$fileExtension, '', $newName);
					$dateSlug        = Text::slug($date);
					$generatedName   = $fileOnlyName . '_' . $dateSlug . '.' . $fileExtension;

					$image->setName($generatedName)
					      ->setMime(array('jpeg', 'png'))
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
                           
							$media           = $this->Medias->newEntity();

							$media->name     = $generatedName;
							$media->created  = $date;
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

										// $readImageFile = new File($fullSizeImage . $generatedName);
										// $readImageFile->delete();
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

							    $json = json_encode(['status' => 'ok', 'name' => $generatedName, 'path' => $path, 'size' => $imageSize]);
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
	public function ajaxSaveWithoutCrop()
	{
        $this->viewBuilder()->enableAutoLayout(false);

		if ($this->request->is('post') || $this->request->is('ajax')) {
			// Sanitize user input
			$filter = new Filter();
			$filter->values(['image', 'type'])->trim()->stripHtml();
			$filter->values(['id'])->int();
			$filterResult = $filter->filter($this->request->getData());
			$requestData  = json_decode(json_encode($filterResult), FALSE);

        	$session   = $this->getRequest()->getSession();
			$sessionID = $session->read('Admin.id');
			$admin     = $this->Admins->get($sessionID);

			$fileImage = $requestData->image;
			$type      = $requestData->type;

			$fullSizeImage = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS;
			$fullSize 	   = Image::open($fullSizeImage . $fileImage)->save($fullSizeImage . $type . '.png', 'png');
			
			$setting = $this->Settings->get($requestData->id);
			
			$setting->value = $type.'.png';
            if ($this->Settings->save($setting)) {
				// Check for media storage
				$mediaStorage = $this->Settings->fetch('mediastorage');

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
					$originalFilePath = $fullSizeImage . $type . '.png';

					// Key
					$originalKey = 'images/original/' . basename($originalFilePath);
					
					// Send original image
					try {
						$result = $s3Client->putObject([
							'Bucket'     => $awsS3Bucket->value,
							'Key'        => $originalKey,
							'SourceFile' => $originalFilePath,
							'ACL'        => 'public-read',
						]);

						$readImageFile = new File($fullSizeImage . $type . '.png');
						$readImageFile->delete();
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
				$event = new Event('Model.Setting.afterUpdateAppearance', $this, ['setting' => $setting, 'admin' => $admin, 'type' => $type]);
				$this->getEventManager()->dispatch($event);

                $json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
            }
            else {
                $json = json_encode(['status' => 'error', 'error' => "Can't update data. Please try again."]);
            }
            $this->set(['json' => $json]);
        }
        else {
            throw new NotFoundException(__('Page not found'));
        }
	} 
    public function ajaxSave() 
    {
        $this->viewBuilder()->enableAutoLayout(false);
        
        if ($this->request->is('ajax') || $this->request->is('post')) {
			// Sanitize user input
			$filter = new Filter();
			$filter->values(['base64', 'type'])->trim()->stripHtml();
			$filter->values(['id'])->int();
			$filterResult = $filter->filter($this->request->getData());
			$requestData  = json_decode(json_encode($filterResult), FALSE);

        	$session   = $this->getRequest()->getSession();
            $sessionID = $session->read('Admin.id');
			$admin     = $this->Admins->get($sessionID);

            $base64   = $requestData->base64;
            $saveType = $requestData->type;

            if (strpos($base64, 'png') !== false) {
                $sanitizeString = str_replace('data:image/png;base64,', '', $base64);
            }
            elseif (strpos($base64, 'jpeg') !== false) {
                $sanitizeString = str_replace('data:image/jpeg;base64,', '', $base64);
            }
            
            $setting = $this->Settings->get($requestData->id);
            
			$fullSizeImage = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS;
			
            list($type, $base64) = explode(';', $base64);
            list(, $base64)      = explode(',', $base64);
			$base64Decode		 = base64_decode($base64);
			file_put_contents($fullSizeImage . $saveType.'.png', $base64Decode);

            $setting->value = $saveType.'.png';
            if ($this->Settings->save($setting)) {
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
					$originalFilePath = $fullSizeImage . $saveType . '.png';

					// Key
					$originalKey = 'images/original/' . basename($originalFilePath);
					
					// Send original image
					try {
						$result = $s3Client->putObject([
							'Bucket'     => $awsS3Bucket->value,
							'Key'        => $originalKey,
							'SourceFile' => $originalFilePath,
							'ACL'        => 'public-read',
						]);

						$readImageFile = new File($fullSizeImage . $saveType . '.png');
						$readImageFile->delete();
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
				$event = new Event('Model.Setting.afterUpdateAppearance', $this, ['setting' => $setting, 'admin' => $admin, 'type' => $type]);
				$this->getEventManager()->dispatch($event);

                $json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
            }
            else {
                $json = json_encode(['status' => 'error', 'error' => "Can't update data. Please try again."]);
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
        
        $appearanceDelete  = new AppearanceDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($appearanceDelete->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['type'])->trim()->stripHtml();
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

				$session   = $this->getRequest()->getSession();
	            $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
				$type      = $requestData->type;

	            $setting       = $this->Settings->get($requestData->id);
	            $filePath      = $setting->value;
	            
	            $fullSizeImage = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS . $filePath;
	            
	            $readImageFile = new File($fullSizeImage);
	            
	            $setting->value = '';
				
				if ($this->Settings->save($setting)) {
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

						if ($deleteOriginal) {
							// Tell system for new event
							$event = new Event('Model.Setting.afterDeleteAppearance', $this, ['setting' => $setting, 'admin' => $admin, 'type' => $type]);
							$this->getEventManager()->dispatch($event);

							$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
						}
						else {
							$json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);

						}
					}
					else {
						if ($readImageFile->delete()) {
							// Tell system for new event
							$event = new Event('Model.Setting.afterDeleteAppearance', $this, ['setting' => $setting, 'admin' => $admin, 'type' => $type]);
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

	            $this->set(['json' => $json]);
	        }
	        else {
	        	$errors = $appearanceDelete->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
	        }
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function ajaxUpdateFooter()
	{
        $this->viewBuilder()->enableAutoLayout(false);

        $sfooterEdit    = new FooterEditForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($sfooterEdit->execute($this->request->getData())) {
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['left', 'right'])->stripHtml('<span><a><strong><em>');
				$filter->values(['id'])->int();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

	            $session   = $this->getRequest()->getSession();
	            $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
	            
	            $setting        = $this->Settings->get($requestData->id);

	            $leftFooter     = htmlentities($requestData->left);
	            $rightFooter    = htmlentities($requestData->right);
	            $setting->value = $leftFooter . '::' . $rightFooter;

	            if ($this->Settings->save($setting)) {
					// Tell system for new event
					$event = new Event('Model.Setting.afterUpdateFooter', $this, ['setting' => $setting, 'admin' => $admin]);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);
	            }
	            else {
	                $json = json_encode(['status' => 'error', 'error' => "Can't update data. Please try again."]);
	            }

	            $this->set(['json' => $json]);
	        }
	        else {
	        	$errors = $sfooterEdit->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
	        }
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
}