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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Mock file for testbed
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HelperFlashMessengerController extends \Zend\Controller\Action
{

    /**
     * Test Function for indexAction
     *
     * @return void
     */
    public function indexAction()
    {
        $flashmessenger = $this->_helper->FlashMessenger;
        $this->getResponse()->appendBody(get_class($flashmessenger));

        $messages = $flashmessenger->getCurrentMessages();
        if (count($messages) === 0) {
            $this->getResponse()->appendBody('1');
        }

        $flashmessenger->addMessage('My message');
        $messages = $flashmessenger->getCurrentMessages();

        if (implode('', $messages) === 'My message') {
            $this->getResponse()->appendBody('2');
        }

        if ($flashmessenger->count() === 0) {
            $this->getResponse()->appendBody('3');
        }

        if ($flashmessenger->hasMessages() === false) {
            $this->getResponse()->appendBody('4');
        }

        if ($flashmessenger->getRequest() === $this->getRequest()) {
            $this->getResponse()->appendBody('5');
        }

        if ($flashmessenger->getResponse() === $this->getResponse()) {
            $this->getResponse()->appendBody('6');
        }

    }

}
