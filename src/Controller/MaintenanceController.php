<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use App\Form\MaintenanceForm;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSeo;
use App\Purple\PurpleProjectSettings;
use App\Purple\PurpleProjectApi;
use Carbon\Carbon;
use Melbahja\Seo\Factory;

class MaintenanceController extends AppController
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
    public function beforeRender(\Cake\Event\Event $event)
    {
    	$this->viewBuilder()->setLayout('maintenance');
    }
    public function initialize()
    {
        if ($this->request->getEnv('HTTP_HOST') == 'localhost') {
            $cakeDebug    = 'on';
            $formSecurity = 'off';
        } 
        else {
            if (Configure::read('debug')) {
                $cakeDebug = 'on';
            }
            else {
                $cakeDebug = 'off';
            }
            $formSecurity  = 'on';
        }

        $data = [
            'cakeDebug'    => $cakeDebug,
            'formSecurity' => $formSecurity
        ];

        $this->set($data);
    }
    public function index()
    {
        $purpleGlobal = new PurpleProjectGlobal();
        $maintenance  = new MaintenanceForm();

        $this->loadModel('Socials');
        $this->loadModel('Settings');

    	$socials = $this->Socials->find('all')->order(['ordering' => 'ASC']);
        $this->set(compact('socials'));
        
        // Generate Schema.org ld+json
        $purpleSeo     = new PurpleProjectSeo();
        $websiteSchema = $purpleSeo->schemaLdJson('website');
        $orgSchema     = $purpleSeo->schemaLdJson('organization');

        $queryBackgroundComingSoon = $this->Settings->find()->where(['name' => 'backgroundmaintenance'])->first();

    	$data = [
    		'notifyEmail'         => $maintenance,
            'settingBgComingSoon' => $queryBackgroundComingSoon,
            'ldJsonWebsite'       => $websiteSchema,
            'ldJsonOrganization'  => $orgSchema
    	];

        $this->set($data);
    }
    public function ajaxGetEmail()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        
        $maintenance  = new MaintenanceForm();
        if ($this->request->is('ajax') || $this->request->is('post')) {
            if ($maintenance->execute($this->request->getData())) {
                $purpleApi = new PurpleProjectApi();
                $verifyEmail = $purpleApi->verifyEmail($this->request->getData('email'));

                if ($verifyEmail == true) {
                    $this->loadModel('Subscribers');

                    $findDuplicate = $this->Subscribers->find()->where(['email' => $this->request->getData('email')]);

                    if ($findDuplicate->count() >= 1) {
                        $json = json_encode(['status' => 'error', 'error' => "Can't save data due to duplication of data. Please try again with another email."]);
                    }
                    else {
                        $subscriber = $this->Subscribers->newEntity();
                        $subscriber = $this->Subscribers->patchEntity($subscriber, $this->request->getData());
                        $subscriber->status = 'active';

                        if ($this->Subscribers->save($subscriber)) {
                            $json = json_encode(['status' => 'ok']);
                        }
                        else {
                            $json = json_encode(['status' => 'error', 'error' => "Can't save data. Please try again."]);
                        }
                    }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "Email is not valid. Please use a real email."]);
                }
            }
            else {
            	$errors = $maintenance->errors();
                $json   = json_encode(['status' => 'error', 'error' => $errors]);
            }
            $this->set(['json' => $json]);
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
}