<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Utility\Text;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\ThemeApplyForm;
use App\Form\Purple\ThemeDeleteForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use App\Purple\PurpleProjectPlugins;

class ThemesController extends AppController
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
            $this->loadModel('Settings');
			$this->loadModel('Histories');

            if (Configure::read('debug') || $this->request->getEnv('HTTP_HOST') == 'localhost') {
                $cakeDebug = 'on';
            } 
            else {
                $cakeDebug = 'off';
            }

			$queryAdmin      = $this->Admins->find()->where(['id' => $sessionID, 'password' => $sessionPassword])->limit(1);
			$queryFavicon    = $this->Settings->find()->where(['name' => 'favicon'])->first();
			$queryDateFormat = $this->Settings->find()->where(['name' => 'dateformat'])->first();
			$queryTimeFormat = $this->Settings->find()->where(['name' => 'timeformat'])->first();

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
						'title'              => 'Themes | Purple CMS',
						'pageTitle'          => 'Themes',
						'pageTitleIcon'      => 'mdi-image',
						'pageBreadcrumb'     => 'Themes',
						'appearanceFavicon'  => $queryFavicon,
						'settingsDateFormat' => $queryDateFormat->value,
						'settingsTimeFormat' => $queryTimeFormat->value,
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
	public function index()
	{
		$themeApply  = new ThemeApplyForm();
		$themeDelete = new ThemeDeleteForm();

		$activeThemeFolder = new Folder(PLUGINS . 'EngageTheme' . DS);
		$listThemeFolder   = new Folder(WWW_ROOT . 'uploads' . DS . 'themes' . DS);

		$readThemeDetail   = new File(PLUGINS .'EngageTheme' . DS . 'detail.json');
		$read              = $readThemeDetail->read();

		if ($read != false) {
			$decodeDetail = json_decode($read, true);
			$themeName    = $decodeDetail['name'];
			$themeAuthor  = $decodeDetail['author'];
			$themeImage   = $decodeDetail['image'];
			$themePreview = $decodeDetail['preview'];
			$themeDesc    = $decodeDetail['description'];
			$themeVersion = $decodeDetail['version'];

			$data = [
				'readStatus'   => true,
				'themeName'    => $themeName,
				'themeAuthor'  => $themeAuthor,
				'themeImage'   => $themeImage,
				'themePreview' => $themePreview,
				'themeDesc'    => $themeDesc,
				'themeVersion' => $themeVersion
			];
		}
		else {
			$data = [
				'readStatus'   => true
			];
		}

		$detailList = $listThemeFolder->findRecursive('detail.json', true);
		$arrayList  = [];
		foreach ($detailList as $list) {
			$readThemeJson = new File($list);
			$readJson = $readThemeJson->read();

			if ($readJson != false) {
				$explodePath  = explode(DS, $list);
				$reversedPath = array_reverse($explodePath);
				$themeFolder  = $reversedPath[1];

				$readStatus   = true;
				$decodeJson   = json_decode($readJson, true);
				$themeName    = $decodeJson['name'];
				$themeAuthor  = $decodeJson['author'];
				$themeImage   = $decodeJson['image'];
				$themePreview = $decodeJson['preview'];
				$themeDesc    = $decodeJson['description'];
				$themeVersion = $decodeJson['version'];
			}
			else {
				$readStatus   = false;
				$themeFolder  = '';
				$themeName    = '';
				$themeAuthor  = '';
				$themeImage   = '';
				$themePreview = '';
				$themeDesc    = '';
				$themeVersion = '';
			}

			$arrayList[] = [
				'readStatus'   => true,
				'themeFolder'  => $themeFolder,
				'themeName'    => $themeName,
				'themeAuthor'  => $themeAuthor,
				'themeImage'   => $themeImage,
				'themePreview' => $themePreview,
				'themeDesc'    => $themeDesc,
				'themeVersion' => $themeVersion
			];
		}

    	$this->set($data);
    	$this->set('arrayList', $arrayList);
    	$this->set('themeApply', $themeApply);
    	$this->set('themeDelete', $themeDelete);

	}
	public function ajaxApplyTheme()
	{
		$this->viewBuilder()->enableAutoLayout(false);
		
        $themeApply = new ThemeApplyForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($themeApply->execute($this->request->getData())) {
            	$session   = $this->getRequest()->getSession();
	            $sessionID = $session->read('Admin.id');

	            $targetFolder = trim($this->request->getData('folder'));

	            // Delete folder from active theme
	            $folderActive   = str_replace(' ', '', ucwords(trim($this->request->getData('active')), ' ')).'Theme'; 
	            $folderToDelete = new Folder(WWW_ROOT . 'uploads' . DS . 'themes' . DS . $folderActive);
				if ($folderToDelete->delete()) {

	             	// Create theme folder
	            	$themePath   = WWW_ROOT . 'uploads' . DS . 'themes' . DS . $folderActive;
	            	$checkPath   = new Folder($themePath);
	            	if (is_null($checkPath->path)) {
		            	$themeFolder = new Folder($themePath, true, 0777);
		            }

					// Copy theme file to theme list
			        $folderSrc = new Folder(PLUGINS . 'EngageTheme' . DS . 'src');
					$folderSrc->copy([
						'to'     => WWW_ROOT . 'uploads' . DS . 'themes' . DS . $folderActive . DS . 'src',
						'skip'   => [PLUGINS . 'EngageTheme' . DS . 'src' . DS . 'Plugin.php'],
						'scheme' => Folder::OVERWRITE
					]);
					$folderWebroot = new Folder(PLUGINS . 'EngageTheme' . DS . 'webroot');
					$folderWebroot->copy([
						'to'     => WWW_ROOT . 'uploads' . DS . 'themes' . DS . $folderActive . DS . 'webroot',
						'scheme' => Folder::OVERWRITE
					]);
					$fileJson = new File(PLUGINS . 'EngageTheme' . DS . 'detail.json');
					$fileJson->copy(WWW_ROOT . 'uploads' . DS . 'themes' . DS . $folderActive . DS . 'detail.json');

					$deleteSrc = new Folder(PLUGINS . 'EngageTheme' . DS . 'src');
					$deleteSrc->delete();
					$deleteWebroot = new Folder(PLUGINS . 'EngageTheme' . DS . 'webroot');
					$deleteWebroot->delete();
					$deleteJson = new File(PLUGINS . 'EngageTheme' . DS . 'detail.json');
					$deleteJson->delete();

					// Copy applying theme to plugins
            		$applyFolderSrc = new Folder(WWW_ROOT . 'uploads' . DS . 'themes' . DS . $targetFolder . DS . 'src');
					$applyFolderSrc->copy([
						'to'     => PLUGINS . 'EngageTheme' . DS . 'src',
						'skip'   => [WWW_ROOT . 'uploads' . DS . 'themes' . DS . $targetFolder . DS . 'src' . DS . 'Plugin.php'],
						'scheme' => Folder::OVERWRITE
					]);

					$folderWebroot = new Folder(WWW_ROOT . 'uploads' . DS . 'themes' . DS . $targetFolder . DS . 'webroot');
					$folderWebroot->copy([
						'to'     => PLUGINS . 'EngageTheme' . DS . 'webroot',
						'scheme' => Folder::OVERWRITE
					]);
					$fileJson = new File(WWW_ROOT . 'uploads' . DS . 'themes' . DS . $targetFolder . DS . 'detail.json');
					$fileJson->copy(PLUGINS . 'EngageTheme' . DS . 'detail.json');

	                /**
					 * Save user activity to histories table
					 * array $options => title, detail, admin_id
					 */

					$options = [
						'title'    => 'Applying a Theme',
						'detail'   => ' apply '.trim($this->request->getData('name')).' as an active theme.',
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
	                $json = json_encode(['status' => 'error', 'error' => "Can't delete folder. Please try again."]);
	            }
        	}
            else {
            	$errors = $themeApply->errors();
                $json = json_encode(['status' => 'error', 'error' => "Can't apply theme. Please try again."]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
	public function ajaxUploadTheme()
	{
		$this->viewBuilder()->enableAutoLayout(false);

		if ($this->request->is('post')) {
			$session   = $this->getRequest()->getSession();
            $sessionID = $session->read('Admin.id');

        	$temporaryFolder = TMP . 'uploads' . DS . 'themes';

			$fileName       = $this->request->getData('file.name');
			$explodeNewName = explode(".", $fileName);
			$fileExtension  = end($explodeNewName);
			$acceptedExt    = ['zip'];
            if (in_array($fileExtension, $acceptedExt)) {
            	if (strpos($fileName, 'Theme') !== false) {
					$onlyName   = str_replace('.zip', '', $fileName);
					$uploadFile = $temporaryFolder . DS . $fileName;
	                if (move_uploaded_file($this->request->getData('file.tmp_name'), $uploadFile)) {
	                	// Create theme folder
		            	$themePath   = WWW_ROOT . 'uploads' . DS . 'themes' . DS . $onlyName;
		            	$checkPath   = new Folder($themePath);
		            	if (is_null($checkPath->path)) {
			            	$themeFolder = new Folder($themePath, true, 0777);
			            }

			            $zipFile = new \PhpZip\ZipFile();
			            $zipFile->openFile($uploadFile)
			            		->extractTo($themePath)
			            		->close();

			            $deleteZip = new File($temporaryFolder . DS . $fileName);
						if ($deleteZip->delete()) {
		                    /**
		                     * Save user activity to histories table
		                     * array $options => title, detail, admin_id
		                     */
		                    
		                    $options = [
		                        'title'    => 'Addition of a New Theme',
		                        'detail'   => ' upload '.$onlyName.' to themes.',
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
		                    $json = json_encode(['status' => 'error', 'error' => "Can't upload file. Please try again."]);
	                	}
	                }
	                else {
	                    $json = json_encode(['status' => 'error', 'error' => "Can't upload file. Please try again."]);
	                }
				}
				else {
	                $json = json_encode(['status' => 'error', 'error' => "File is not allowed. Please try again with a Purple CMS theme file."]);
				}
            }
            else {
                $json = json_encode(['status' => 'error', 'error' => "File extension is not allowed. Please try again with zip file."]);
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

		$themeDelete = new ThemeDeleteForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($themeDelete->execute($this->request->getData())) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
                
				$deleteTheme = new Folder(WWW_ROOT . 'uploads' . DS . 'themes' . DS . $this->request->getData('folder'));

                if ($deleteTheme->delete()) {
                    /**
                     * Save user activity to histories table
                     * array $options => title, detail, admin_id
                     */
                    
                    $options = [
                        'title'    => 'Deletion of a Theme',
                        'detail'   => ' delete '.$this->request->getData('name').' from themes.',
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
            	$errors = $themeDelete->errors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
	}
}