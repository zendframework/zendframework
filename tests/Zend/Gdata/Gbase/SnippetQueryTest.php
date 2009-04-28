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
require_once 'Zend/Gdata/Gbase/SnippetQuery.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Gbase_SnippetQueryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->snippetQuery = new Zend_Gdata_Gbase_SnippetQuery();
    }

    public function testBq()
    {
        $this->snippetQuery->resetParameters();
        $this->snippetQuery->setBq('[title:PHP]');
        $this->assertEquals($this->snippetQuery->getBq(), '[title:PHP]');
    }

    public function testRefine() 
    {
        $this->snippetQuery->resetParameters();
        $this->snippetQuery->setRefine('true');
        $this->assertEquals($this->snippetQuery->getRefine(), 'true');
    }

    public function testContent() 
    {
        $this->snippetQuery->resetParameters();
        $this->snippetQuery->setContent('stats');
        $this->assertEquals($this->snippetQuery->getContent(), 'stats');
    }

    public function testOrderBy() 
    {
        $this->snippetQuery->resetParameters();
        $this->snippetQuery->setOrderBy('relevancy');
        $this->assertEquals($this->snippetQuery->getOrderBy(), 'relevancy');
    }

    public function testSortOrder() 
    {
        $this->snippetQuery->resetParameters();
        $this->snippetQuery->setOrderBy('descending');
        $this->assertEquals($this->snippetQuery->getOrderBy(), 'descending');
    }

    public function testCrowdBy() 
    {
        $this->snippetQuery->resetParameters();
        $this->snippetQuery->setCrowdBy('attribute:5,content:2,url');
        $this->assertEquals($this->snippetQuery->getCrowdBy(), 'attribute:5,content:2,url');
    }

    public function testAdjust() 
    {
        $this->snippetQuery->resetParameters();
        $this->snippetQuery->setAdjust('true');
        $this->assertEquals($this->snippetQuery->getAdjust(), 'true');
    }

}
