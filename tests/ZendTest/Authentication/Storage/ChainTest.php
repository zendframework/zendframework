<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Uri
 */

namespace ZendTest\Authentication\Storage;

use Zend\Authentication\Storage\Chain,
    Zend\Authentication\Storage\StorageInterface,
    Zend\Authentication\Storage\NonPersistent;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
  * @group      Zend_Auth
 */
class ChainTest extends TestCase
{
    const ID = 1337;

    /**
     * Ensure chain without storage behavious as empty storage.
     */
    public function testEmptyChain()
    {
        $chain = new Chain;

        $this->assertTrue($chain->isEmpty());
    }

    /**
     * Ensure chain with single empty storage behavious as expected.
     */
    public function testSingularChainEmpty()
    {
        $chain = new Chain;
        $chain->add($this->storageFactory());

        $this->assertTrue($chain->isEmpty());
    }

    /**
     * Ensure chain with single non-empty storage behavious as expected.
     */
    public function testSingularChainNonEmpty()
    {
        $chain = new Chain;
        $chain->add($this->storageFactory(self::ID));

        $this->assertFalse($chain->isEmpty());
        $this->assertEquals(self::ID, $chain->read());
    }

    /**
     * Ensure the priority of storage engines is correctly used.
     */
    public function testChainPriority()
    {
        $storageA = $this->storageFactory();
        $storageB = $this->storageFactory(self::ID);

        $chain = new Chain;
        $chain->add($storageA); // Defaults to 1
        $chain->add($storageB, 10);
        $chain->isEmpty();

        // Storage B has higher priority AND is non-empty. Thus
        // storage A should been used at all and remain empty.
        $this->assertTrue($storageA->isEmpty());
    }

    /**
     * Ensure that a chain with empty storages is considered empty and
     * won't populated any of its underlying storages.
     */
    public function testEmptyChainIsEmpty()
    {
        $emptyStorageA = $this->storageFactory();
        $emptyStorageB = $this->storageFactory();

        $chain = new Chain;
        $chain->add($emptyStorageA);
        $chain->add($emptyStorageB);

        $this->assertTrue($chain->isEmpty());

        // Storage A and B remain empty
        $this->assertTrue($emptyStorageA->isEmpty());
        $this->assertTrue($emptyStorageB->isEmpty());
    }

    /**
     * Ensure that chain will yield non-empty if one of its underlying storage
     * engines is non-empty.
     *
     * Make sure that storage engines with higher priority then the first non-empty
     * storage engine get populated with that same content.
     */
    public function testSuccessfullReadWillPopulateStoragesWithHigherPriority()
    {
        $emptyStorageA = $this->storageFactory();
        $emptyStorageB = $this->storageFactory();
        $storageC = $this->storageFactory(self::ID);
        $emptyStorageD = $this->storageFactory();

        $chain = new Chain;
        $chain->add($emptyStorageA);
        $chain->add($emptyStorageB);
        $chain->add($storageC);
        $chain->add($emptyStorageD);

        // Chain is non empty
        $this->assertFalse($chain->isEmpty());
        $this->assertEquals(self::ID, $chain->read());

        // Storage A and B are filled
        $this->assertFalse($emptyStorageA->isEmpty());
        $this->assertEquals(self::ID, $emptyStorageA->read());
        $this->assertFalse($emptyStorageA->isEmpty());
        $this->assertEquals(self::ID, $emptyStorageB->read());

        // Storage C and D remain identical
        $this->assertFalse($storageC->isEmpty());
        $this->assertEquals(self::ID, $storageC->read());
        $this->assertTrue($emptyStorageD->isEmpty());
    }

    /**
     * @param  mixed            $identity
     * @return StorageInterface
     */
    protected function storageFactory($identity = null)
    {
        $storage = new NonPersistent();

        if ($identity !== null) {
            $storage->write($identity);
        }

        return $storage;
    }
}
