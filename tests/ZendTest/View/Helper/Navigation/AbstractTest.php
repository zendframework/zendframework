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

use Zend\Navigation\Navigation;
use Zend\Config\Factory as ConfigFactory;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\ServiceManager\ServiceManager;
use Zend\I18n\Translator\Translator;
use Zend\View\Renderer\PhpRenderer;
use ZendTest\View\Helper\TestAsset;

/**
 * Base class for navigation view helper tests
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    const REGISTRY_KEY = 'Zend_Navigation';

    /**
     * @var
     */
    protected $serviceManager;

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
     * The first container in the config file (files/navigation.xml)
     *
     * @var Zend_Navigation
     */
    protected $_nav1;

    /**
     * The second container in the config file (files/navigation.xml)
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

        // setup service manager
        $smConfig = array(
            'modules'                 => array(),
            'module_listener_options' => array(
                'config_cache_enabled' => false,
                'cache_dir'            => 'data/cache',
                'module_paths'         => array(),
                'extra_config'         => array(
                    'service_manager' => array(
                        'factories' => array(
                            'Config' => function () use ($config) {
                                return array(
                                    'navigation' => array(
                                        'default' => $config->get('nav_test1'),
                                    ),
                                );
                            }
                        ),
                    ),
                ),
            ),
        );

        $sm = $this->serviceManager = new ServiceManager(new ServiceManagerConfig);
        $sm->setService('ApplicationConfig', $smConfig);
        $sm->get('ModuleManager')->loadModules();
        $sm->get('Application')->bootstrap();
        $sm->setFactory('Navigation', 'Zend\Navigation\Service\DefaultNavigationFactory');

        $sm->setService('nav1', $this->_nav1);
        $sm->setService('nav2', $this->_nav2);

        $app = $this->serviceManager->get('Application');
        $app->getMvcEvent()->setRouteMatch(new RouteMatch(array(
            'controller' => 'post',
            'action'     => 'view',
            'id'         => '1337',
        )));
    }

    /**
     * Returns the contens of the expected $file
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
        $loader = new TestAsset\ArrayTranslator();
        $loader->translations = array(
            'Page 1'       => 'Side 1',
            'Page 1.1'     => 'Side 1.1',
            'Page 2'       => 'Side 2',
            'Page 2.3'     => 'Side 2.3',
            'Page 2.3.3.1' => 'Side 2.3.3.1',
            'Home'         => 'Hjem',
            'Go home'      => 'Gå hjem'
        );
        $translator = new Translator();
        $translator->getPluginManager()->setService('default', $loader);
        $translator->addTranslationFile('default', null);
        return $translator;
    }
}
