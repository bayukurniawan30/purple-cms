<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use Cake\Http\Exception\NotFoundException;
use App\Form\Purple\ThemeApplyForm;
use App\Form\Purple\ThemeDeleteForm;
use App\Form\Purple\SearchForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectPlugins;
use Particle\Filter\Filter;

class ThemesController extends AppController
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
				['_name' => 'adminLogin', '?' => ['ref' => Router::url($this->getRequest()->getRequestTarget(), true)]]
			);
		}
	}
	public function initialize()
	{
		parent::initialize();

		$this->loadComponent('Flash');

		$session = $this->getRequest()->getSession();
		$sessionHost     = $session->read('Admin.host');
		$sessionID       = $session->read('Admin.id');
		$sessionPassword = $session->read('Admin.password');

		$this->viewBuilder()->setLayout('dashboard');
		$this->loadModel('Admins');
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
				['controller' => 'Authenticate', 'action' => 'login', '?' => ['ref' => Router::url($this->getRequest()->getRequestTarget(), true)]]
			);
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
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['folder', 'name', 'active'])->trim()->stripHtml();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

            	$session   = $this->getRequest()->getSession();
	            $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);

	            $targetFolder = $requestData->folder;

	            // Delete folder from active theme
	            $folderActive   = str_replace(' ', '', ucwords($requestData->active)).'Theme'; 
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

					// Tell system for new event
					$event = new Event('Model.Theme.afterApply', $this, ['theme' => $requestData->name, 'admin' => $admin]);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

					$this->Flash->set($requestData->name . ' has been applied.', [
						'element' => 'Flash/Purple/success'
					]);
	            }
	            else {
	                $json = json_encode(['status' => 'error', 'error' => "Can't delete folder. Please try again."]);
	            }
        	}
            else {
            	$errors = $themeApply->getErrors();
                $json = json_encode(['status' => 'error', 'error' => "Can't apply theme. Please try again."]);
            }

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
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
			$admin     = $this->Admins->get($sessionID);

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
							// Tell system for new event
							$event = new Event('Model.Theme.afterUpload', $this, ['theme' => $onlyName, 'admin' => $admin]);
							$this->getEventManager()->dispatch($event);

							$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

							$this->Flash->set($onlyName . ' has been uploaded.', [
								'element' => 'Flash/Purple/success'
							]);
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

            $this->response = $this->response->withType('json');
            $this->response = $this->response->withStringBody($json);

            $this->set(compact('json'));
            $this->set('_serialize', 'json');
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
				// Sanitize user input
				$filter = new Filter();
				$filter->values(['folder', 'name'])->trim()->stripHtml();
				$filterResult = $filter->filter($this->request->getData());
				$requestData  = json_decode(json_encode($filterResult), FALSE);

                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');
				$admin     = $this->Admins->get($sessionID);
                
				$deleteTheme = new Folder(WWW_ROOT . 'uploads' . DS . 'themes' . DS . $requestData->folder);

                if ($deleteTheme->delete()) {
					// Tell system for new event
					$event = new Event('Model.Theme.afterDelete', $this, ['theme' => $requestData->name, 'admin' => $admin]);
					$this->getEventManager()->dispatch($event);

					$json = json_encode(['status' => 'ok', 'activity' => $event->getResult()]);

					$this->Flash->set($requestData->name . ' has been deleted.', [
						'element' => 'Flash/Purple/success'
					]);
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Can't delete data. Please try again."]);
                }
            }
            else {
            	$errors = $themeDelete->getErrors();
                $json = json_encode(['status' => 'error', 'error' => $errors]);
            }

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