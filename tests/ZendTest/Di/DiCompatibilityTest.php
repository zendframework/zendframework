<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Di;

use Zend\Di\Config;
use Zend\Di\Definition;
use Zend\Di\DefinitionList;
use Zend\Di\Di;
use Zend\Di\InstanceManager;

class DiCompatibilityTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @dataProvider providesSimpleClasses
     * @param string $class
     */
    public function testDiSimple($class)
    {
        $di = new Di();

        $bareObject = new $class;

        $diObject = $di->get($class);

        $this->assertInstanceOf($class, $bareObject, 'Test instantiate simple');
        $this->assertInstanceOf($class, $diObject, 'Test $di->get');
    }

    /**
     * provides known classes invokable without parameters
     *
     * @return array
     */
    public function providesSimpleClasses()
    {
        return array(
            array('Zend\Di\Di'),
            array('Zend\EventManager\EventManager'),
            array('Zend\Filter\Null'),
            array('Zend\Form\Form'),
            array('Zend\Log\Logger'),
            array('Zend\Stdlib\SplStack'),
            array('Zend\View\Model\ViewModel'),
        );
    }

    /**
     *
     * error: Missing argument 1 for $class::__construct()
     * @dataProvider providesClassWithConstructionParameters
     * @expectedException PHPUnit_Framework_Error
     * @param string $class
     */
    public function testRaiseErrorMissingConstructorRequiredParameter($class)
    {
        $bareObject = new $class;
        $this->assertInstanceOf($class, $bareObject, 'Test instantiate simple');
    }

    /**
     *
     * @dataProvider providesClassWithConstructionParameters
     * @expectedException \Zend\Di\Exception\MissingPropertyException
     * @param string $class
     */
    public function testWillThrowExceptionMissingConstructorRequiredParameterWithDi($class)
    {
        $di = new Di();
        $diObject = $di->get($class);
        $this->assertInstanceOf($class, $diObject, 'Test $di->get');
    }

    /**
     *
     * @dataProvider providesClassWithConstructionParameters
     * @param string $class
     */
    public function testCanCreateInstanceWithConstructorRequiredParameter($class, $args)
    {
        $reflection = new \ReflectionClass($class);
        $bareObject = $reflection->newInstanceArgs($args);
        $this->assertInstanceOf($class, $bareObject, 'Test instantiate with constructor required parameters');
    }

    /**
     * @dataProvider providesClassWithConstructionParameters
     * @param string $class
     */
    public function testCanCreateInstanceWithConstructorRequiredParameterWithDi($class, $args)
    {
        $di = new Di();
        $diObject = $di->get($class, $args);
        $this->assertInstanceOf($class, $diObject, 'Test $di->get with constructor required paramters');
    }

    public function providesClassWithConstructionParameters()
    {
        $serviceManager = new \Zend\ServiceManager\ServiceManager;
        $serviceManager->setService('EventManager', new \Zend\EventManager\EventManager);
        $serviceManager->setService('Request', new \stdClass);
        $serviceManager->setService('Response', new \stdClass);

        return array(
            array('Zend\Config\Config', array('array' => array())),
            array('Zend\Db\Adapter\Adapter', array('driver' => array('driver' => 'Pdo_Sqlite'))),
            array('Zend\Mvc\Application', array('configuration' => array(), 'serviceManager' => $serviceManager)),
        );
    }
}
