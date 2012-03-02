<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Db\Sql\Predicate;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Db\Sql\Predicate\IsNull,
    Zend\Db\Sql\Predicate\Predicate;

class PredicateTest extends TestCase
{
    public function setUp()
    {
        $this->set = new Predicate();
    }

    public function testEqualToCreatesOperatorPredicate()
    {
        $this->set->equalTo('foo.bar', 'bar');
        $parts = $this->set->getWhereParts();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%s = %s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar'), $parts[0]);
    }

    public function testLessThanCreatesOperatorPredicate()
    {
        $this->set->lessThan('foo.bar', 'bar');
        $parts = $this->set->getWhereParts();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%s < %s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar'), $parts[0]);
    }

    public function testGreaterThanCreatesOperatorPredicate()
    {
        $this->set->greaterThan('foo.bar', 'bar');
        $parts = $this->set->getWhereParts();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%s > %s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar'), $parts[0]);
    }

    public function testLessThanOrEqualToCreatesOperatorPredicate()
    {
        $this->set->lessThanOrEqualTo('foo.bar', 'bar');
        $parts = $this->set->getWhereParts();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%s <= %s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar'), $parts[0]);
    }

    public function testGreaterThanOrEqualToCreatesOperatorPredicate()
    {
        $this->set->greaterThanOrEqualTo('foo.bar', 'bar');
        $parts = $this->set->getWhereParts();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%s >= %s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar'), $parts[0]);
    }

    public function testLikeCreatesLikePredicate()
    {
        $this->set->like('foo.bar', 'bar%');
        $parts = $this->set->getWhereParts();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%1$s LIKE %2$s', $parts[0]);
        $this->assertContains(array('foo.bar', 'bar%'), $parts[0]);
    }

    public function testLiteralCreatesLiteralPredicate()
    {
        $this->set->literal('foo.bar = ?', 'bar');
        $parts = $this->set->getWhereParts();
        $this->assertEquals(1, count($parts));
        $this->assertContains('foo.bar = %s', $parts[0]);
        $this->assertContains(array('bar'), $parts[0]);
    }

    public function testIsNullCreatesIsNullPredicate()
    {
        $this->set->isNull('foo.bar');
        $parts = $this->set->getWhereParts();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%1$s IS NULL', $parts[0]);
        $this->assertContains(array('foo.bar'), $parts[0]);
    }

    public function testIsNotNullCreatesIsNotNullPredicate()
    {
        $this->set->isNotNull('foo.bar');
        $parts = $this->set->getWhereParts();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%1$s IS NOT NULL', $parts[0]);
        $this->assertContains(array('foo.bar'), $parts[0]);
    }

    public function testInCreatesInPredicate()
    {
        $this->set->in('foo.bar', array('foo', 'bar'));
        $parts = $this->set->getWhereParts();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%1$s IN (%2$s)', $parts[0]);
        $this->assertContains(array('foo.bar', 'foo', 'bar'), $parts[0]);
    }

    public function testBetweenCreatesBetweenPredicate()
    {
        $this->set->between('foo.bar', 1, 10);
        $parts = $this->set->getWhereParts();
        $this->assertEquals(1, count($parts));
        $this->assertContains('%1$s BETWEEN %2$s AND %3$s', $parts[0]);
        $this->assertContains(array('foo.bar', 1, 10), $parts[0]);
    }

    public function testCanChainPredicateFactoriesBetweenOperators()
    {
        $this->set->isNull('foo.bar')
                  ->or
                  ->isNotNull('bar.baz')
                  ->and
                  ->equalTo('baz.bat', 'foo');
        $parts = $this->set->getWhereParts();
        $this->assertEquals(5, count($parts));

        $this->assertContains('%1$s IS NULL', $parts[0]);
        $this->assertContains(array('foo.bar'), $parts[0]);

        $this->assertEquals(' OR ', $parts[1]);

        $this->assertContains('%1$s IS NOT NULL', $parts[2]);
        $this->assertContains(array('bar.baz'), $parts[2]);

        $this->assertEquals(' AND ', $parts[3]);

        $this->assertContains('%s = %s', $parts[4]);
        $this->assertContains(array('baz.bat', 'foo'), $parts[4]);
    }

    public function testCanNestPredicates()
    {
        $this->set->isNull('foo.bar')
                  ->nest
                  ->isNotNull('bar.baz')
                  ->and
                  ->equalTo('baz.bat', 'foo')
                  ->unnest;
        $parts = $this->set->getWhereParts();

        $this->assertEquals(7, count($parts));

        $this->assertContains('%1$s IS NULL', $parts[0]);
        $this->assertContains(array('foo.bar'), $parts[0]);

        $this->assertEquals(' AND ', $parts[1]);

        $this->assertEquals('(', $parts[2]);

        $this->assertContains('%1$s IS NOT NULL', $parts[3]);
        $this->assertContains(array('bar.baz'), $parts[3]);

        $this->assertEquals(' AND ', $parts[4]);

        $this->assertContains('%s = %s', $parts[5]);
        $this->assertContains(array('baz.bat', 'foo'), $parts[5]);
        
        $this->assertEquals(')', $parts[6]);
    }
}
