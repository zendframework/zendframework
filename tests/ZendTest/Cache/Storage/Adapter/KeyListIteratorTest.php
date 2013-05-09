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

use Zend\Cache;
use Zend\Cache\Storage\Adapter\Memory as MemoryStorage;
use Zend\Cache\Storage\Adapter\KeyListIterator;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class KeyListIteratorTest extends \PHPUnit_Framework_TestCase
{

    public function testCount()
    {
        $keys = array('key1', 'key2', 'key3');
        $storage = $this->getMock('Zend\Cache\Storage\StorageInterface');
        $iterator = new KeyListIterator($storage, $keys);
        $this->assertEquals(3, $iterator->count());
    }
}
