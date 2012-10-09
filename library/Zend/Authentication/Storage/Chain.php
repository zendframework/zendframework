<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Authentication
 */

namespace Zend\Authentication\Storage;

use Zend\Stdlib\PriorityQueue;
use Zend\Authentication\Storage\StorageInterface;

/**
 * @category   Zend
 * @package    Zend_Authentication
 * @subpackage Storage
 */
class Chain implements StorageInterface
{
    /**
     * Contains all storages that this authentication method uses. A storage
     * placed in the priority queue with a higher priority is always used
     * before using a storage with a lower priority.
     * 
     * @var PriorityQueue
     */
    protected $storages;
    
    /**
     * Initializes the priority queue that contains storages.
     */
    public function __construct()
    {
        $this->storages = new PriorityQueue();
    }
    
    /**
     * @param StorageInterface $storage
     * @param integer $priority
     */
    public function add( StorageInterface $storage, $priority = 1 )
    {
        $this->storages->insert($storage, $priority);
    }
    
    /**
     * Loop over the queue of storages until a storage is found that is non-empty. If such 
     * storage is not found, then this chain storage itself is empty. 
     * 
     * In case a non-empty storage is found then this chain storage is also non-empty. Report 
     * that, but also make sure that all the storages with a higher priorty that are empty 
     * are filled.
     * 
     * @see StorageInterface::isEmpty()
     */
    public function isEmpty()
    { 
        $storagesWithHigherPriority = array();

        // Loop invariant: $storagesWithHigherPriority contains all storages with a higher priorty  
        // than the current one.
        foreach( $this->storages as $storage )
        {
            if( $storage->isEmpty() )
            {
                $storagesWithHigherPriority[] = $storage;
            }
            else
            { 
                $storageValue = $storage->read();
                foreach( $storagesWithHigherPriority as $higherPriorityStorage )
                    $higherPriorityStorage->write($storageValue);
                    
                return false;
            }
        }
        
        return true;
    }

    /**
     * If the chain is non-empty then the storage with the top priority is guaranteed to be 
     * filled. Return its value. 
     * 
     * @see StorageInterface::read()
     */
    public function read()
    {
        return $this->storages->top()->read();
    }

    /**
     * Write the new $contents to all storages in the chain.
     * 
     * @see StorageInterface::write()
     */
    public function write( $contents )
    {
        foreach( $this->storages as $storage )
            $storage->write($contents);
    }

    /**
     * Clear all storages in the chain.
     * 
     * @see StorageInterface::clear()
     */
    public function clear()
    {
        foreach( $this->storages as $storage )
            $storage->clear();
    }
}