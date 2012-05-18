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

namespace ZendTest\Paginator\Adapter;

use Zend\Paginator\Adapter;
use Zend\Paginator;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Paginator
 */
class NullTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Paginator\Adapter\Array
     */
    private $_adapter;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp ()
    {
        parent::setUp();
        $this->_adapter = new Adapter\Null(101);
    }
    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_adapter = null;
        parent::tearDown();
    }

    public function testGetsItems()
    {
        $actual = $this->_adapter->getItems(0, 10);
        $this->assertEquals(array_fill(0, 10, null), $actual);
    }

    public function testReturnsCorrectCount()
    {
        $this->assertEquals(101, $this->_adapter->count());
    }

    /**
     * @group ZF-3873
     */
    public function testAdapterReturnsCorrectValues()
    {
        $paginator = Paginator\Paginator::factory(2);
        $paginator->setCurrentPageNumber(1);
        $paginator->setItemCountPerPage(5);

        $pages = $paginator->getPages();

        $this->assertEquals(2, $pages->currentItemCount);
        $this->assertEquals(2, $pages->lastItemNumber);

        $paginator = Paginator\Paginator::factory(19);
        $paginator->setCurrentPageNumber(4);
        $paginator->setItemCountPerPage(5);

        $pages = $paginator->getPages();

        $this->assertEquals(4, $pages->currentItemCount);
        $this->assertEquals(19, $pages->lastItemNumber);
    }

    /**
     * @group ZF-4151
     */
    public function testEmptySet() {
        $this->_adapter = new Adapter\Null(0);
        $actual = $this->_adapter->getItems(0, 10);
        $this->assertEquals(array(), $actual);
    }
    
    /**
     * Verify that the fix for ZF-4151 doesn't create an OBO error
     */
    public function testSetOfOne() {
        $this->_adapter = new Adapter\Null(1);
        $actual = $this->_adapter->getItems(0, 10);
        $this->assertEquals(array_fill(0, 1, null), $actual);
    }
}
