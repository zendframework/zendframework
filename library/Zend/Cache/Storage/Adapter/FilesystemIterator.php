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

use GlobIterator,
    Zend\Cache\Storage\IterableInterface,
    Zend\Cache\Storage\IteratorInterface;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FilesystemIterator implements IteratorInterface
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
     * The GlobIterator instance
     *
     * @var GlobIterator
     */
    protected $globIterator;

    /**
     * The namespace sprefix
     *
     * @var string
     */
    protected $prefix;

    /**
     * String length of namespace prefix
     *
     * @var int
     */
    protected $prefixLength;

    /**
     * Constructor
     *
     * @param Filesystem  $storage
     * @param string      $path
     * @param string      $prefix
     * @return void
     */
    public function __construct(Filesystem $storage, $path, $prefix)
    {
        $this->storage      = $storage;
        $this->globIterator = new GlobIterator($path, GlobIterator::KEY_AS_FILENAME);
        $this->prefix       = $prefix;
        $this->prefixLength = strlen($prefix);
    }

    /**
     * Get storage instance
     *
     * @return StorageInterface
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
     */
    public function key()
    {
        $filename = $this->globIterator->key();

        // return without namespace prefix and file suffix
        return substr($filename, $this->prefixLength, -4);
    }

    /**
     * Move forward to next element
     *
     * @return void
     */
    public function next()
    {
        $this->globIterator->next();
    }

	/**
	 * Checks if current position is valid
	 *
	 * @return boolean
     */
    public function valid()
    {
        try {
            return $this->globIterator->valid();
        } catch (\LogicException $e) {
            // @link https://bugs.php.net/bug.php?id=55701
            // GlobIterator throws LogicException with message
            // 'The parent constructor was not called: the object is in an invalid state'
            return false;
        }
    }

	/**
	 * Rewind the Iterator to the first element.
	 *
	 * @return void
     */
    public function rewind()
    {
        return $this->globIterator->rewind();
    }
}
