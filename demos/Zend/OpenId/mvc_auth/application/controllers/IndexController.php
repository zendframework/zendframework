<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

/**
 * @see Zend_Auth
 */
require_once 'Zend/Auth.php';

/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Demos
 * @uses       Zend_Controller_Action
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IndexController extends Zend_Controller_Action
{
    /**
     * indexAction
     *
     * @return void
     */
    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('/index/login');
        } else {
            $this->_redirect('/index/welcome');
        }
    }

    /**
     * welcomeAction
     *
     * @return void
     */
    public function welcomeAction()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('index/login');
        }
        $this->view->user = $auth->getIdentity();
    }

    /**
     * loginAction
     *
     * @return void
     */
    public function loginAction()
    {
        $this->view->status = "";
        if (($this->_request->isPost() &&
             $this->_request->getPost('openid_action') == 'login' &&
             $this->_request->getPost('openid_identifier', '') !== '') ||
            ($this->_request->isPost() &&
             $this->_request->getPost('openid_mode') !== null) ||
            (!$this->_request->isPost() &&
             $this->_request->getQuery('openid_mode') != null)) {
            Zend_Loader::loadClass('Zend_Auth_Adapter_OpenId');
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate(
                new Zend_Auth_Adapter_OpenId($this->_request->getPost('openid_identifier')));
            if ($result->isValid()) {
                $this->_redirect('/index/welcome');
            } else {
                $auth->clearIdentity();
                foreach ($result->getMessages() as $message) {
                    $this->view->status .= "$message<br>\n";
                }
            }
        }
        $this->render();
    }

    /**
     * logoutAction
     *
     * @return void
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/index/index');
    }
}
