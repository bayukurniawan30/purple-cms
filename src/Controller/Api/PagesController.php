<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\Utility\Text;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Http\ServerRequest;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSeo;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;

class PagesController extends AppController
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

        $this->loadModel('Pages');
        $this->loadModel('Menus');
        $this->loadModel('Submenus');
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
    public function view()
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey = trim($this->request->getHeaderLine('X-Purple-Api-Key'));

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $pages = $this->Pages->find('all', [
                    'order' => ['Pages.id' => 'DESC']])->contain('PageTemplates')->contain('Admins', function (Query $q) {
                        return $q
                            ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                    })->where(['Pages.parent IS' => NULL]);

                if ($pages->count() > 0) {
                    foreach ($pages as $page) {
                        $pageId = $page->id;

                        $child = $this->Pages->find('all', [
                            'order' => ['Pages.id' => 'DESC']])->contain('PageTemplates')->contain('Admins', function (Query $q) {
                                return $q
                                    ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                            })->where(['Pages.parent IS' => $pageId]);

                        if ($child->count() > 0) {
                            $page->child = $child->first();
                        }
                        else {
                            $page->child = NULL;
                        }
                    }

                    $return = [
                        'status' => 'ok',
                        'pages'  => $pages,
                        'error'  => $error
                    ];
                }
                else {
                    $return = [
                        'status' => 'ok',
                        'pages'  => NULL,
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
    public function detail($idOrSlug)
    {
        if ($this->request->is('get') && $this->request->hasHeader('X-Purple-Api-Key')) {
            $apiKey   = trim($this->request->getHeaderLine('X-Purple-Api-Key'));
            $idOrSlug = trim($idOrSlug);

            $apiAccessKey = $this->Settings->settingsApiAccessKey();

            $error = NULL;

            if ($apiAccessKey == $apiKey) {
                $pages = $this->Pages->find('all', [
                    'order' => ['Pages.id' => 'DESC']])->contain('PageTemplates')->contain('Admins', function (Query $q) {
                        return $q
                            ->select(['id', 'username', 'email', 'display_name', 'level', 'photo']);  
                    })->where([
                        'OR' => [['Pages.id' => $idOrSlug], ['Pages.slug' => $idOrSlug]],
                    ])->limit(1);

                if ($pages->count() > 0) {
                    $page = $pages->first();

                    if ($page->parent != NULL) {
                        $parent = $this->Pages->find('all')->where(['Pages.id' => $page->parent])->limit(1);
                        if ($parent->count() > 0) {
                            $page->parent_slug = $parent->first()->slug;
                        }
                        else {
                            $page->parent_slug = NULL;
                        }
                    }

                    $return = [
                        'status' => 'ok',
                        'page'   => $page,
                        'error'  => $error
                    ];
                }
                else {
                    $return = [
                        'status' => 'ok',
                        'page'   => NULL,
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