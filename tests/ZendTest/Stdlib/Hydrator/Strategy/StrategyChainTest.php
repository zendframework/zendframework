<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\Strategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyChain;
use Zend\Stdlib\Hydrator\Strategy\ClosureStrategy;

class StrategyChainTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyStrategyChainReturnsOriginalValue()
    {
        $chain = new StrategyChain(array());
        $this->assertEquals('something', $chain->hydrate('something'));
        $this->assertEquals('something', $chain->extract('something'));
    }

    public function testWithDefaultPriority()
    {
        $chain = new StrategyChain(array(
            new ClosureStrategy(
                function ($value) {
                    return $value % 12;
                },
                function ($value) {
                    return $value % 3;
                }
            ),
            new ClosureStrategy(
                function ($value) {
                    return $value % 9;
                },
                function ($value) {
                    return $value % 7;
                }
            ),
        ));
        $this->assertEquals(3, $chain->extract(87));
        $this->assertEquals(0, $chain->hydrate(87));

        $chain = new StrategyChain(array(
            array(
                'strategy' => new ClosureStrategy(function ($value) { return $value % 7; }),
            ),
            new ClosureStrategy(function ($value) { return $value % 2; }),
            array(
                'strategy' => new ClosureStrategy(function ($value) { return $value % 9; }),
                'priority' => 4,
            ),
        ));
        $this->assertEquals(1, $chain->extract(80));
    }

    public function testStrategiesAreExecutedAccordingToPriority()
    {
        $chain = new StrategyChain(array(
            array(
                'strategy' => new ClosureStrategy(
                    function ($value) {
                        return $value % 8;
                    },
                    function ($value) {
                        return $value % 8;
                    }
                ),
                'priority' => 100,
            ),
            array(
                'strategy' => new ClosureStrategy(
                    function ($value) {
                        return $value % 3;
                    },
                    function ($value) {
                        return $value % 3;
                    }
                ),
                'priority' => 10,
            ),
        ));
        $this->assertEquals(1, $chain->extract(20));
        $this->assertEquals(2, $chain->hydrate(20));

        $chain = new StrategyChain(array(
            array(
                'strategy' => new ClosureStrategy(
                    function ($value) {
                        return $value % 6;
                    },
                    function ($value) {
                        return $value % 9;
                    }
                ),
                'priority' => 100,
            ),
            array(
                'strategy' => new ClosureStrategy(
                    function ($value) {
                        return $value % 7;
                    },
                    function ($value) {
                        return $value % 4;
                    }
                ),
                'priority' => 110,
            ),
        ));
        $this->assertEquals(2, $chain->extract(30));
        $this->assertEquals(3, $chain->hydrate(30));
    }

    public function testGetExceptionWhenStrategyIsNotProvided()
    {
        $this->setExpectedException('Zend\Stdlib\Hydrator\Strategy\Exception\DomainException');

        $chain = new StrategyChain(array(
            array(
                'priority' => 4,
            ),
        ));
    }

    public function testGetExceptionWithInvalidStrategy()
    {
        $this->setExpectedException('Zend\Stdlib\Hydrator\Strategy\Exception\InvalidArgumentException');

        $chain = new StrategyChain(array(
            array(
                'strategy' => 'asdfsadf',
            ),
        ));
    }
}
