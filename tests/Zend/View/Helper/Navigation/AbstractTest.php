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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper\Navigation;

use Zend\Navigation\Navigation,
    Zend\Acl\Acl,
    Zend\Acl\Role\GenericRole,
    Zend\Acl\Resource\GenericResource,
    Zend\Config\Factory as ConfigFactory,
    Zend\Translator\Translator,
    Zend\View\Renderer\PhpRenderer;

/**
 * Base class for navigation view helper tests
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
abstract class AbstractTest extends \PHPUnit_Framework_TestCase
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
     * @var Zend\View\Helper\Navigation\AbstractHelper
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
     * @var Navigation\Navigation
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
        $config = ConfigFactory::fromFile($this->_files . '/navigation.xml', true);

        // setup containers from config
        $this->_nav1 = new Navigation($config->get('nav_test1'));
        $this->_nav2 = new Navigation($config->get('nav_test2'));

        // setup view
        $view = new PhpRenderer();
        $view->resolver()->addPath($cwd . '/_files/mvc/views');

        // create helper
        $this->_helper = new $this->_helperName;
        $this->_helper->setView($view);

        // set nav1 in helper as default
        $this->_helper->setContainer($this->_nav1);
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
     * @return Acl
     */
    protected function _getAcl()
    {
        $acl = new Acl();

        $acl->addRole(new GenericRole('guest'));
        $acl->addRole(new GenericRole('member'), 'guest');
        $acl->addRole(new GenericRole('admin'), 'member');
        $acl->addRole(new GenericRole('special'), 'member');

        $acl->addResource(new GenericResource('guest_foo'));
        $acl->addResource(new GenericResource('member_foo'), 'guest_foo');
        $acl->addResource(new GenericResource('admin_foo', 'member_foo'));
        $acl->addResource(new GenericResource('special_foo'), 'member_foo');

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
     * @return Translator
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

        return new Translator('ArrayAdapter', $data, 'nb_NO');
    }
}
