<?php
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSeo;

class HeadlessController extends AppController
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
    	$this->viewBuilder()->setLayout('headless');
    }
    public function initialize()
    {
        parent::initialize();

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
        $this->loadModel('Settings');
        
        // Generate Schema.org ld+json
        $purpleSeo     = new PurpleProjectSeo();
        $websiteSchema = $purpleSeo->schemaLdJson('website');
        $orgSchema     = $purpleSeo->schemaLdJson('organization');

        $web = $this->Settings->find()->where(['name' => 'headlessweb'])->first();

    	$data = [
            'web'                 => $web,
            'ldJsonWebsite'       => $websiteSchema,
            'ldJsonOrganization'  => $orgSchema
    	];

        $this->set($data);
    }
}