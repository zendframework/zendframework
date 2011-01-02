<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-webat this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Session\Configuration;

use Zend\Validator\Hostname\Hostname as HostnameValidator,
    Zend\Session\Exception;

/**
 * Session configuration proxying to session INI options 
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage Configuration
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SessionConfiguration extends StandardConfiguration
{
    /**
     * Used with {@link _handleError()}; stores PHP error code
     * @var int
     */
    protected $_phpErrorCode    = false;

    /**
     * Used with {@link _handleError()}; stores PHP error message
     * @var string
     */
    protected $_phpErrorMessage = false;

    /**
     * @var int Default number of seconds to make session sticky, when rememberMe() is called
     */
    protected $_rememberMeSeconds = 1209600; // 2 weeks

    /**
     * @var string session.serialize_handler
     */
    protected $_serializeHandler;

    /**
     * @var array Valid cache limiters (per session.cache_limiter)
     */
    protected $_validCacheLimiters = array(
        'nocache',
        'public',
        'private',
        'private_no_expire',
    );

    /**
     * @var array Valid hash bits per character (per session.hash_bits_per_character)
     */
    protected $_validHashBitsPerCharacters = array(
        4,
        5,
        6,
    );

    /**
     * @var array Valid hash functions (per session.hash_function)
     */
    protected $_validHashFunctions;

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
            case 'remember_me_seconds':
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
            case 'remember_me_seconds':
                // No remote storage option; just return the current value
                return $this->_rememberMeSeconds;
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

    /**
     * Set session.save_handler
     * 
     * @param  string $saveHandler 
     * @return SessionConfiguration
     * @throws SessionException
     */
    public function setSaveHandler($saveHandler)
    {
        $saveHandler = (string) $saveHandler;
        set_error_handler(array($this, '_handleError'));
        ini_set('session.save_handler', $saveHandler);
        restore_error_handler();
        if ($this->_phpErrorCode >= E_WARNING) {
            throw new Exception\InvalidArgumentException('Invalid save handler specified');
        }

        $this->setOption('save_handler', $saveHandler);
        return $this;
    }

    /**
     * Set session.serialize_handler
     * 
     * @param  string $serializeHandler 
     * @return SessionConfiguration
     * @throws SessionException
     */
    public function setSerializeHandler($serializeHandler)
    {
        $serializeHandler = (string) $serializeHandler;

        set_error_handler(array($this, '_handleError'));
        ini_set('session.serialize_handler', $serializeHandler);
        restore_error_handler();
        if ($this->_phpErrorCode >= E_WARNING) {
            throw new Exception\InvalidArgumentException('Invalid serialize handler specified');
        }

        $this->_serializeHandler = (string) $serializeHandler;
        return $this;
    }

    // session.cache_limiter

    public function setCacheLimiter($cacheLimiter)
    {
        $cacheLimiter = (string) $cacheLimiter;
        if (!in_array($cacheLimiter, $this->_validCacheLimiters)) {
            throw new Exception\InvalidArgumentException('Invalid cache limiter provided');
        }
        $this->setOption('cache_limiter', $cacheLimiter);
        ini_set('session.cache_limiter', $cacheLimiter);
        return $this;
    }
   
    /**
     * Retrieve list of valid hash functions
     * 
     * @return array
     */
    protected function _getHashFunctions()
    {
        if (empty($this->_validHashFunctions)) {
            /**
             * @see http://php.net/manual/en/session.configuration.php#ini.session.hash-function
             * "0" and "1" refer to MD5-128 and SHA1-160, respectively, and are 
             * valid in addition to whatever is reported by hash_algos()
             */
            $this->_validHashFunctions = array('0', '1') + hash_algos();
        }
        return $this->_validHashFunctions;
    }

    /**
     * Set session.hash_function
     * 
     * @param  string|int $hashFunction 
     * @return SessionConfiguration
     * @throws SessionException
     */
    public function setHashFunction($hashFunction)
    {
        $hashFunction = (string) $hashFunction;
        $validHashFunctions = $this->_getHashFunctions();
        if (!in_array($hashFunction, $this->_getHashFunctions(), true)) {
            throw new Exception\InvalidArgumentException('Invalid hash function provided');
        }

        $this->setOption('hash_function', $hashFunction);
        ini_set('session.hash_function', $hashFunction);
        return $this;
    }

    /**
     * Set session.hash_bits_per_character
     * 
     * @param  int $hashBitsPerCharacter 
     * @return SessionConfiguration
     * @throws SessionException
     */
    public function setHashBitsPerCharacter($hashBitsPerCharacter)
    {
        if (!is_numeric($hashBitsPerCharacter)
            || !in_array($hashBitsPerCharacter, $this->_validHashBitsPerCharacters)
        ) {
            throw new Exception\InvalidArgumentException('Invalid hash bits per character provided');
        }

        $hashBitsPerCharacter = (int) $hashBitsPerCharacter;
        $this->setOption('hash_bits_per_character', $hashBitsPerCharacter);
        ini_set('session.hash_bits_per_character', $hashBitsPerCharacter);
        return $this;
    }
}
