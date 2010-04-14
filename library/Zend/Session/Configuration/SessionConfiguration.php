<?php

namespace Zend\Session\Configuration;

use Zend\Validator\Hostname\Hostname as HostnameValidator,
    Zend\Session\Exception as SessionException;;

class SessionConfiguration extends StandardConfiguration
{
    /**
     * Set storage option in backend configuration store
     *
     * Does nothing in this implementation; others might use it to set things 
     * such as INI settings.
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return Zend\Session\Configuration\StandardConfiguration
     */
    public function setStorageOption($name, $value)
    {
        $key = false;
        switch ($name) {
            case 'strict':
                // do nothing; not an INI option
                return;
            case 'url_rewriter_tags':
                $key = 'url_rewriter.tags';
                break;
            default:
                $key = 'session.' . $name;
                break;
        }

        ini_set($key, $value);
    }

    /**
     * Retrieve a storage option from a backend configuration store
     *
     * Used to retrieve default values from a backend configuration store.
     * 
     * @param  string $name 
     * @return mixed
     */
    public function getStorageOption($name)
    {
        $key       = false;
        $transform = false;
        switch ($name) {
            case 'strict':
                // No remote storage option; just return the current value
                return $this->_strict;
            case 'url_rewriter_tags':
                $key = 'url_rewriter.tags';
                break;
            // The following all need a transformation on the retrieved value;
            // however they use the same key naming scheme
            case 'use_cookies':
            case 'use_only_cookies':
            case 'use_trans_sid':
                $transform = function ($value) {
                    return (bool) $value;
                };
            default:
                $key = 'session.' . $name;
                break;
        }

        $value = ini_get($key);
        if (false !== $transform) {
            $value = $transform($value);
        }
        return $value;
    }

    protected $_phpErrorCode    = false;
    protected $_phpErrorMessage = false;

    /**
     * Handle PHP errors
     * 
     * @param  int $code 
     * @param  string $message 
     * @return void
     */
    protected function _handleError($code, $message)
    {
        $this->_phpErrorCode    = $code;
        $this->_phpErrorMessage = $message;
    }

   
    // session.save_handler

    public function setSaveHandler($saveHandler)
    {
        $saveHandler = (string) $saveHandler;
        set_error_handler(array($this, '_handleError'));
        ini_set('session.save_handler', $saveHandler);
        restore_error_handler();
        if ($this->_phpErrorCode >= E_WARNING) {
            throw new SessionException('Invalid save handler specified');
        }

        $this->_saveHandler = $saveHandler;
        return $this;
    }

    // session.serialize_handler

    protected $_serializeHandler;

    public function setSerializeHandler($serializeHandler)
    {
        $serializeHandler = (string) $serializeHandler;

        set_error_handler(array($this, '_handleError'));
        ini_set('session.serialize_handler', $serializeHandler);
        restore_error_handler();
        if ($this->_phpErrorCode >= E_WARNING) {
            throw new SessionException('Invalid serialize handler specified');
        }

        $this->_serializeHandler = (string) $serializeHandler;
        return $this;
    }

    // session.cache_limiter

    protected $_cacheLimiter;
    protected $_validCacheLimiters = array(
        'nocache',
        'public',
        'private',
        'private_no_expire',
    );

    public function setCacheLimiter($cacheLimiter)
    {
        if (!in_array($cacheLimiter, $this->_validCacheLimiters)) {
            throw new SessionException('Invalid cache limiter provided');
        }
        $this->_cacheLimiter = (string) $cacheLimiter;
        ini_set('session.cache_limiter', $this->_cacheLimiter);
        return $this;
    }
   
    // session.hash_function

    protected $_hashFunction;
    protected $_validHashFunctions;

    protected function _getHashFunctions()
    {
        if (empty($this->_validHashFunctions)) {
            $this->_validHashFunctions = array(0, 1) + hash_algos();
        }
        return $this->_validHashFunctions;
    }

    public function setHashFunction($hashFunction)
    {
        $validHashFunctions = $this->_getHashFunctions();
        if (!in_array($hashFunction, $this->_getHashFunctions(), true)) {
            throw new SessionException('Invalid hash function provided');
        }
        $this->_hashFunction = (string) $hashFunction;
        ini_set('session.hash_function', $this->_hashFunction);
        return $this;
    }

    // session.hash_bits_per_character

    protected $_hashBitsPerCharacter;
    protected $_validHashBitsPerCharacters = array(
        4,
        5,
        6,
    );

    public function setHashBitsPerCharacter($hashBitsPerCharacter)
    {
        if (!is_numeric($hashBitsPerCharacter)
            || !in_array($hashBitsPerCharacter, $this->_validHashBitsPerCharacters)
        ) {
            throw new SessionException('Invalid hash bits per character provided');
        }
        $this->_hashBitsPerCharacter = (int) $hashBitsPerCharacter;
        ini_set('session.hash_bits_per_character', $this->_hashBitsPerCharacter);
        return $this;
    }
}
