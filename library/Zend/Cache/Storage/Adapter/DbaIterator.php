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
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Adapter;

use Zend\Cache\Exception,
    Zend\Cache\Storage\IterableInterface,
    Zend\Cache\Storage\IteratorInterface;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DbaIterator implements IteratorInterface
{
    /**
     * The apc storage intance
     *
     * @var Apc
     */
    protected $storage;

    /**
     * The iterator mode
     *
     * @var int
     */
    protected $mode = IteratorInterface::CURRENT_AS_KEY;

    /**
     * The dba resource handle
     *
     * @var resource
     */
    protected $handle;

    /**
     * The length of the namespace prefix
     *
     * @var int
     */
    protected $prefixLength;

    /**
     * The current internal key
     *
     * @var string|boolean
     */
    protected $currentInternalKey;

    /**
     * Constructor
     *
     * @param Dba      $storage
     * @param resource $handle
     * @param string   $prefix
     * @return void
     */
    public function __construct(Dba $storage, $handle, $prefix)
    {
        $this->storage      = $storage;
        $this->handle       = $handle;
        $this->prefixLength = strlen($prefix);

        $this->rewind();
    }

    /**
     * Get storage instance
     *
     * @return Dba
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Get iterator mode
     *
     * @return int Value of IteratorInterface::CURRENT_AS_*
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set iterator mode
     *
     * @param int $mode
     * @return ApcIterator Fluent interface
     */
    public function setMode($mode)
    {
        $this->mode = (int) $mode;
        return $this;
    }

    /* Iterator */

    /**
     * Get current key, value or metadata.
     *
     * @return mixed
     * @throws Exception\RuntimeException
     */
    public function current()
    {
        if ($this->mode == IteratorInterface::CURRENT_AS_SELF) {
            return $this;
        }

        $key = $this->key();

        if ($this->mode == IteratorInterface::CURRENT_AS_VALUE) {
            return $this->storage->getItem($key);
        } elseif ($this->mode == IteratorInterface::CURRENT_AS_METADATA) {
            return $this->storage->getMetadata($key);
        }

        return $key;
    }

    /**
     * Get current key
     *
     * @return string
     * @throws Exception\RuntimeException
     */
    public function key()
    {
        if ($this->currentInternalKey === false) {
            throw new Exception\RuntimeException("Iterater is on an invalid state");
        }

        // remove namespace prefix
        return substr($this->currentInternalKey, $this->prefixLength);
    }

    /**
     * Move forward to next element
     *
     * @return void
     * @throws Exception\RuntimeException
     */
    public function next()
    {
        if ($this->currentInternalKey === false) {
            throw new Exception\RuntimeException("Iterater is on an invalid state");
        }

        $this->currentInternalKey = dba_nextkey($this->handle);

        // Workaround for PHP-Bug #62492
        if ($this->currentInternalKey === null) {
            $this->currentInternalKey = false;
        }
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean
     */
    public function valid()
    {
        return ($this->currentInternalKey !== false);
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @return void
     * @throws Exception\RuntimeException
     */
    public function rewind()
    {
        if ($this->currentInternalKey === false) {
            throw new Exception\RuntimeException("Iterater is on an invalid state");
        }

        $this->currentInternalKey = dba_firstkey($this->handle);

        // Workaround for PHP-Bug #62492
        if ($this->currentInternalKey === null) {
            $this->currentInternalKey = false;
        }
    }
}
