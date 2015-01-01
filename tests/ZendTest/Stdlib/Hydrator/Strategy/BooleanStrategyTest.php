<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\BooleanStrategy;

/**
 * Tests for {@see \Zend\Stdlib\Hydrator\Strategy\BooleanStrategy}
 *
 * @covers \Zend\Stdlib\Hydrator\Strategy\BooleanStrategy
 */
class BooleanStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorWithValidInteger()
    {
        $this->assertInstanceOf('Zend\Stdlib\Hydrator\Strategy\BooleanStrategy', new BooleanStrategy(1, 0));
    }

    public function testConstructorWithValidString()
    {
        $this->assertInstanceOf('Zend\Stdlib\Hydrator\Strategy\BooleanStrategy', new BooleanStrategy('true', 'false'));
    }

    public function testExceptionOnWrongTrueValueInConstructor()
    {
        $this->setExpectedException(
            'Zend\Stdlib\Exception\InvalidArgumentException',
            'Expected int or string as $trueValue.'
        );

        new BooleanStrategy(true, 0);
    }

    public function testExceptionOnWrongFalseValueInConstructor()
    {
        $this->setExpectedException(
            'Zend\Stdlib\Exception\InvalidArgumentException',
            'Expected int or string as $falseValue.'
        );

        new BooleanStrategy(1, false);
    }

    public function testExtractString()
    {
        $hydrator = new BooleanStrategy('true', 'false');
        $this->assertEquals('true', $hydrator->extract(true));
        $this->assertEquals('false', $hydrator->extract(false));
    }

    public function testExtractInteger()
    {
        $hydrator = new BooleanStrategy(1, 0);

        $this->assertEquals(1, $hydrator->extract(true));
        $this->assertEquals(0, $hydrator->extract(false));
    }

    public function testExtractThrowsExceptionOnUnknownValue()
    {
        $hydrator = new BooleanStrategy(1, 0);

        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException', 'Unable to extract');

        $hydrator->extract(5);
    }

    public function testHydrateString()
    {
        $hydrator = new BooleanStrategy('true', 'false');
        $this->assertEquals(true, $hydrator->hydrate('true'));
        $this->assertEquals(false, $hydrator->hydrate('false'));
    }

    public function testHydrateInteger()
    {
        $hydrator = new BooleanStrategy(1, 0);
        $this->assertEquals(true, $hydrator->hydrate(1));
        $this->assertEquals(false, $hydrator->hydrate(0));
    }

    public function testHydrateUnexpectedValueThrowsException()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException', 'Unexpected value');
        $hydrator = new BooleanStrategy(1, 0);
        $hydrator->hydrate(2);
    }

    public function testHydrateInvalidArgument()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException', 'Unable to hydrate');
        $hydrator = new BooleanStrategy(1, 0);
        $hydrator->hydrate(new \stdClass());
    }
}
