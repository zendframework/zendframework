<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\Parameters;

class ParametersTest extends \PHPUnit_Framework_TestCase
{

    public function testParametersConstructionAndClassStructure()
    {
        $parameters = new Parameters();
        $this->assertInstanceOf('Zend\Stdlib\ParametersInterface', $parameters);
        $this->assertInstanceOf('ArrayObject', $parameters);
        $this->assertInstanceOf('ArrayAccess', $parameters);
        $this->assertInstanceOf('Countable', $parameters);
        $this->assertInstanceOf('Serializable', $parameters);
        $this->assertInstanceOf('Traversable', $parameters);
    }

    public function testParametersPersistNameAndValues()
    {
        $parameters = new Parameters(array('foo' => 'bar'));
        $this->assertEquals('bar', $parameters['foo']);
        $this->assertEquals('bar', $parameters->foo);
        $parameters->offsetSet('baz', 5);
        $this->assertEquals(5, $parameters->baz);

        $parameters->fromArray(array('bar' => 'foo'));
        $this->assertEquals('foo', $parameters->bar);

        $parameters->fromString('bar=foo&five=5');
        $this->assertEquals('foo', $parameters->bar);
        $this->assertEquals('5', $parameters->five);
        $this->assertEquals(array('bar' => 'foo', 'five' => '5'), $parameters->toArray());
        $this->assertEquals('bar=foo&five=5', $parameters->toString());

        $parameters->fromArray(array());
        $parameters->set('foof', 'barf');
        $this->assertEquals('barf', $parameters->get('foof'));
        $this->assertEquals('barf', $parameters->foof);

    }

    public function testParametersOffsetgetReturnsNullIfNonexistentKeyIsProvided()
    {
        $parameters = new Parameters;
        $this->assertNull($parameters->foo);
    }

    public function testParametersGetReturnsDefaultValueIfNonExistent()
    {
        $parameters = new Parameters();

        $this->assertEquals(5, $parameters->get('nonExistentProp', 5));
    }

}
