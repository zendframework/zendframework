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
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Navigation/Page/Mvc.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Router/Route.php';

/**
 * Tests the class Zend_Navigation_Page_Mvc
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Navigation
 */
class Zend_Navigation_Page_MvcTest extends PHPUnit_Framework_TestCase
{
    protected $_front;
    protected $_oldRequest;
    protected $_oldRouter;

    protected function setUp()
    {
        $this->_front = Zend_Controller_Front::getInstance();
        $this->_oldRequest = $this->_front->getRequest();
        $this->_oldRouter = $this->_front->getRouter();

        $this->_front->resetInstance();
        $this->_front->setRequest(new Zend_Controller_Request_Http());
        $this->_front->getRouter()->addDefaultRoutes();
    }

    protected function tearDown()
    {
        if (null !== $this->_oldRequest) {
            $this->_front->setRequest($this->_oldRequest);
        } else {
            $this->_front->setRequest(new Zend_Controller_Request_Http());
        }
        $this->_front->setRouter($this->_oldRouter);
    }

    public function testHrefGeneratedByUrlHelperRequiresNoRoute()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $page->setAction('view');
        $page->setController('news');

        $this->assertEquals('/news/view', $page->getHref());
    }

    public function testHrefGeneratedIsRouteAware()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'myaction',
            'controller' => 'mycontroller',
            'route' => 'myroute',
            'params' => array(
                'page' => 1337
            )
        ));

        $this->_front->getRouter()->addRoute(
            'myroute',
            new Zend_Controller_Router_Route(
                'lolcat/:action/:page',
                array(
                    'module'     => 'default',
                    'controller' => 'foobar',
                    'action'     => 'bazbat',
                    'page'       => 1
                )
            )
        );

        $this->assertEquals('/lolcat/myaction/1337', $page->getHref());
    }

    public function testIsActiveReturnsTrueOnIdenticalModuleControllerAction()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $this->_front->getRequest()->setParams(array(
            'module' => 'default',
            'controller' => 'index',
            'action' => 'index'
        ));

        $this->assertEquals(true, $page->isActive());
    }

    public function testIsActiveReturnsFalseOnDifferentModuleControllerAction()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'bar',
            'controller' => 'index'
        ));

        $this->_front->getRequest()->setParams(array(
            'module' => 'default',
            'controller' => 'index',
            'action' => 'index'
        ));

        $this->assertEquals(false, $page->isActive());
    }

    public function testIsActiveReturnsTrueOnIdenticalIncludingPageParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'view',
            'controller' => 'post',
            'module' => 'blog',
            'params' => array(
                'id' => '1337'
            )
        ));

        $this->_front->getRequest()->setParams(array(
            'module' => 'blog',
            'controller' => 'post',
            'action' => 'view',
            'id' => '1337'
        ));

        $this->assertEquals(true, $page->isActive());
    }

    public function testIsActiveReturnsTrueWhenRequestHasMoreParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'view',
            'controller' => 'post',
            'module' => 'blog'
        ));

        $this->_front->getRequest()->setParams(array(
            'module' => 'blog',
            'controller' => 'post',
            'action' => 'view',
            'id' => '1337'
        ));

        $this->assertEquals(true, $page->isActive());
    }

    public function testIsActiveReturnsFalseWhenRequestHasLessParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'view',
            'controller' => 'post',
            'module' => 'blog',
            'params' => array(
                'id' => '1337'
            )
        ));

        $this->_front->getRequest()->setParams(array(
            'module' => 'blog',
            'controller' => 'post',
            'action' => 'view',
            'id' => null
        ));

        $this->assertEquals(false, $page->isActive());
    }

    public function testActionAndControllerAccessors()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $props = array('Action', 'Controller');
        $valids = array('index', 'help', 'home', 'default', '1', ' ', '', null);
        $invalids = array(42, (object) null);

        foreach ($props as $prop) {
            $setter = "set$prop";
            $getter = "get$prop";

            foreach ($valids as $valid) {
                $page->$setter($valid);
                $this->assertEquals($valid, $page->$getter());
            }

            foreach ($invalids as $invalid) {
                try {
                    $page->$setter($invalid);
                    $msg = "'$invalid' is invalid for $setter(), but no ";
                    $msg .= 'Zend_Navigation_Exception was thrown';
                    $this->fail($msg);
                } catch (Zend_Navigation_Exception $e) {

                }
            }
        }
    }

    public function testModuleAndRouteAccessors()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $props = array('Module', 'Route');
        $valids = array('index', 'help', 'home', 'default', '1', ' ', null);
        $invalids = array(42, (object) null);

        foreach ($props as $prop) {
            $setter = "set$prop";
            $getter = "get$prop";

            foreach ($valids as $valid) {
                $page->$setter($valid);
                $this->assertEquals($valid, $page->$getter());
            }

            foreach ($invalids as $invalid) {
                try {
                    $page->$setter($invalid);
                    $msg = "'$invalid' is invalid for $setter(), but no ";
                    $msg .= 'Zend_Navigation_Exception was thrown';
                    $this->fail($msg);
                } catch (Zend_Navigation_Exception $e) {

                }
            }
        }
    }

    public function testSetAndGetResetParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $valids = array(true, 1, '1', 3.14, 'true', 'yes');
        foreach ($valids as $valid) {
            $page->setResetParams($valid);
            $this->assertEquals(true, $page->getResetParams());
        }

        $invalids = array(false, 0, '0', 0.0, array());
        foreach ($invalids as $invalid) {
            $page->setResetParams($invalid);
            $this->assertEquals(false, $page->getResetParams());
        }
    }

    public function testSetAndGetParams()
    {
        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index'
        ));

        $params = array('foo' => 'bar', 'baz' => 'bat');

        $page->setParams($params);
        $this->assertEquals($params, $page->getParams());

        $page->setParams();
        $this->assertEquals(array(), $page->getParams());

        $page->setParams($params);
        $this->assertEquals($params, $page->getParams());

        $page->setParams(array());
        $this->assertEquals(array(), $page->getParams());
    }

    public function testToArrayMethod()
    {
        $options = array(
            'label' => 'foo',
            'action' => 'index',
            'controller' => 'index',
            'module' => 'test',
            'id' => 'my-id',
            'class' => 'my-class',
            'title' => 'my-title',
            'target' => 'my-target',
            'order' => 100,
            'active' => true,
            'visible' => false,

            'foo' => 'bar',
            'meaning' => 42
        );

        $page = new Zend_Navigation_Page_Mvc($options);

        $toArray = $page->toArray();

        $options['reset_params'] = true;
        $options['route'] = null;
        $options['params'] = array();
        $options['rel'] = array();
        $options['rev'] = array();

        $this->assertEquals(array(),
            array_diff_assoc($options, $page->toArray()));
    }

    public function testSpecifyingAnotherUrlHelperToGenerateHrefs()
    {
        $path = dirname(dirname(__FILE__)) . '/_files/My/UrlHelper.php';
        require_once $path;

        $newHelper = new My_UrlHelper();
        Zend_Navigation_Page_Mvc::setUrlHelper($newHelper);

        $page = new Zend_Navigation_Page_Mvc();

        $expected = My_UrlHelper::RETURN_URL;
        $actual = $page->getHref();

        $old = Zend_Controller_Action_HelperBroker::getStaticHelper('Url');
        Zend_Navigation_Page_Mvc::setUrlHelper($old);

        $this->assertEquals($expected, $actual);
    }
}
