<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller', 'CakeEmail', 'Network/Email');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $uses = array('AdminVariable', 'Employee');

    public $components = array(
        'Session',
        'RequestHandler',
        'Auth' => array(
            'loginRedirect' => array(
                'controller' => 'posts',
                'action' => 'index'
            ),
            'logoutRedirect' => array(
                'controller' => 'pages',
                'action' => 'display',
                'home'
            )
        )
    );

    public function beforeFilter() {
        $this->set('title_for_layout', 'Westtoer Afwezig');
        $session = $this->Session->read('Auth.Employee');
        $isSupervisor = false;
        if(!empty($session)){
            if($this->Employee->find('count', array('conditions' => array('Employee.supervisor_id' => $session["internal_id"]))) > 0){
                $isSupervisor = true;
            }
        }

        $this->set('isSupervisor', $isSupervisor);
        //$this->Auth->allow('index', 'view');
        if($this->admin_variable('lockApp') == 'true'){
            if($this->Session->read('Auth.Role.adminpanel') == false){
                if($this->params['controller'] !== 'users'){
                    if($this->here !== '/'){
                        $this->Session->destroy();
                        $this->Auth->logout();
                        $this->redirect(array('controller' => 'users', 'action' => 'locked'));
                    }
                }
            }
        }
    }

    private function admin_variable($name, $type = 'find'){
        $adminVar = $this->AdminVariable->find('first', array('conditions' => array('name' => $name)));
        if($type == 'find'){
            if(!empty($adminVar)){
                $x = $adminVar["AdminVariable"]["value"];
            } else {
                $x = null;
            }

        }

        return $x;
    }
}
