<?php
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Zend_Controller_Router_Route */
require_once 'Zend/Controller/Router/Route/Static.php';

/** PHPUnit test case */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_Router_Route_StaticTest extends PHPUnit_Framework_TestCase
{

    public function testStaticMatch()
    {
        $route = new Zend_Controller_Router_Route_Static('users/all');
        $values = $route->match('users/all');

        $this->assertType('array', $values);
    }

    public function testStaticMatchFailure()
    {
        $route = new Zend_Controller_Router_Route_Static('archive/2006');
        $values = $route->match('users/all');

        $this->assertSame(false, $values);
    }

    public function testStaticMatchWithDefaults()
    {
        $route = new Zend_Controller_Router_Route_Static('users/all', 
                    array('controller' => 'ctrl', 'action' => 'act'));
        $values = $route->match('users/all');

        $this->assertType('array', $values);
        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
    }

    public function testStaticUTFMatch()
    {
        $route = new Zend_Controller_Router_Route_Static('żółć');
        $values = $route->match('żółć');

        $this->assertType('array', $values);
    }

    public function testRootRoute()
    {
        $route = new Zend_Controller_Router_Route_Static('/');
        $values = $route->match('');

        $this->assertSame(array(), $values);
    }

    public function testAssemble()
    {
        $route = new Zend_Controller_Router_Route_Static('/about');
        $url = $route->assemble();

        $this->assertSame('about', $url);
    }

    public function testGetDefaults()
    {
        $route = new Zend_Controller_Router_Route_Static('users/all', 
                    array('controller' => 'ctrl', 'action' => 'act'));

        $values = $route->getDefaults();

        $this->assertType('array', $values);
        $this->assertSame('ctrl', $values['controller']);
        $this->assertSame('act', $values['action']);
    }

    public function testGetDefault()
    {
        $route = new Zend_Controller_Router_Route_Static('users/all', 
                    array('controller' => 'ctrl', 'action' => 'act'));

        $this->assertSame('ctrl', $route->getDefault('controller'));
        $this->assertSame(null, $route->getDefault('bogus'));
    }

    public function testGetInstance()
    {
        require_once 'Zend/Config.php';

        $routeConf = array(
            'route' => 'users/all',
            'defaults' => array(
                'controller' => 'ctrl'
            )
        );
        
        $config = new Zend_Config($routeConf);
        $route = Zend_Controller_Router_Route_Static::getInstance($config);
        
        $this->assertType('Zend_Controller_Router_Route_Static', $route);

        $values = $route->match('users/all');

        $this->assertSame('ctrl', $values['controller']);

    }

}
