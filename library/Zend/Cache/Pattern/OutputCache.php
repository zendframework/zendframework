<?php

namespace Zend\Cache\Pattern;

use Zend\Cache,
    Zend\Cache\Exception\InvalidArgumentException,
    Zend\Cache\Exception\MissingKeyException,
    Zend\Cache\Exception\RuntimeException;

class OutputCache extends AbstractPattern
{

    /**
     * The storage adapter
     *
     * @var Zend\Cache\Storage\Adapter
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
     * @param array|Traversable $options
     * @throws InvalidArgumentException
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        if (!$this->getStorage()) {
            throw new InvalidArgumentException("Missing option 'storage'");
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
     * return Zend\Cache\Storage\Adapter
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set cache storage
     *
     * @param Zend\Cache\Storage\Adapter|array|string $storage
     * @return Zend\Cache\Pattern\PatternInterface
     */
    public function setStorage($storage)
    {
        if (is_array($storage)) {
            $storage = Cache\StorageFactory::factory($storage);
        } elseif (is_string($storage)) {
            $storage = Cache\StorageFactory::adapterFactory($storage);
        } elseif ( !($storage instanceof Cache\Storage\Adapter) ) {
            throw new InvalidArgumentException(
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
     * @throws Zend_Cache_Exception
     */
    public function start($key, array $options = array())
    {
        if (($key = (string)$key) === '') {
            throw new MissingKeyException('Missing key to read/write output from storage');
        }

        $optOutput = true;
        if (isset($options['output'])) {
            $optOutput = (bool)$options['output'];
            unset($options['output']); // don't forword this option to storage
        }

        $data = $this->getStorage()->getItem($key, $options);
        if ($data !== false) {
            if ($optOutput) {
                echo $data;
                return true;
            } else {
                return (string)$data;
            }
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
     * @throws Zend_Cache_Exception
     */
    public function end(array $options = array())
    {
        $key = array_pop($this->keyStack);
        if ($key === null) {
            throw new RuntimeException('use of end() without a start()');
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
            throw new RuntimeException('Output buffering not active');
        }

        return $this->getStorage()->setItem($key, $data, $options);
    }

}
