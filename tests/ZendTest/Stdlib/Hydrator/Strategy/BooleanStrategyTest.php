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

    public function testExtractThrowsExceptionOnWrongParameter()
    {
        $this->setExpectedException('Zend\Stdlib\Exception\InvalidArgumentException', 'Unable to extract');
        $hydrator = new BooleanStrategy(1, 0);
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
