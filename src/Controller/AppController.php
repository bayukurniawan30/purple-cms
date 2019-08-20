<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use App\Purple\PurpleProjectGlobal;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');

        if ($this->request->getParam('controller') == 'Setup' || $this->request->getParam('controller') == 'Production') {

        }
        else {
            // Timezone
            $purpleGlobal      = new PurpleProjectGlobal();
            $productionKeyInfo = $purpleGlobal->productionKeyInfo();
            if ($productionKeyInfo == 'filled') {
                $session  = $this->getRequest()->getSession();

                $this->loadModel('Settings');
                $this->set('timeZone', $this->Settings->settingsTimeZone());
                if (!$session->check('Purple.timezone')) {
                    $session->write('Purple.timezone', $this->Settings->settingsTimeZone());
                }

                if (!$session->check('Purple.settingTimezone')) {
                    $session->write('Purple.settingTimezone', $this->Settings->settingsTimeZone());
                }
            }
        }

        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        // $this->loadComponent('Security');
    }
    public function setClientTimezone()
    {
    	$this->viewBuilder()->enableAutoLayout(false);
        if ($this->request->is('ajax') || $this->request->is('post')) {
            $this->loadModel('Settings');
            $session  = $this->getRequest()->getSession();
            $settingTimezone = $this->Settings->settingsTimeZone();
            $session->write('Purple.settingTimezone', $settingTimezone);

            $timezone = trim($this->request->getData('timezone'));
            $session->write('Purple.timezone', $timezone);
            if ($session->check('Purple.timezone')) {
                $json = json_encode(['status' => 'ok', 'timezone' => $timezone]);
            }
            else {
                $json = json_encode(['status' => 'error']);
            }
			$this->set(['json' => $json]);
        }
    	else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
}