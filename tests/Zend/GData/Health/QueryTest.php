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
 * @package    Zend_GData_Health
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\Health;

/**
 * @category   Zend
 * @package    Zend_GData_Health
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Health
 */
class QueryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->query = new \Zend\GData\Health\Query();
    }

    public function testDigest()
    {
        $this->query->resetParameters();
        $this->assertEquals(null, $this->query->getDigest());
        $this->query->setDigest('true');
        $this->assertEquals('true', $this->query->getDigest());
        $this->assertContains('digest=true', $this->query->getQueryUrl());
    }

    public function testCategory()
    {
        $this->query->resetParameters();
        $this->query->setCategory('medication');
        $this->assertEquals($this->query->getCategory(), 'medication');

        $this->query->setCategory('medication', 'Lipitor');
        $this->assertEquals($this->query->getCategory(), 'medication/%7Bhttp%3A%2F%2Fschemas.google.com%2Fhealth%2Fitem%7DLipitor');

        $this->query->setCategory('condition', 'Malaria');
        $this->assertEquals($this->query->getCategory(), 'condition/%7Bhttp%3A%2F%2Fschemas.google.com%2Fhealth%2Fitem%7DMalaria');
    }

    public function testGrouped()
    {
        $this->query->resetParameters();
        $this->query->setGrouped('true');
        $this->assertEquals('true', $this->query->getGrouped());
        $this->assertContains('grouped=true', $this->query->getQueryUrl());
    }

    public function testMaxResultsGroup()
    {
        $this->query->resetParameters();

        try {
            $this->query->setMaxResultsGroup(1);
            $this->fail('Expecting to catch Zend\GData\App\InvalidArgumentException');
        } catch (\Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend\GData\App\InvalidArgumentException'),
                'Expecting Zend\GData\App\InvalidArgumentException, got '.get_class($e));
        }

        $this->assertEquals(null, $this->query->getMaxResultsGroup());

        $this->query->setGrouped('true');
        $this->query->setMaxResultsGroup(1);
        $this->assertEquals(1, $this->query->getMaxResultsGroup());
        $this->assertContains('max-results-group=1', $this->query->getQueryUrl());
    }

    public function testMaxResultsInGroup()
    {
        $this->query->resetParameters();

        try {
            $this->query->setMaxResultsInGroup(2);
            $this->fail('Expecting to catch Zend\GData\App\InvalidArgumentException');
        } catch (\Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend\GData\App\InvalidArgumentException'),
                'Expecting Zend\GData\App\InvalidArgumentException, got '.get_class($e));
        }

        $this->query->setGrouped('true');
        $this->assertEquals(null, $this->query->getMaxResultsInGroup());
        $this->query->setMaxResultsInGroup(2);
        $this->assertEquals(2, $this->query->getMaxResultsInGroup());
        $this->assertContains('max-results-in-group=2', $this->query->getQueryUrl());
    }

    public function testStartIndexGroup()
    {
        $this->query->resetParameters();

        $this->assertEquals(null, $this->query->getStartIndexGroup());

        try {
            $this->query->setStartIndexGroup(3);
            $this->fail('Expecting to catch Zend\GData\App\InvalidArgumentException');
        } catch (\Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend\GData\App\InvalidArgumentException'),
                'Expecting Zend\GData\App\InvalidArgumentException, got '.get_class($e));
        }

        $this->query->setGrouped('true');
        $this->query->setStartIndexGroup(3);
        $this->assertEquals(3, $this->query->getStartIndexGroup());
        $this->assertContains('start-index-group=3', $this->query->getQueryUrl());
    }

    public function testStartIndexInGroup()
    {
        $this->query->resetParameters();

        $this->assertEquals(null, $this->query->getStartIndexInGroup());

        try {
            $this->query->setStartIndexInGroup(4);
            $this->fail('Expecting to catch Zend\GData\App\InvalidArgumentException');
        } catch (\Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend\GData\App\InvalidArgumentException'),
                'Expecting Zend\GData\App\InvalidArgumentException, got '.get_class($e));
        }

        $this->query->setGrouped('true');
        $this->query->setStartIndexInGroup(4);
        $this->assertEquals(4, $this->query->getStartIndexInGroup());
        $this->assertContains('start-index-in-group=4', $this->query->getQueryUrl());
    }

}

