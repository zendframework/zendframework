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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__) . '/TestAbstract.php';
require_once 'Zend/View/Helper/Navigation.php';

/**
 * Tests Zend_View_Helper_Navigation
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_Navigation_NavigationTest
    extends Zend_View_Helper_Navigation_TestAbstract
{
    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName = 'Zend_View_Helper_Navigation';

    /**
     * View helper
     *
     * @var Zend_View_Helper_Navigation
     */
    protected $_helper;

    public function testHelperEntryPointWithoutAnyParams()
    {
        $returned = $this->_helper->navigation();
        $this->assertEquals($this->_helper, $returned);
        $this->assertEquals($this->_nav1, $returned->getContainer());
    }

    public function testHelperEntryPointWithContainerParam()
    {
        $returned = $this->_helper->navigation($this->_nav2);
        $this->assertEquals($this->_helper, $returned);
        $this->assertEquals($this->_nav2, $returned->getContainer());
    }

    public function testShouldProxyToMenuHelperByDeafult()
    {
        // setup
        $oldReg = null;
        if (Zend_Registry::isRegistered(self::REGISTRY_KEY)) {
            $oldReg = Zend_Registry::get(self::REGISTRY_KEY);
        }
        Zend_Registry::set(self::REGISTRY_KEY, $this->_nav1);
        $this->_helper->setContainer(null);

        // result
        $expected = $this->_getExpected('menu/default1.html');
        $actual = $this->_helper->render();

        // teardown
        Zend_Registry::set(self::REGISTRY_KEY, $oldReg);

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
        $acl = new Zend_Acl();
        $this->_helper->setAcl($acl);
        $this->assertEquals($acl, $this->_helper->getAcl());
    }

    public function testGetAclReturnsAclInstanceSetWithSetDefaultAcl()
    {
        $acl = new Zend_Acl();
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);
        $actual = $this->_helper->getAcl();
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl(null);
        $this->assertEquals($acl, $actual);
    }

    public function testSetDefaultAclAcceptsNull()
    {
        $acl = new Zend_Acl();
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl(null);
        $this->assertNull($this->_helper->getAcl());
    }

    public function testSetDefaultAclAcceptsNoParam()
    {
        $acl = new Zend_Acl();
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl();
        $this->assertNull($this->_helper->getAcl());
    }

    public function testSetRoleAcceptsString()
    {
        $this->_helper->setRole('member');
        $this->assertEquals('member', $this->_helper->getRole());
    }

    public function testSetRoleAcceptsRoleInterface()
    {
        $role = new Zend_Acl_Role('member');
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
        } catch (Zend_View_Exception $e) {
            $this->assertContains('$role must be a string', $e->getMessage());
        }
    }

    public function testSetRoleThrowsExceptionWhenGivenAnArbitraryObject()
    {
        try {
            $this->_helper->setRole(new stdClass());
            $this->fail('An invalid argument was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('$role must be a string', $e->getMessage());
        }
    }

    public function testSetDefaultRoleAcceptsString()
    {
        $expected = 'member';
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($expected);
        $actual = $this->_helper->getRole();
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole(null);
        $this->assertEquals($expected, $actual);
    }

    public function testSetDefaultRoleAcceptsRoleInterface()
    {
        $expected = new Zend_Acl_Role('member');
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole($expected);
        $actual = $this->_helper->getRole();
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole(null);
        $this->assertEquals($expected, $actual);
    }

    public function testSetDefaultRoleAcceptsNull()
    {
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole(null);
        $this->assertNull($this->_helper->getRole());
    }

    public function testSetDefaultRoleAcceptsNoParam()
    {
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole();
        $this->assertNull($this->_helper->getRole());
    }

    public function testSetDefaultRoleThrowsExceptionWhenGivenAnInt()
    {
        try {
            Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole(1337);
            $this->fail('An invalid argument was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (Zend_View_Exception $e) {
            $this->assertContains('$role must be', $e->getMessage());
        }
    }

    public function testSetDefaultRoleThrowsExceptionWhenGivenAnArbitraryObject()
    {
        try {
            Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole(new stdClass());
            $this->fail('An invalid argument was given, but a ' .
                        'Zend_View_Exception was not thrown');
        } catch (Zend_View_Exception $e) {
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
        $nl = Zend_View_Helper_Navigation::EOL;

        $container = new Zend_Navigation(array(
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
                  . '    <li>' . PHP_EOL
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
        $container = new Zend_Navigation(array(
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
}
