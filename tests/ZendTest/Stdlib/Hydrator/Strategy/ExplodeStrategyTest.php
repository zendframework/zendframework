<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\ExplodeStrategy;

class ExplodeStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function getExpectedData()
    {
        return array(
            array('foo,bar,dev,null', ',', array('foo', 'bar', 'dev', 'null')),
            array('foo;bar;dev;null', ';', array('foo', 'bar', 'dev', 'null')),
            array('', ',', array('')),
        );
    }

    /**
     * @dataProvider getExpectedData
     */
    public function testHydrate($hydrateValue, $delimiter, $expected)
    {
        $strategy = new ExplodeStrategy($delimiter);
        $this->assertEquals($expected, $strategy->hydrate($hydrateValue));
    }

    /**
     * @dataProvider getExpectedData
     */
    public function testExtract($expected, $delimiter, $extractValue)
    {
        $strategy = new ExplodeStrategy($delimiter);
        $this->assertEquals($expected, $strategy->extract($extractValue));
    }

    public function testGetExceptionWithInvalidArgumentOnHydration()
    {
        $strategy = new ExplodeStrategy();
        $this->setExpectedException('Zend\Stdlib\Hydrator\Strategy\Exception\InvalidArgumentException');
        $strategy->hydrate(array());
    }

    public function testGetExceptionWithInvalidArgumentOnExtraction()
    {
        $strategy = new ExplodeStrategy();
        $this->setExpectedException('Zend\Stdlib\Hydrator\Strategy\Exception\InvalidArgumentException');
        $strategy->extract('');
    }

    public function testGetEmptyArrayWhenHydratingNullValue()
    {
        $strategy = new ExplodeStrategy();
        $this->assertEquals(array(), $strategy->hydrate(null));
    }

    public function testGetExceptionWithEmptyDelimiter()
    {
        $this->setExpectedException('Zend\Stdlib\Hydrator\Strategy\Exception\InvalidArgumentException');
        $strategy = new ExplodeStrategy('');
    }

    public function testGetExceptionWithInvalidDelimiter()
    {
        $this->setExpectedException('Zend\Stdlib\Hydrator\Strategy\Exception\InvalidArgumentException');
        $strategy = new ExplodeStrategy(array());
    }

    public function testHydrateWithExplodeLimit()
    {
        $strategy = new ExplodeStrategy('-', 2);
        $this->assertEquals(array('foo', 'bar-baz-bat'), $strategy->hydrate('foo-bar-baz-bat'));

        $strategy = new ExplodeStrategy('-', '3');
        $this->assertEquals(array('foo', 'bar', 'baz-bat'), $strategy->hydrate('foo-bar-baz-bat'));
    }
}
