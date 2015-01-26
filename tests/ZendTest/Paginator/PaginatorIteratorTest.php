<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Paginator;

use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use Zend\Paginator\PaginatorIterator;

class PaginatorIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIteratorFlattensPaginator()
    {
        $paginator = new Paginator(
            new ArrayAdapter(array('foo', 'bar', 'fiz'))
        );

        $paginator->setItemCountPerPage(2);

        $iterator = new PaginatorIterator($paginator);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertEquals('foo', $iterator->current());
        $this->assertEquals(0, $iterator->key());
        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertEquals('bar', $iterator->current());
        $this->assertEquals(1, $iterator->key());
        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertEquals('fiz', $iterator->current());
        $this->assertEquals(2, $iterator->key());
        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    public function testIteratorReturnsInvalidOnEmptyIterator()
    {
        $paginator = new Paginator(
            new ArrayAdapter(array())
        );

        $iterator = new PaginatorIterator($paginator);

        $iterator->rewind();
        $this->assertFalse($iterator->valid());
    }
}
