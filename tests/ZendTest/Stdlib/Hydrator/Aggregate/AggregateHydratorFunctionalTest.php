<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ocramius
 * Date: 5/9/13
 * Time: 4:29 PM
 * To change this template use File | Settings | File Templates.
 */

namespace ZendTest\Stdlib\Hydrator\Aggregate;


use PHPUnit_Framework_TestCase;
use Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator;

class AggregateHydratorFunctionalTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Stdlib\Hydrator\Aggregate\AggregateHydrator
     */
    protected $hydrator;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->hydrator = new AggregateHydrator();
    }

    public function testEmptyAggregate()
    {
        $this->markTestIncomplete();
    }

    public function testSingleHydrator()
    {
        $this->markTestIncomplete();
    }

    public function testMultipleHydrators()
    {
        $this->markTestIncomplete();
    }

    public function testStoppedPropagation()
    {
        $this->markTestIncomplete();
    }
}