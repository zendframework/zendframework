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
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Pattern;

use Zend\Cache\Exception,
    Zend\Cache\StorageFactory,
    Zend\Cache\Storage\Adapter as StorageAdapter;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Pattern
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class OutputCache extends AbstractPattern
{
    /**
     * The storage adapter
     *
     * @var StorageAdapter
     */
    protected $storage;

    /**
     * The key stack
     *
     * @var array
     */
    protected $keyStack = array();

    /**
     * Constructor
     *
     * @param  array|\Traversable $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        if (!$this->getStorage()) {
            throw new Exception\InvalidArgumentException("Missing option 'storage'");
        }
    }

    /**
     * Get all pattern options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        $options['storage'] = $this->getStorage();
        return $options;
    }

    /**
     * Get cache storage
     *
     * return StorageAdapter
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set cache storage
     *
     * @param  StorageAdapter|array|string $storage
     * @return OutputCache
     */
    public function setStorage($storage)
    {
        if (is_array($storage)) {
            $storage = StorageFactory::factory($storage);
        } elseif (is_string($storage)) {
            $storage = StorageFactory::adapterFactory($storage);
        } elseif (!($storage instanceof StorageAdapter)) {
            throw new Exception\InvalidArgumentException(
                'The storage must be an instanceof Zend\Cache\Storage\Adapter '
              . 'or an array passed to Zend\Cache\Storage::factory '
              . 'or simply the name of the storage adapter'
            );
        }

        $this->storage = $storage;
        return $this;
    }

    /**
     * Start output cache
     *
     * Options:
     *   output  boolean  If true the (default) cached output will be displayed
     *                    else the cached output will be returned instead of true
     *   + storage read options
     *
     * @param  string  $key      Key
     * @param  array   $options  Output start options (ttl | validate | output)
     * @return boolean|string    True if the cache is hit or if output disabled the cached data, false else
     * @throws Exception
     */
    public function start($key, array $options = array())
    {
        if (($key = (string) $key) === '') {
            throw new Exception\MissingKeyException('Missing key to read/write output from storage');
        }

        $optOutput = true;
        if (isset($options['output'])) {
            $optOutput = (bool) $options['output'];
            unset($options['output']); // don't forword this option to storage
        }

        $data = $this->getStorage()->getItem($key, $options);
        if ($data !== false) {
            if ($optOutput) {
                echo $data;
                return true;
            }
            return (string) $data;
        }

        ob_start();
        ob_implicit_flush(false);
        $this->keyStack[] = $key;
        return false;
    }

    /**
     * Stop output cache
     *
     * Options:
     *   output  boolean  If true (default) the catched output will be displayed
     *                    else teh catched output will only be written to cache
     *   + storage write options
     *
     * @param  array   $options
     * @return boolean
     * @throws Exception
     */
    public function end(array $options = array())
    {
        $key = array_pop($this->keyStack);
        if ($key === null) {
            throw new Exception\RuntimeException('use of end() without a start()');
        }

        $optOutput = true;
        if (isset($options['output'])) {
            $optOutput = (bool)$options['output'];
            unset($options['output']); // don't forword this option to storage
        }

        if ($optOutput) {
            $data = ob_get_flush();
        } else {
            $data = ob_get_clean();
        }

        if ($data === false) {
            throw new Exception\RuntimeException('Output buffering not active');
        }

        return $this->getStorage()->setItem($key, $data, $options);
    }
}
