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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Http/Client.php';
require_once 'Zend/Gdata/Query.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_QueryTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testSetAndGetAlt()
    {
        $query = new Zend_Gdata_Query();
        $query->setAlt('rss');
        $this->assertEquals('rss', $query->alt);
        $this->assertContains('alt=rss', $query->getQueryUrl());
    }

    public function testSetAndGetUpdatedMax()
    {
        $query = new Zend_Gdata_Query();
        $query->setUpdatedMax('2007-01-01');
        $this->assertEquals('2007-01-01', $query->getUpdatedMax());
        $this->assertContains('updated-max=2007-01-01', $query->getQueryUrl());
    }

    public function testSetAndGetUpdatedMin()
    {
        $query = new Zend_Gdata_Query();
        $query->setUpdatedMin('2007-01-01');
        $this->assertEquals('2007-01-01', $query->getUpdatedMin());
        $this->assertContains('updated-min=2007-01-01', $query->getQueryUrl());
    }

    public function testSetAndGetPublishedMax()
    {
        $query = new Zend_Gdata_Query();
        $query->setPublishedMax('2007-01-01');
        $this->assertEquals('2007-01-01', $query->getPublishedMax());
        $this->assertContains('published-max=2007-01-01',
            $query->getQueryUrl());
    }

    public function testSetAndGetPublishedMin()
    {
        $query = new Zend_Gdata_Query();
        $query->setPublishedMin('2007-01-01');
        $this->assertEquals('2007-01-01', $query->getPublishedMin());
        $this->assertContains('published-min=2007-01-01', 
                $query->getQueryUrl());
    }

    public function testSetAndGetAuthor()
    {
        $query = new Zend_Gdata_Query();
        $query->setAuthor('My Name');
        $this->assertEquals('My Name', $query->getAuthor());
        $this->assertContains('author=My+Name', $query->getQueryUrl());
    }

    public function testSetAndGetMaxResults()
    {
        $query = new Zend_Gdata_Query();
        $query->setMaxResults('300');
        $this->assertEquals('300', $query->getMaxResults());
        $this->assertContains('max-results=300', $query->getQueryUrl());
    }

    public function testSetAndGetGenericParam()
    {
        $query = new Zend_Gdata_Query();
        $query->setParam('fw', 'zend');
        $this->assertEquals('zend', $query->getParam('fw'));
        $this->assertContains('fw=zend', $query->getQueryUrl());
    }

    public function testSetAndGetFullTextQuery()
    {
        $query = new Zend_Gdata_Query();
        $query->setQuery('geek events');
        $this->assertEquals('geek events', $query->getQuery());
        $this->assertContains('q=geek+events', $query->getQueryUrl());
    }

    public function testSetAndGetStartIndex()
    {
        $query = new Zend_Gdata_Query();
        $query->setStartIndex(12);
        $this->assertEquals(12, $query->getStartIndex());
        $this->assertContains('start-index=12', $query->getQueryUrl()); 
    }

}
