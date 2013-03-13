<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper\Navigation;

use Zend\Navigation\Navigation as Container;
use Zend\Permissions\Acl;
use Zend\Permissions\Acl\Role;
use Zend\ServiceManager\ServiceManager;
use Zend\View;
use Zend\View\Helper\Navigation;

/**
 * Tests Zend_View_Helper_Navigation
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class NavigationTest extends AbstractTest
{
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zend\View\Helper\Navigation';

    /**
     * View helper
     *
     * @var Zend\View\Helper\Navigation
     */
    protected $_helper;

    public function testHelperEntryPointWithoutAnyParams()
    {
        $returned = $this->_helper->__invoke();
        $this->assertEquals($this->_helper, $returned);
        $this->assertEquals($this->_nav1, $returned->getContainer());
    }

    public function testHelperEntryPointWithContainerParam()
    {
        $returned = $this->_helper->__invoke($this->_nav2);
        $this->assertEquals($this->_helper, $returned);
        $this->assertEquals($this->_nav2, $returned->getContainer());
    }

    public function testAcceptAclShouldReturnGracefullyWithUnknownResource()
    {
        // setup
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $accepted = $this->_helper->accept(
            new \Zend\Navigation\Page\Uri(array(
                'resource'  => 'unknownresource',
                'privilege' => 'someprivilege'
            ),
            false)
        );

        $this->assertEquals($accepted, false);
    }

    public function testShouldProxyToMenuHelperByDefault()
    {
        $this->_helper->setContainer($this->_nav1);

        // result
        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testHasContainer()
    {
        $oldContainer = $this->_helper->getContainer();
        $this->_helper->setContainer(null);
        $this->assertFalse($this->_helper->hasContainer());
        $this->_helper->setContainer($oldContainer);
    }

    public function testInjectingContainer()
    {
        // setup
        $this->_helper->setContainer($this->_nav2);
        $expected = array(
            'menu' => $this->_getExpected('menu/default2.html'),
            'breadcrumbs' => $this->_getExpected('bc/default.html')
        );
        $actual = array();

        // result
        $actual['menu'] = $this->_helper->render();
        $this->_helper->setContainer($this->_nav1);
        $actual['breadcrumbs'] = $this->_helper->breadcrumbs()->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingContainerInjection()
    {
        // setup
        $this->_helper->setInjectContainer(false);
        $this->_helper->menu()->setContainer(null);
        $this->_helper->breadcrumbs()->setContainer(null);
        $this->_helper->setContainer($this->_nav2);

        // result
        $expected = array(
            'menu'        => '',
            'breadcrumbs' => ''
        );
        $actual = array(
            'menu'        => $this->_helper->render(),
            'breadcrumbs' => $this->_helper->breadcrumbs()->render()
        );

        $this->assertEquals($expected, $actual);
    }

    public function testServiceManagerIsUsedToRetrieveContainer()
    {
        $container      = new Container;
        $serviceManager = new ServiceManager;
        $serviceManager->setService('navigation', $container);

        $pluginManager  = new View\HelperPluginManager;
        $pluginManager->setServiceLocator($serviceManager);

        $this->_helper->setServiceLocator($pluginManager);
        $this->_helper->setContainer('navigation');

        $expected = $this->_helper->getContainer();
        $actual   = $container;
        $this->assertEquals($expected, $actual);
    }

    public function testInjectingAcl()
    {
        // setup
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);

        $expected = $this->_getExpected('menu/acl.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingAclInjection()
    {
        // setup
        $acl = $this->_getAcl();
        $this->_helper->setAcl($acl['acl']);
        $this->_helper->setRole($acl['role']);
        $this->_helper->setInjectAcl(false);

        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testInjectingTranslator()
    {
        $this->_helper->setTranslator($this->_getTranslator());

        $expected = $this->_getExpected('menu/translated.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDisablingTranslatorInjection()
    {
        $this->_helper->setTranslator($this->_getTranslator());
        $this->_helper->setInjectTranslator(false);

        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->_helper->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTranslatorMethods()
    {
        $translatorMock = $this->getMock('Zend\I18n\Translator\Translator');
        $this->_helper->setTranslator($translatorMock, 'foo');

        $this->assertEquals($translatorMock, $this->_helper->getTranslator());
        $this->assertEquals('foo', $this->_helper->getTranslatorTextDomain());
        $this->assertTrue($this->_helper->hasTranslator());
        $this->assertTrue($this->_helper->isTranslatorEnabled());

        $this->_helper->setTranslatorEnabled(false);
        $this->assertFalse($this->_helper->isTranslatorEnabled());
    }

    public function testSpecifyingDefaultProxy()
    {
        $expected = array(
            'breadcrumbs' => $this->_getExpected('bc/default.html'),
            'menu' => $this->_getExpected('menu/default1.html')
        );
        $actual = array();

        // result
        $this->_helper->setDefaultProxy('breadcrumbs');
        $actual['breadcrumbs'] = $this->_helper->render($this->_nav1);
        $this->_helper->setDefaultProxy('menu');
        $actual['menu'] = $this->_helper->render($this->_nav1);

        $this->assertEquals($expected, $actual);
    }

    public function testGetAclReturnsNullIfNoAclInstance()
    {
        $this->assertNull($this->_helper->getAcl());
    }

    public function testGetAclReturnsAclInstanceSetWithSetAcl()
    {
        $acl = new Acl\Acl();
        $this->_helper->setAcl($acl);
        $this->assertEquals($acl, $this->_helper->getAcl());
    }

    public function testGetAclReturnsAclInstanceSetWithSetDefaultAcl()
    {
        $acl = new Acl\Acl();
        Navigation\AbstractHelper::setDefaultAcl($acl);
        $actual = $this->_helper->getAcl();
        Navigation\AbstractHelper::setDefaultAcl(null);
        $this->assertEquals($acl, $actual);
    }

    public function testSetDefaultAclAcceptsNull()
    {
        $acl = new Acl\Acl();
        Navigation\AbstractHelper::setDefaultAcl($acl);
        Navigation\AbstractHelper::setDefaultAcl(null);
        $this->assertNull($this->_helper->getAcl());
    }

    public function testSetDefaultAclAcceptsNoParam()
    {
        $acl = new Acl\Acl();
        Navigation\AbstractHelper::setDefaultAcl($acl);
        Navigation\AbstractHelper::setDefaultAcl();
        $this->assertNull($this->_helper->getAcl());
    }

    public function testSetRoleAcceptsString()
    {
        $this->_helper->setRole('member');
        $this->assertEquals('member', $this->_helper->getRole());
    }

    public function testSetRoleAcceptsRoleInterface()
    {
        $role = new Role\GenericRole('member');
        $this->_helper->setRole($role);
        $this->assertEquals($role, $this->_helper->getRole());
    }

    public function testSetRoleAcceptsNull()
    {
        $this->_helper->setRole('member')->setRole(null);
        $this->assertNull($this->_helper->getRole());
    }

    public function testSetRoleAcceptsNoParam()
    {
        $this->_helper->setRole('member')->setRole();
        $this->assertNull($this->_helper->getRole());
    }

    public function testSetRoleThrowsExceptionWhenGivenAnInt()
    {
        try {
            $this->_helper->setRole(1337);
            $this->fail('An invalid argument was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (View\Exception\ExceptionInterface $e) {
            $this->assertContains('$role must be a string', $e->getMessage());
        }
    }

    public function testSetRoleThrowsExceptionWhenGivenAnArbitraryObject()
    {
        try {
            $this->_helper->setRole(new \stdClass());
            $this->fail('An invalid argument was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (View\Exception\ExceptionInterface $e) {
            $this->assertContains('$role must be a string', $e->getMessage());
        }
    }

    public function testSetDefaultRoleAcceptsString()
    {
        $expected = 'member';
        Navigation\AbstractHelper::setDefaultRole($expected);
        $actual = $this->_helper->getRole();
        Navigation\AbstractHelper::setDefaultRole(null);
        $this->assertEquals($expected, $actual);
    }

    public function testSetDefaultRoleAcceptsRoleInterface()
    {
        $expected = new Role\GenericRole('member');
        Navigation\AbstractHelper::setDefaultRole($expected);
        $actual = $this->_helper->getRole();
        Navigation\AbstractHelper::setDefaultRole(null);
        $this->assertEquals($expected, $actual);
    }

    public function testSetDefaultRoleAcceptsNull()
    {
        Navigation\AbstractHelper::setDefaultRole(null);
        $this->assertNull($this->_helper->getRole());
    }

    public function testSetDefaultRoleAcceptsNoParam()
    {
        Navigation\AbstractHelper::setDefaultRole();
        $this->assertNull($this->_helper->getRole());
    }

    public function testSetDefaultRoleThrowsExceptionWhenGivenAnInt()
    {
        try {
            Navigation\AbstractHelper::setDefaultRole(1337);
            $this->fail('An invalid argument was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (View\Exception\ExceptionInterface $e) {
            $this->assertContains('$role must be', $e->getMessage());
        }
    }

    public function testSetDefaultRoleThrowsExceptionWhenGivenAnArbitraryObject()
    {
        try {
            Navigation\AbstractHelper::setDefaultRole(new \stdClass());
            $this->fail('An invalid argument was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (View\Exception\ExceptionInterface $e) {
            $this->assertContains('$role must be', $e->getMessage());
        }
    }

    private $_errorMessage;
    public function toStringErrorHandler($code, $msg, $file, $line, array $c)
    {
        $this->_errorMessage = $msg;
    }

    public function testMagicToStringShouldNotThrowException()
    {
        set_error_handler(array($this, 'toStringErrorHandler'));
        $this->_helper->menu()->setPartial(array(1337));
        $this->_helper->__toString();
        restore_error_handler();

        $this->assertContains('array must contain two values', $this->_errorMessage);
    }

    public function testPageIdShouldBeNormalized()
    {
        $nl = PHP_EOL;

        $container = new \Zend\Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'id'    => 'p1',
                'uri'   => 'p1'
            ),
            array(
                'label' => 'Page 2',
                'id'    => 'p2',
                'uri'   => 'p2'
            )
        ));

        $expected = '<ul class="navigation">' . $nl
                  . '    <li>' . $nl
                  . '        <a id="menu-p1" href="p1">Page 1</a>' . $nl
                  . '    </li>' . $nl
                  . '    <li>' . $nl
                  . '        <a id="menu-p2" href="p2">Page 2</a>' . $nl
                  . '    </li>' . $nl
                  . '</ul>';

        $actual = $this->_helper->render($container);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @group ZF-6854
     */
    public function testRenderInvisibleItem()
    {
        $container = new \Zend\Navigation\Navigation(array(
            array(
                'label' => 'Page 1',
                'id'    => 'p1',
                'uri'   => 'p1'
            ),
            array(
                'label'   => 'Page 2',
                'id'      => 'p2',
                'uri'     => 'p2',
                'visible' => false
            )
        ));

        $render = $this->_helper->menu()->render($container);

        $this->assertFalse(strpos($render, 'p2'));

        $this->_helper->menu()->setRenderInvisible();

        $render = $this->_helper->menu()->render($container);

        $this->assertTrue(strpos($render, 'p2') !== false);
    }

    public function testMultipleNavigations()
    {
        $sm   = new ServiceManager();
        $nav1 = new Container();
        $nav2 = new Container();
        $sm->setService('nav1', $nav1);
        $sm->setService('nav2', $nav2);

        $helper = new Navigation();
        $helper->setServiceLocator($sm);

        $menu     = $helper('nav1')->menu();
        $actual   = spl_object_hash($nav1);
        $expected = spl_object_hash($menu->getContainer());
        $this->assertEquals($expected, $actual);

        $menu     = $helper('nav2')->menu();
        $actual   = spl_object_hash($nav2);
        $expected = spl_object_hash($menu->getContainer());
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group #3859
     */
    public function testMultipleNavigationsWithDifferentHelpersAndDifferentContainers()
    {
        $sm   = new ServiceManager();
        $nav1 = new Container();
        $nav2 = new Container();
        $sm->setService('nav1', $nav1);
        $sm->setService('nav2', $nav2);

        $helper = new Navigation();
        $helper->setServiceLocator($sm);

        $menu     = $helper('nav1')->menu();
        $actual   = spl_object_hash($nav1);
        $expected = spl_object_hash($menu->getContainer());
        $this->assertEquals($expected, $actual);

        $breadcrumbs = $helper('nav2')->breadcrumbs();
        $actual      = spl_object_hash($nav2);
        $expected    = spl_object_hash($breadcrumbs->getContainer());
        $this->assertEquals($expected, $actual);

        $links    = $helper()->links();
        $expected = spl_object_hash($links->getContainer());
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group #3859
     */
    public function testMultipleNavigationsWithDifferentHelpersAndSameContainer()
    {
        $sm   = new ServiceManager();
        $nav1 = new Container();
        $sm->setService('nav1', $nav1);

        $helper = new Navigation();
        $helper->setServiceLocator($sm);

        // Tests
        $menu     = $helper('nav1')->menu();
        $actual   = spl_object_hash($nav1);
        $expected = spl_object_hash($menu->getContainer());
        $this->assertEquals($expected, $actual);

        $breadcrumbs = $helper('nav1')->breadcrumbs();
        $expected    = spl_object_hash($breadcrumbs->getContainer());
        $this->assertEquals($expected, $actual);

        $links    = $helper()->links();
        $expected = spl_object_hash($links->getContainer());
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group #3859
     */
    public function testMultipleNavigationsWithSameHelperAndSameContainer()
    {
        $sm   = new ServiceManager();
        $nav1 = new Container();
        $sm->setService('nav1', $nav1);

        $helper = new Navigation();
        $helper->setServiceLocator($sm);

        // Test
        $menu     = $helper('nav1')->menu();
        $actual   = spl_object_hash($nav1);
        $expected = spl_object_hash($menu->getContainer());
        $this->assertEquals($expected, $actual);

        $menu     = $helper('nav1')->menu();
        $expected = spl_object_hash($menu->getContainer());
        $this->assertEquals($expected, $actual);

        $menu    = $helper()->menu();
        $expected = spl_object_hash($menu->getContainer());
        $this->assertEquals($expected, $actual);
    }

    /**
     * Returns the contens of the expected $file, normalizes newlines
     * @param  string $file
     * @return string
     */
    protected function _getExpected($file)
    {
        return str_replace("\n", PHP_EOL, parent::_getExpected($file));
    }
}
