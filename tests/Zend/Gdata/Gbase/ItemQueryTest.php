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

require_once 'Zend/Gdata/Gbase.php';
require_once 'Zend/Gdata/Gbase/ItemQuery.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Gbase_ItemQueryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->itemQuery = new Zend_Gdata_Gbase_ItemQuery();
    }

    public function testBq()
    {
        $this->itemQuery->resetParameters();
        $this->itemQuery->setBq('[title:PHP]');
        $this->assertEquals($this->itemQuery->getBq(), '[title:PHP]');
    }

    public function testRefine() 
    {
        $this->itemQuery->resetParameters();
        $this->itemQuery->setRefine('true');
        $this->assertEquals($this->itemQuery->getRefine(), 'true');
    }

    public function testContent() 
    {
        $this->itemQuery->resetParameters();
        $this->itemQuery->setContent('stats');
        $this->assertEquals($this->itemQuery->getContent(), 'stats');
    }

    public function testOrderBy() 
    {
        $this->itemQuery->resetParameters();
        $this->itemQuery->setOrderBy('relevancy');
        $this->assertEquals($this->itemQuery->getOrderBy(), 'relevancy');
    }

    public function testSortOrder() 
    {
        $this->itemQuery->resetParameters();
        $this->itemQuery->setOrderBy('descending');
        $this->assertEquals($this->itemQuery->getOrderBy(), 'descending');
    }

    public function testCrowdBy() 
    {
        $this->itemQuery->resetParameters();
        $this->itemQuery->setCrowdBy('attribute:5,content:2,url');
        $this->assertEquals($this->itemQuery->getCrowdBy(), 'attribute:5,content:2,url');
    }

    public function testAdjust() 
    {
        $this->itemQuery->resetParameters();
        $this->itemQuery->setAdjust('true');
        $this->assertEquals($this->itemQuery->getAdjust(), 'true');
    }
}
