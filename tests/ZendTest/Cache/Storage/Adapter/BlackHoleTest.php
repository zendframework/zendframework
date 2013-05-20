<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\BlackHole;
use Zend\Cache\StorageFactory;

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class BlackHoleAdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The storage adapter
     *
     * @var StorageInterface
     */
    protected $storage;

    public function setUp()
    {
        $this->storage = StorageFactory::adapterFactory('BlackHole');
    }

    public function testGetOptions()
    {
        $options = $this->storage->getOptions();
        $this->assertInstanceOf('Zend\Cache\Storage\Adapter\AdapterOptions', $options);
    }

    public function testSetOptions()
    {
        $this->storage->setOptions(array('namespace' => 'test'));
        $this->assertSame('test', $this->storage->getOptions()->getNamespace());
    }

    public function testGetCapabilities()
    {
        $capabilities = $this->storage->getCapabilities();
        $this->assertInstanceOf('Zend\Cache\Storage\Capabilities', $capabilities);
    }

    public function testSingleStorageOperatios()
    {
        $this->assertFalse($this->storage->setItem('test', 1));
        $this->assertFalse($this->storage->addItem('test', 1));
        $this->assertFalse($this->storage->replaceItem('test', 1));
        $this->assertFalse($this->storage->touchItem('test'));
        $this->assertFalse($this->storage->incrementItem('test', 1));
        $this->assertFalse($this->storage->decrementItem('test', 1));
        $this->assertFalse($this->storage->hasItem('test'));
        $this->assertNull($this->storage->getItem('test', $success));
        $this->assertFalse($success);
        $this->assertFalse($this->storage->getMetadata('test'));
        $this->assertFalse($this->storage->removeItem('test'));
    }

    public function testMultiStorageOperatios()
    {
        $this->assertSame(array('test'), $this->storage->setItems(array('test' => 1)));
        $this->assertSame(array('test'), $this->storage->addItems(array('test' => 1)));
        $this->assertSame(array('test'), $this->storage->replaceItems(array('test' => 1)));
        $this->assertSame(array('test'), $this->storage->touchItems(array('test')));
        $this->assertSame(array(), $this->storage->incrementItems(array('test' => 1)));
        $this->assertSame(array(), $this->storage->decrementItems(array('test' => 1)));
        $this->assertSame(array(), $this->storage->hasItems(array('test')));
        $this->assertSame(array(), $this->storage->getItems(array('test')));
        $this->assertSame(array(), $this->storage->getMetadatas(array('test')));
        $this->assertSame(array('test'), $this->storage->removeItems(array('test')));
    }
}
