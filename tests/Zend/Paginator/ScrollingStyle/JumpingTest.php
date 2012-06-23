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
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Paginator\ScrollingStyle;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Paginator
 */
class JumpingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Paginator\ScrollingStyle\Jumping
     */
    private $_scrollingStyle;
    /**
     * @var \Zend\Paginator\Paginator
     */
    private $_paginator;

    private $_expectedRange;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_scrollingStyle = new \Zend\Paginator\ScrollingStyle\Jumping();
        $this->_paginator = \Zend\Paginator\Paginator::factory(range(1, 101));
        $this->_paginator->setItemCountPerPage(10);
        $this->_paginator->setPageRange(10);
        $this->_expectedRange = array_combine(range(1, 10), range(1, 10));
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_scrollingStyle = null;
        $this->_paginator = null;
        parent::tearDown();
    }

    public function testGetsPagesInRangeForFirstPage()
    {
        $this->_paginator->setCurrentPageNumber(1);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $this->assertEquals($this->_expectedRange, $actual);
    }

    public function testGetsPagesInRangeForSecondPage()
    {
        $this->_paginator->setCurrentPageNumber(2);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $this->assertEquals($this->_expectedRange, $actual);
    }

    public function testGetsPagesInRangeForSecondLastPage()
    {
        $this->_paginator->setCurrentPageNumber(10);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $this->assertEquals($this->_expectedRange, $actual);
    }

    public function testGetsPagesInRangeForLastPage()
    {
        $this->_paginator->setCurrentPageNumber(11);
        $actual = $this->_scrollingStyle->getPages($this->_paginator);
        $expected = array(11 => 11);
        $this->assertEquals($expected, $actual);
    }

    public function testGetsNextAndPreviousPageForFirstPage()
    {
        $this->_paginator->setCurrentPageNumber(1);
        $pages = $this->_paginator->getPages('Jumping');

        $this->assertEquals(2, $pages->next);
    }

    public function testGetsNextAndPreviousPageForSecondPage()
    {
        $this->_paginator->setCurrentPageNumber(2);
        $pages = $this->_paginator->getPages('Jumping');
        $this->assertEquals(1, $pages->previous);
        $this->assertEquals(3, $pages->next);
    }

    public function testGetsNextAndPreviousPageForMiddlePage()
    {
        $this->_paginator->setCurrentPageNumber(6);
        $pages = $this->_paginator->getPages('Jumping');
        $this->assertEquals(5, $pages->previous);
        $this->assertEquals(7, $pages->next);
    }

    public function testGetsNextAndPreviousPageForSecondLastPage()
    {
        $this->_paginator->setCurrentPageNumber(10);
        $pages = $this->_paginator->getPages('Jumping');
        $this->assertEquals(9, $pages->previous);
        $this->assertEquals(11, $pages->next);
    }

    public function testGetsNextAndPreviousPageForLastPage()
    {
        $this->_paginator->setCurrentPageNumber(11);
        $pages = $this->_paginator->getPages('Jumping');
        $this->assertEquals(10, $pages->previous);
    }
}
