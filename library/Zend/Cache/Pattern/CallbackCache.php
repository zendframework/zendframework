<?php

namespace Zend\Cache\Pattern;
use Zend\Cache,
    Zend\Cache\Exception\RuntimeException,
    Zend\Cache\Exception\InvalidArgumentException;

class CallbackCache extends AbstractPattern
{

    /**
     * The storage adapter
     *
     * @var Zend\Cache\Storage\Adapter
     */
    protected $_storage;

    /**
     * Caching output stream
     *
     * @var boolean
     */
    protected $_cacheOutput = true;

    public function __construct($options = array())
    {
        parent::__construct($options);

        if (!$this->getStorage()) {
            throw new InvalidArgumentException("Missing option 'storage'");
        }
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['storage']      = $this->getStorage();
        $options['cache_output'] = $this->getCacheOutput();
        return $options;
    }

    /**
     * Get cache storage
     *
     * return Zend\Cache\Storage\Adapter
     */
    public function getStorage()
    {
        return $this->_storage;
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

        $this->_storage = $storage;
        return $this;
    }

    /**
     * Enable/Disable caching output stream
     *
     * @param boolean $flag
     */
    public function setCacheOutput($flag)
    {
        $this->_cacheOutput = (bool)$flag;
        return $this;
    }

    /**
     * Get caching output stream
     *
     * return boolean
     */
    public function getCacheOutput()
    {
        return $this->_cacheOutput;
    }

    /**
     * Call the specified callback or get the result from cache
     *
     * @param  callback   $callback  A valid callback
     * @param  array      $args      Callback arguments
     * @param  array      $options   Options
     * @return mixed Result
     * @throws Zend\Cache\Exception
     */
    public function call($callback, array $args = array(), array $options = array())
    {
        $key = $this->_generateKey($callback, $args, $options);
        if ( ($rs = $this->getStorage()->getItem($key, $options)) !== false ) {
            if (!isset($rs[0])) {
                throw new RuntimeException("Invalid cached data for key '{$key}'");
            }

            echo isset($rs[1]) ? $rs[1] : '';
            return $rs[0];
        }

        if ( ($cacheOutput = $this->getCacheOutput()) ) {
            ob_start();
            ob_implicit_flush(false);
        }

        // TODO: do not cache on errors using [set|restore]_error_handler

        try {
            if ($args) {
                $ret = call_user_func_array($callback, $args);
            } else {
                $ret = call_user_func($callback);
            }
        } catch (\Exception $e) {
            if ($cacheOutput) {
                ob_end_flush();
            }
            throw $e;
        }

        if ($cacheOutput) {
            $data = array($ret, ob_get_flush());
        } else {
            $data = array($ret);
        }

        $this->getStorage()->setItem($key, $data, $options);

        return $ret;
    }

    /**
     * function call handler
     *
     * @param  string $function  Function name to call
     * @param  array  $args      Function arguments
     * @return mixed
     * @throws Zend\Cache\Exception
     */
    public function __call($function, array $args)
    {
        return $this->call($function, $args);
    }

    /**
     * Generate a unique key in base of a key representing the callback part
     * and a key representing the arguments part merged using md5($callbackKey.$argumentsKey).
     *
     * Options:
     *   callback_key  A string representing the callback part of the key
     *                 or NULL to autogenerate the callback key part
     *   argument_key  A string representing the arguments part of the key
     *                 or NULL to autogenerate the arguments key part
     *
     * @param  callback   $callback  A valid callback
     * @param  array      $args      Callback arguments
     * @param  array      $options   Options
     * @return string
     * @throws Zend\Cache\Exception
     */
    public function generateKey($callback, array $args = array(), array $options = array())
    {
        return $this->_generateKey($callback, $args, $options);
    }

    protected function _generateKey($callback, array $args, array $options)
    {
        $callbackKey = '';
        $argumentKey = '';

        // generate callback key part
        if (isset($options['callback_key'])) {
            $callbackKey = (string)$options['callback_key'];

            if (!is_callable($callback, false)) {
                throw new InvalidArgumentException('Invalid callback');
            }
        } else {
            if (!is_callable($callback, false, $callbackKey)) {
                throw new InvalidArgumentException('Invalid callback');
            }

            // functions, methods and classnames are case-insensitive
            $callbackKey = strtolower($callbackKey);

            // generate a unique key of object callbacks
            if (is_object($callback)) { // Closures & __invoke
                $object = $callback;
            } elseif (isset($callback[0])) { // array($object, 'method')
                $object = $callback[0];
            }
            if (isset($object)) {
                try {
                    $serializedObject = @serialize($object);
                } catch (\Exception $e) {
                    throw new RuntimeException(
                        "Can't serialize callback: see previous exception"
                    , 0, $e);
                }

                if (!$serializedObject) {
                    $lastErr = error_get_last();
                    throw new RuntimeException(
                        "Can't serialize callback: "
                        . $lastErr['message']
                    );
                }
                $callbackKey.= $serializedObject;
            }
        }

        // generate argument key part
        if (isset($options['argument_key'])) {
            $argumentKey = (string)$options['argument_key'];
        } elseif ($args) {
            try {
                $serializedArgs = @serialize(array_values($args));
            } catch (\Exception $e) {
                throw new RuntimeException(
                    "Can't serialize arguments: see previous exception"
                , 0, $e);
            }

            if (!$serializedArgs) {
                $lastErr = error_get_last();
                throw new RuntimeException(
                    "Can't serialize arguments: "
                    . $lastErr['message']
                );
            }

            $argumentKey = $serializedArgs;
        }

        // merge and return the key parts
        return md5($callbackKey.$argumentKey);
    }

}
