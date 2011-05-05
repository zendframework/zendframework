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
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Controller\Router;
use Zend\Controller\Router\PriorityList;
use ZendTest\Controller\Router\TestAsset\DummyRoute;

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Controller
 * @group      Zend_Controller_Router
 */
class PriorityListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PriorityList
     */
    protected $list;

    public function setUp()
    {
        $this->list = new PriorityList();
    }

    public function testInsert()
    {
        $this->list->insert('foo', new DummyRoute(), 0);

        $this->assertEquals(1, count($this->list));

        foreach ($this->list as $key => $value) {
            $this->assertEquals('foo', $key);
        }
    }

    public function testRemove()
    {
        $this->list->insert('foo', new DummyRoute(), 0);
        $this->list->insert('bar', new DummyRoute(), 0);

        $this->assertEquals(2, count($this->list));

        $this->list->remove('foo');

        $this->assertEquals(1, count($this->list));
    }
    
    public function testGet()
    {
        $route = new DummyRoute();
        
        $this->list->insert('foo', $route, 0);
        
        $this->assertEquals($route, $this->list->get('foo'));
        $this->assertNull($this->list->get('bar'));
    }

    public function testLIFOOnly()
    {
        $this->list->insert('foo', new DummyRoute(), 0);
        $this->list->insert('bar', new DummyRoute(), 0);
        $this->list->insert('baz', new DummyRoute(), 0);

        $order = array();

        foreach ($this->list as $key => $value) {
            $orders[] = $key;
        }

        $this->assertEquals(array('baz', 'bar', 'foo'), $orders);
    }

    public function testPriorityOnly()
    {
        $this->list->insert('foo', new DummyRoute(), 1);
        $this->list->insert('bar', new DummyRoute(), 0);
        $this->list->insert('baz', new DummyRoute(), 2);

        $order = array();

        foreach ($this->list as $key => $value) {
            $orders[] = $key;
        }

        $this->assertEquals(array('baz', 'foo', 'bar'), $orders);
    }

    public function testLIFOWithPriority()
    {
        $this->list->insert('foo', new DummyRoute(), 0);
        $this->list->insert('bar', new DummyRoute(), 0);
        $this->list->insert('baz', new DummyRoute(), 1);

        $order = array();

        foreach ($this->list as $key => $value) {
            $orders[] = $key;
        }

        $this->assertEquals(array('baz', 'bar', 'foo'), $orders);
    }
}
