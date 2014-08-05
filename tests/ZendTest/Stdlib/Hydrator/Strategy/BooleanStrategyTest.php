<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\BooleanStrategy;

class BooleanStrategyTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Zend\Stdlib\Hydrator\Strategy\BooleanStrategy::__construct
     */
    public function testNoExceptionOnValidInteger()
    {
        $hydrator = new BooleanStrategy(1, 0);
    }

    /**
     * @covers Zend\Stdlib\Hydrator\Strategy\BooleanStrategy::__construct
     */
    public function testNoExceptionOnValidStringValues()
    {
        $hydrator = new BooleanStrategy('true', 'false');
    }

    /**
     * @covers Zend\Stdlib\Hydrator\Strategy\BooleanStrategy::__construct
     */
    public function testExceptionOnWrongTrueValueInConstructor()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException', 'Expected int or string as $trueValue.');
        $hydrator = new BooleanStrategy(true, 0);
    }

    /**
     * @covers Zend\Stdlib\Hydrator\Strategy\BooleanStrategy::__construct
     */
    public function testExceptionOnWrongFalseValueInConstructor()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException', 'Expected int or string as $falseValue.');
        $hydrator = new BooleanStrategy(1, false);
    }

    /**
     * @covers Zend\Stdlib\Hydrator\Strategy\BooleanStrategy::extract
     */
    public function testExtractString()
    {
        $hydrator = new BooleanStrategy('true', 'false');
        $this->assertEquals('true', $hydrator->extract(true));
        $this->assertEquals('false', $hydrator->extract(false));
    }

    /**
     * @covers Zend\Stdlib\Hydrator\Strategy\BooleanStrategy::extract
     */
    public function testExtractInteger()
    {
        $hydrator = new BooleanStrategy(1, 0);
        $this->assertEquals(1, $hydrator->extract(true));
        $this->assertEquals(0, $hydrator->extract(false));
    }

    /**
     * @covers Zend\Stdlib\Hydrator\Strategy\BooleanStrategy::extract
     */
    public function testExtractThrowsExceptionOnWrongParameter()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException', 'Unable to extract');
        $hydrator = new BooleanStrategy(1, 0);
        $hydrator->extract(5);
    }

    /**
     * @covers Zend\Stdlib\Hydrator\Strategy\BooleanStrategy::hydrate
     */
    public function testHydrateString()
    {
        $hydrator = new BooleanStrategy('true', 'false');
        $this->assertEquals(true, $hydrator->hydrate('true'));
        $this->assertEquals(false, $hydrator->hydrate('false'));
    }

    /**
     * @covers Zend\Stdlib\Hydrator\Strategy\BooleanStrategy::hydrate
     */
    public function testHydrateInteger()
    {
        $hydrator = new BooleanStrategy(1, 0);
        $this->assertEquals(true, $hydrator->hydrate(1));
        $this->assertEquals(false, $hydrator->hydrate(0));
    }

    /**
     * @covers Zend\Stdlib\Hydrator\Strategy\BooleanStrategy::hydrate
     */
    public function testHydrateUnexpectedValueThrowsException()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException', 'Unexpected value');
        $hydrator = new BooleanStrategy(1, 0);
        $hydrator->hydrate(2);
    }

    /**
     * @covers Zend\Stdlib\Hydrator\Strategy\BooleanStrategy::hydrate
     */
    public function testHydrateInvalidArgument()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException', 'Unable to hydrate');
        $hydrator = new BooleanStrategy(1, 0);
        $hydrator->hydrate(new \stdClass());
    }
}
