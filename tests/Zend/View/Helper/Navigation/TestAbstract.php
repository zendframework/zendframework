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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper\Navigation;

use Zend\Navigation,
    Zend\Controller,
    Zend\Acl\Role,
    Zend\Acl\Resource;

/**
 * Base class for navigation view helper tests
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
abstract class TestAbstract extends \PHPUnit_Framework_TestCase
{
    const REGISTRY_KEY = 'Zend_Navigation';

    /**
     * Path to files needed for test
     *
     * @var string
     */
    protected $_files;

    /**
     * Class name for view helper to test
     *
     * @var string
     */
    protected $_helperName;

    /**
     * View helper
     *
     * @var Zend_View_Helper_Navigation_HelperAbstract
     */
    protected $_helper;

    /**
     * The first container in the config file (_files/navigation.xml)
     *
     * @var Zend_Navigation
     */
    protected $_nav1;

    /**
     * The second container in the config file (_files/navigation.xml)
     *
     * @var Zend_Navigation
     */
    protected $_nav2;

    private $_oldControllerDir;

    /**
     * Prepares the environment before running a test
     *
     */
    protected function setUp()
    {
        $cwd = __DIR__;

        // read navigation config
        $this->_files = $cwd . '/_files';
        $config = new \Zend\Config\Xml($this->_files . '/navigation.xml');

        // setup containers from config
        $this->_nav1 = new Navigation\Navigation($config->get('nav_test1'));
        $this->_nav2 = new Navigation\Navigation($config->get('nav_test2'));

        // setup view
        $view = new \Zend\View\View();
        $view->setScriptPath($cwd . '/_files/mvc/views');

        // setup front
        $front = Controller\Front::getInstance();
        $this->_oldControllerDir = $front->getControllerDirectory('test');
        $front->setControllerDirectory($cwd . '/_files/mvc/controllers');

        // create helper
        $this->_helper = new $this->_helperName;
        $this->_helper->setView($view);

        // set nav1 in helper as default
        $this->_helper->setContainer($this->_nav1);
    }

    /**
     * Cleans up the environment after running a test
     *
     */
    protected function tearDown()
    {
        $front = Controller\Front::getInstance();

        if ($this->_oldControllerDir) {
            $front->setControllerDirectory($this->_oldControllerDir, 'test');
        } else {
            $front->removeControllerDirectory('test');
        }
    }

    /**
     * Returns the contens of the exepcted $file
     * @param  string $file
     * @return string
     */
    protected function _getExpected($file)
    {
        return file_get_contents($this->_files . '/expected/' . $file);
    }

    /**
     * Sets up ACL
     *
     * @return Zend_Acl
     */
    protected function _getAcl()
    {
        $acl = new \Zend\Acl\Acl();

        $acl->addRole(new Role\GenericRole('guest'));
        $acl->addRole(new Role\GenericRole('member'), 'guest');
        $acl->addRole(new Role\GenericRole('admin'), 'member');
        $acl->addRole(new Role\GenericRole('special'), 'member');

        $acl->addResource(new Resource\GenericResource('guest_foo'));
        $acl->addResource(new Resource\GenericResource('member_foo'), 'guest_foo');
        $acl->addResource(new Resource\GenericResource('admin_foo', 'member_foo'));
        $acl->addResource(new Resource\GenericResource('special_foo'), 'member_foo');

        $acl->allow('guest', 'guest_foo');
        $acl->allow('member', 'member_foo');
        $acl->allow('admin', 'admin_foo');
        $acl->allow('special', 'special_foo');
        $acl->allow('special', 'admin_foo', 'read');

        return array('acl' => $acl, 'role' => 'special');
    }

    /**
     * Returns translator
     *
     * @return Zend_Translate
     */
    protected function _getTranslator()
    {
        $data = array(
            'Page 1'       => 'Side 1',
            'Page 1.1'     => 'Side 1.1',
            'Page 2'       => 'Side 2',
            'Page 2.3'     => 'Side 2.3',
            'Page 2.3.3.1' => 'Side 2.3.3.1',
            'Home'         => 'Hjem',
            'Go home'      => 'Gå hjem'
        );

        return new \Zend\Translator\Translator('ArrayAdapter', $data, 'nb_NO');
    }
}
