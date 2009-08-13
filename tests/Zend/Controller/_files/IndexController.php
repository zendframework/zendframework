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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Controller/Action.php';

/**
 * Mock file for testbed
 *
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class IndexController extends Zend_Controller_Action
{

    /**
     * Test Function for indexAction
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_response->appendBody("Index action called\n");
    }

    /**
     * Test Function for prefixAction
     *
     * @return void
     */
    public function prefixAction()
    {
        $this->_response->appendBody("Prefix action called\n");
    }

    /**
     * Test Function for argsAction
     *
     * @return void
     */
    public function argsAction()
    {
        $args = '';
        foreach ($this->getInvokeArgs() as $key => $value) {
            $args .= $key . ': ' . $value . '; ';
        }

        $this->_response->appendBody('Args action called with params ' . $args . "\n");
    }

    /**
     * Test Function for replaceAction
     *
     * @return void
     */
    public function replaceAction()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index')
                ->setActionName('reset')
                ->setDispatched(false);
        $response = new Zend_Controller_Response_Http();
        $front    = Zend_Controller_Front::getInstance();
        $front->setRequest($request)
              ->setResponse($response);
    }

    /**
     * Test Function for resetAction
     *
     * @return void
     */
    public function resetAction()
    {
        $this->_response->appendBody('Reset action called');
    }

}
