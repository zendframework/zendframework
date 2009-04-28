<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Acl.php';
require_once 'Zend/Acl/Resource.php';
require_once 'Zend/Acl/Role.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Config/Xml.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Translate.php';
require_once 'Zend/View.php';
require_once 'Zend/Navigation.php';

/**
 * Base class for navigation view helper tests
 *
 */
abstract class Zend_View_Helper_Navigation_TestAbstract
    extends PHPUnit_Framework_TestCase
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
        $cwd = dirname(__FILE__);

        // read navigation config
        $this->_files = $cwd . '/_files';
        $config = new Zend_Config_Xml($this->_files . '/navigation.xml');

        // setup containers from config
        $this->_nav1 = new Zend_Navigation($config->get('nav_test1'));
        $this->_nav2 = new Zend_Navigation($config->get('nav_test2'));

        // setup view
        $view = new Zend_View();
        $view->setScriptPath($cwd . '/_files/mvc/views');

        // setup front
        $front = Zend_Controller_Front::getInstance();
        $this->_oldControllerDir = $front->getControllerDirectory('test');
        $front->setControllerDirectory($cwd . '/_files/mvc/controllers');

        // create helper
        $this->_helper = new $this->_helperName();
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
        $front = Zend_Controller_Front::getInstance();

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
        $acl = new Zend_Acl();

        $acl->addRole(new Zend_Acl_Role('guest'));
        $acl->addRole(new Zend_Acl_Role('member'), 'guest');
        $acl->addRole(new Zend_Acl_Role('admin'), 'member');
        $acl->addRole(new Zend_Acl_Role('special'), 'member');

        $acl->add(new Zend_Acl_Resource('guest_foo'));
        $acl->add(new Zend_Acl_Resource('member_foo'), 'guest_foo');
        $acl->add(new Zend_Acl_Resource('admin_foo', 'member_foo'));
        $acl->add(new Zend_Acl_Resource('special_foo'), 'member_foo');

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

        return new Zend_Translate('array', $data, 'nb_NO');
    }
}