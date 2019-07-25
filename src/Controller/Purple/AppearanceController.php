<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
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
use \Gumlet\ImageResize;

class AppearanceController extends AppController
{
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
			$this->loadModel('Medias');
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
							/**
							 * Old style, cropping with ImageResize, but quality is bad
							 * 
								$fullSize        = new ImageResize($image->getFullPath());
								$fullSize->save($fullSizeImage . $generatedName);
							 */
							$fullSize = Image::open($image->getFullPath())->save($fullSizeImage . $generatedName, 'guess', 90);
							/**
							 * Old style, cropping with ImageResize, but quality is bad
							 * 
								$thumbnailSquare = new ImageResize($image->getFullPath());
								$thumbnailSquare->crop(300, 300);
								$thumbnailSquare->save($uploadedThumbnailSquare . $generatedName);
							 */
							$thumbnailSquare = Image::open($image->getFullPath())
											    ->zoomCrop(300, 300)
											    ->save($uploadedThumbnailSquare . $generatedName, 'guess', 90);
							/**
							 * Old style, cropping with ImageResize, but quality is bad
							 * 
								$thumbnailLandscape = new ImageResize($image->getFullPath());
								$thumbnailLandscape->crop(480, 270);
								$thumbnailLandscape->save($uploadedThumbnailLandscape . $generatedName);
                             */
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

							    $json = json_encode(['status' => 'ok', 'name' => $generatedName, 'size' => $imageSize]);
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
        	$session   = $this->getRequest()->getSession();
            $sessionID = $session->read('Admin.id');

			$fileImage = $this->request->getData('image');
			$type      = $this->request->getData('type');

			$fullSizeImage = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS;
			$fullSize 	   = Image::open($fullSizeImage . $fileImage)->save($fullSizeImage . $type . '.png', 'png');
			
			$data = $this->Settings->get($this->request->getData('id'));
			
			$data->value = $type.'.png';
            if ($this->Settings->save($data)) {
            	/**
                 * Save user activity to histories table
                 * array $options => title, detail, admin_id
                 */
                
                $options = [
                    'title'    => 'Change of '.ucwords($type),
                    'detail'   => ' change the '.$type.' of the website.',
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
        	$session   = $this->getRequest()->getSession();
            $sessionID = $session->read('Admin.id');

            $base64   = $this->request->getData('base64');
            $saveType = $this->request->getData('type');

            if (strpos($base64, 'png') !== false) {
                $sanitizeString = str_replace('data:image/png;base64,', '', $base64);
            }
            elseif (strpos($base64, 'jpeg') !== false) {
                $sanitizeString = str_replace('data:image/jpeg;base64,', '', $base64);
            }
            
            $data = $this->Settings->get($this->request->getData('id'));
            
			$fullSizeImage = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS;
			
            list($type, $base64) = explode(';', $base64);
            list(, $base64)      = explode(',', $base64);
			$base64Decode		 = base64_decode($base64);
			file_put_contents($fullSizeImage . $saveType.'.png', $base64Decode);
			
			 /**
             * Old style, cropping with ImageResize, but quality is bad
             * 
            	$image = ImageResize::createFromString(base64_decode($sanitizeString));
				$image->save($fullSizeImage . $type.'.png', IMAGETYPE_PNG);
			 */

            $data->value = $saveType.'.png';
            if ($this->Settings->save($data)) {
            	/**
                 * Save user activity to histories table
                 * array $options => title, detail, admin_id
                 */
                
                $options = [
                    'title'    => 'Change of '.ucwords($type),
                    'detail'   => ' change the '.$type.' of the website.',
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
				$session   = $this->getRequest()->getSession();
	            $sessionID = $session->read('Admin.id');
	            $type      = $this->request->getData('type');

	            $data          = $this->Settings->get($this->request->getData('id'));
	            $filePath      = $data->value;
	            
	            $fullSizeImage = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS . $filePath;
	            
	            $readImageFile = new File($fullSizeImage);
	            
	            $data->value = '';
	            
	            if ($readImageFile->delete() && $this->Settings->save($data)) {
	                /**
	                 * Save user activity to histories table
	                 * array $options => title, detail, admin_id
	                 */
	                
	                $setting = $this->Settings->get($this->request->getData('id'));

	                $options = [
	                    'title'    => 'Delete '.ucwords($type),
	                    'detail'   => ' delete '.ucwords($type).' website.',
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
	            $session   = $this->getRequest()->getSession();
	            $sessionID = $session->read('Admin.id');
	            
	            $setting        = $this->Settings->get($this->request->getData('id'));

	            $leftFooter     = htmlentities(strip_tags($this->request->getData('left'), '<span><a><strong><em>'));
	            $rightFooter    = htmlentities(strip_tags($this->request->getData('right'), '<span><a><strong><em>'));
	            $setting->value = $leftFooter . '::' . $rightFooter;

	            if ($this->Settings->save($setting)) {
	                /**
	                 * Save user activity to histories table
	                 * array $options => title, detail, admin_id
	                 */
	                
	                $setting = $this->Settings->get($this->request->getData('id'));

	                $options = [
	                    'title'    => 'Change of Setting',
	                    'detail'   => ' change footer setting.',
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
	                $json = json_encode(['status' => 'error', 'error' => "Can't update data. Please try again."]);
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
}