<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Di
 */

namespace ZendTest\Di;

use Zend\Di\ServiceLocator;
use PHPUnit_Framework_TestCase as TestCase;

class ServiceLocatorTest extends TestCase
{
    public function setUp()
    {
        $this->services = new ServiceLocator();
    }

    public function testRetrievingUnknownServiceResultsInNullValue()
    {
        $this->assertNull($this->services->get('foo'));
    }

    public function testCanRetrievePreviouslyRegisteredServices()
    {
        $s = new \stdClass;
        $this->services->set('foo', $s);
        $test = $this->services->get('foo');
        $this->assertSame($s, $test);
    }

    public function testRegisteringAServiceUnderAnExistingNameOverwrites()
    {
        $s = new \stdClass();
        $t = new \stdClass();
        $this->services->set('foo', $s);
        $this->services->set('foo', $t);
        $test = $this->services->get('foo');
        $this->assertSame($t, $test);
    }

    public function testRetrievingAServiceMultipleTimesReturnsSameInstance()
    {
        $s = new \stdClass();
        $this->services->set('foo', $s);
        $test1 = $this->services->get('foo');
        $test2 = $this->services->get('foo');
        $this->assertSame($s, $test1);
        $this->assertSame($s, $test2);
        $this->assertSame($test1, $test2);
    }

    public function testRegisteringCallbacksReturnsReturnValueWhenServiceRequested()
    {
        $this->services->set('foo', function () {
            $object = new \stdClass();
            $object->foo = 'FOO';
            return $object;
        });
        $test = $this->services->get('foo');
        $this->assertInstanceOf('stdClass', $test);
        $this->assertEquals('FOO', $test->foo);
    }

    public function testReturnValueOfCallbackIsCachedBetweenRequestsToService()
    {
        $this->services->set('foo', function () {
            $object = new \stdClass();
            $object->foo = 'FOO';
            return $object;
        });
        $test1 = $this->services->get('foo');
        $test2 = $this->services->get('foo');
        $this->assertEquals('FOO', $test1->foo);
        $this->assertSame($test1, $test2);
    }

    public function testParametersArePassedToCallbacks()
    {
        $this->services->set('foo', function () {
            $object = new \stdClass();
            $object->params = func_get_args();
            return $object;
        });

        $params = array('foo', 'bar');
        $test = $this->services->get('foo', $params);
        $this->assertEquals($params, $test->params);
    }

    public function testGetProxiesToMappedMethods()
    {
        $sc = new TestAsset\ContainerExtension();
        $sc->foo = 'FOO';
        $this->assertEquals('FOO', $sc->get('foo'));
    }

    public function testProxiedMethodsReceiveParametersPassedToGet()
    {
        $sc = new TestAsset\ContainerExtension();
        $params = array('foo' => 'FOO');
        $test = $sc->get('params', $params);
        $this->assertEquals($params, $test);
        $this->assertEquals($params, $sc->params);
    }
}
