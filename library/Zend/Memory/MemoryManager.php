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
 * @package    Zend_Memory
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Memory;

use Zend\Cache\Storage\ClearByNamespaceInterface;

use Zend\Cache\Storage\StorageInterface as CacheStorage,
    Zend\Cache\Storage\ClearByNamespaceInterface as ClearByNamespaceCacheStorage,
    Zend\Cache\Storage\FlushableInterface as FlushableCacheStorage;

/**
 * Memory manager
 *
 * This class encapsulates memory menagement operations, when PHP works
 * in limited memory mode.
 *
 * @category   Zend
 * @package    Zend_Memory
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MemoryManager
{
    /**
     * Storage cache object
     *
     * @var CacheStorage
     */
    private $_cache = null;

    /**
     * Memory grow limit.
     * Default value is 2/3 of memory_limit php.ini variable
     * Negative value means no limit
     *
     * @var integer
     */
    private $_memoryLimit = -1;

    /**
     * Minimum value size to be swapped.
     * Default value is 16K
     * Negative value means that memory objects are never swapped
     *
     * @var integer
     */
    private $_minSize = 16384;

    /**
     * Overall size of memory, used by values
     *
     * @var integer
     */
    private $_memorySize = 0;

    /**
     * Id for next Zend_Memory object
     *
     * @var integer
     */
    private $_nextId = 0;

    /**
     * List of candidates to unload
     *
     * It also represents objects access history. Last accessed objects are moved to the end of array
     *
     * array(
     *     <id> => <memory container object>,
     *     ...
     *      )
     *
     * @var array
     */
    private $_unloadCandidates = array();

    /**
     * List of object sizes.
     *
     * This list is used to calculate modification of object sizes
     *
     * array( <id> => <size>, ...)
     *
     * @var array
     */
    private $_sizes = array();

    /**
     * Last modified object
     *
     * It's used to reduce number of calls necessary to trace objects' modifications
     * Modification is not processed by memory manager until we do not switch to another
     * object.
     * So we have to trace only _first_ object modification and do nothing for others
     *
     * @var \Zend\Memory\Container\Movable
     */
    private $_lastModified = null;

    /**
     * Unique memory manager id
     *
     * @var integer
     */
    private $_managerId;

    /**
     * This function is intended to generate unique id, used by memory manager
     */
    private function _generateMemManagerId()
    {
        /**
         * @todo !!!
         * uniqid() php function doesn't really garantee the id to be unique
         * it should be changed by something else
         * (Ex. backend interface should be extended to provide this functionality)
         */
        $this->_managerId = str_replace('.', '_', uniqid('ZendMemManager', true)) . '_';
    }

    /**
     * Memory manager constructor
     *
     * If cache is not specified, then memory objects are never swapped
     *
     * @param  CacheStorage $cache
     * @return void
     */
    public function __construct(CacheStorage $cache = null)
    {
        if ($cache === null) {
            return;
        }

        $this->_cache = $cache;
        $this->_generateMemManagerId();

        $memoryLimitStr = trim(ini_get('memory_limit'));
        if ($memoryLimitStr != ''  &&  $memoryLimitStr != -1) {
            $this->_memoryLimit = (integer)$memoryLimitStr;
            switch (strtolower($memoryLimitStr[strlen($memoryLimitStr)-1])) {
                case 'g':
                    $this->_memoryLimit *= 1024;
                    // Break intentionally omitted
                case 'm':
                    $this->_memoryLimit *= 1024;
                    // Break intentionally omitted
                case 'k':
                    $this->_memoryLimit *= 1024;
                    break;

                default:
                    break;
            }

            $this->_memoryLimit = (int)($this->_memoryLimit*2/3);
        } // No limit otherwise
    }

    /**
     * Object destructor
     *
     * Clean up cache storage
     */
    public function __destruct()
    {
        if ($this->_cache !== null) {
            if ($this->_cache instanceof ClearByNamespaceCacheStorage) {
                $this->_cache->clearByNamespace($this->_cache->getOptions()->getNamespace());
            } elseif ($this->_cache instanceof FlushableCacheStorage) {
                $this->_cache->flush();
            }
        }
    }

    /**
     * Set memory grow limit
     *
     * @param integer $newLimit
     */
    public function setMemoryLimit($newLimit)
    {
        $this->_memoryLimit = $newLimit;

        $this->_swapCheck();
    }

    /**
     * Get memory grow limit
     *
     * @return integer
     */
    public function getMemoryLimit()
    {
        return $this->_memoryLimit;
    }

    /**
     * Set minimum size of values, which may be swapped
     *
     * @param integer $newSize
     */
    public function setMinSize($newSize)
    {
        $this->_minSize = $newSize;
    }

    /**
     * Get minimum size of values, which may be swapped
     *
     * @return integer
     */
    public function getMinSize()
    {
        return $this->_minSize;
    }

    /**
     * Create new Zend_Memory value container
     *
     * @param string $value
     * @return \Zend\Memory\Container
     * @throws \Zend\Memory\Exception
     */
    public function create($value = '')
    {
        return $this->_create($value,  false);
    }

    /**
     * Create new Zend_Memory value container, which has value always
     * locked in memory
     *
     * @param string $value
     * @return \Zend\Memory\Container
     * @throws \Zend\Memory\Exception
     */
    public function createLocked($value = '')
    {
        return $this->_create($value, true);
    }

    /**
     * Create new Zend_Memory object
     *
     * @param string $value
     * @param boolean $locked
     * @return \Zend\Memory\Container
     * @throws \Zend\Memory\Exception
     */
    private function _create($value, $locked)
    {
        $id = $this->_nextId++;

        if ($locked  ||  ($this->_cache === null) /* Use only memory locked objects if backend is not specified */) {
            return new Container\Locked($value);
        }

        // Commit other objects modifications
        $this->_commit();

        $valueObject = new Container\Movable($this, $id, $value);

        // Store last object size as 0
        $this->_sizes[$id] = 0;
        // prepare object for next modifications
        $this->_lastModified = $valueObject;

        return new Container\AccessController($valueObject);
    }

    /**
     * Unlink value container from memory manager
     *
     * Used by Memory container destroy() method
     *
     * @internal
     * @param integer $id
     * @return \Zend\Memory\Container\AbstractContainer
     */
    public function unlink(Container\Movable $container, $id)
    {
        if ($this->_lastModified === $container) {
            // Drop all object modifications
            $this->_lastModified = null;
            unset($this->_sizes[$id]);
            return;
        }

        if (isset($this->_unloadCandidates[$id])) {
            unset($this->_unloadCandidates[$id]);
        }

        $this->_memorySize -= $this->_sizes[$id];
        unset($this->_sizes[$id]);
    }

    /**
     * Process value update
     *
     * @internal
     * @param \Zend\Memory\Container\Movable $container
     * @param integer $id
     */
    public function processUpdate(Container\Movable $container, $id)
    {
        /**
         * This method is automatically invoked by memory container only once per
         * "modification session", but user may call memory container touch() method
         * several times depending on used algorithm. So we have to use this check
         * to optimize this case.
         */
        if ($container === $this->_lastModified) {
            return;
        }

        // Remove just updated object from list of candidates to unload
        if( isset($this->_unloadCandidates[$id])) {
            unset($this->_unloadCandidates[$id]);
        }

        // Reduce used memory mark
        $this->_memorySize -= $this->_sizes[$id];

        // Commit changes of previously modified object if necessary
        $this->_commit();

        $this->_lastModified = $container;
    }

    /**
     * Commit modified object and put it back to the loaded objects list
     */
    private function _commit()
    {
        if (($container = $this->_lastModified) === null) {
            return;
        }

        $this->_lastModified = null;

        $id = $container->getId();

        // Calculate new object size and increase used memory size by this value
        $this->_memorySize += ($this->_sizes[$id] = strlen($container->getRef()));

        if ($this->_sizes[$id] > $this->_minSize) {
            // Move object to "unload candidates list"
            $this->_unloadCandidates[$id] = $container;
        }

        $container->startTrace();

        $this->_swapCheck();
    }

    /**
     * Check and swap objects if necessary
     *
     * @throws Zend_MemoryException
     */
    private function _swapCheck()
    {
        if ($this->_memoryLimit < 0  ||  $this->_memorySize < $this->_memoryLimit) {
            // Memory limit is not reached
            // Do nothing
            return;
        }

        // walk through loaded objects in access history order
        foreach ($this->_unloadCandidates as $id => $container) {
            $this->_swap($container, $id);
            unset($this->_unloadCandidates[$id]);

            if ($this->_memorySize < $this->_memoryLimit) {
                // We've swapped enough objects
                return;
            }
        }

        throw new Exception\RuntimeException('Memory manager can\'t get enough space.');
    }

    /**
     * Swap object data to disk
     * Actualy swaps data or only unloads it from memory,
     * if object is not changed since last swap
     *
     * @param \Zend\Memory\Container\Movable $container
     * @param integer $id
     */
    private function _swap(Container\Movable $container, $id)
    {
        if ($container->isLocked()) {
            return;
        }

        if (!$container->isSwapped()) {
            $this->_cache->setItem($this->_managerId . $id, $container->getRef());
        }

        $this->_memorySize -= $this->_sizes[$id];

        $container->markAsSwapped();
        $container->unloadValue();
    }

    /**
     * Load value from swap file.
     *
     * @internal
     * @param \Zend\Memory\Container\Movable $container
     * @param integer $id
     */
    public function load(Container\Movable $container, $id)
    {
        $value = $this->_cache->getItem($this->_managerId . $id);

        // Try to swap other objects if necessary
        // (do not include specified object into check)
        $this->_memorySize += strlen($value);
        $this->_swapCheck();

        // Add loaded obect to the end of loaded objects list
        $container->setValue($value);

        if ($this->_sizes[$id] > $this->_minSize) {
            // Add object to the end of "unload candidates list"
            $this->_unloadCandidates[$id] = $container;
        }
    }
}
