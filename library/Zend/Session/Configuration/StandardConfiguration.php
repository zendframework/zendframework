<?php

namespace Zend\Session\Configuration;

use Zend\Session\Configuration as Configurable,
    Zend\Session\Exception as SessionException,
    Zend\Validator\Hostname\Hostname as HostnameValidator;

class StandardConfiguration implements Configurable
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
        return null;
    }

    // session.save_path

    protected $_savePath;

    public function setSavePath($path)
    {
        if (!is_dir($path)) {
            throw new SessionException('Invalid save_path provided');
        }
        $this->_savePath = $path;
        $this->setStorageOption('save_path', $path);
        return $this;
    }

    public function getSavePath()
    {
        if (null === $this->_savePath) {
            $this->_savePath = $this->getStorageOption('save_path');
        }
        return $this->_savePath;
    }

    // session.name

    protected $_name;

    public function setName($name)
    {
        $this->_name = (string) $name;
        $this->setStorageOption('name', $this->_name);
        return $this;
    }

    public function getName()
    {
        if (null === $this->_name) {
            $this->_name = $this->getStorageOption('name');
        }
        return $this->_name;
    }
    
    // session.save_handler

    protected $_saveHandler;

    public function setSaveHandler($saveHandler)
    {
        $saveHandler = (string) $saveHandler;
        $this->_saveHandler = $saveHandler;
        $this->setStorageOption('save_handler', $saveHandler);
        return $this;
    }

    public function getSaveHandler()
    {
        if (null === $this->_saveHandler) {
            $this->_saveHandler = $this->getStorageOption('save_handler');
        }
        return $this->_saveHandler;
    }
    
    // session.gc_probability

    protected $_gcProbability;

    public function setGcProbability($gcProbability)
    {
        if (!is_numeric($gcProbability)) {
            throw new SessionException('Invalid gc_probability; must be numeric');
        }
        $gcProbability = (int) $gcProbability;
        if (1 > $gcProbability || 100 < $gcProbability) {
            throw new SessionException('Invalid gc_probability; must be a percentage');
        }
        $this->_gcProbability = $gcProbability;
        $this->setStorageOption('gc_probability', $gcProbability);
        return $this;
    }

    public function getGcProbability()
    {
        if (null === $this->_gcProbability) {
            $this->_gcProbability = $this->getStorageOption('gc_probability');
        }
        return $this->_gcProbability;
    }
    
    // session.gc_divisor

    protected $_gcDivisor;

    public function setGcDivisor($gcDivisor)
    {
        if (!is_numeric($gcDivisor)) {
            throw new SessionException('Invalid gc_divisor; must be numeric');
        }
        $gcDivisor = (int) $gcDivisor;
        if (1 > $gcDivisor) {
            throw new SessionException('Invalid gc_divisor; must be a positive integer');
        }
        $this->_gcDivisor = $gcDivisor;
        $this->setStorageOption('gc_divisor', $gcDivisor);
        return $this;
    }

    public function getGcDivisor()
    {
        if (null === $this->_gcDivisor) {
            $this->_gcDivisor = $this->getStorageOption('gc_divisor');
        }
        return $this->_gcDivisor;
    }
    
    // session.gc_maxlifetime

    protected $_gcMaxlifetime;

    public function setGcMaxlifetime($gcMaxlifetime)
    {
        if (!is_numeric($gcMaxlifetime)) {
            throw new SessionException('Invalid gc_maxlifetime; must be numeric');
        }

        $gcMaxlifetime = (int) $gcMaxlifetime;
        if (1 > $gcMaxlifetime) {
            throw new SessionException('Invalid gc_maxlifetime; must be a positive integer');
        }

        $this->_gcMaxlifetime = $gcMaxlifetime;
        $this->setStorageOption('gc_maxlifetime', $gcMaxlifetime);
        return $this;
    }

    public function getGcMaxlifetime()
    {
        if (null === $this->_gcMaxlifetime) {
            $this->_gcMaxlifetime = $this->getStorageOption('gc_maxlifetime');
        }
        return $this->_gcMaxlifetime;
    }
    
    // session.serialize_handler

    protected $_serializeHandler;

    public function setSerializeHandler($serializeHandler)
    {
        $serializeHandler = (string) $serializeHandler;
        $this->_serializeHandler = $serializeHandler;
        $this->setStorageOption('serialize_handler', $serializeHandler);
        return $this;
    }

    public function getSerializeHandler()
    {
        if (null === $this->_serializeHandler) {
            $this->_serializeHandler = $this->getStorageOption('serialize_handler');
        }
        return $this->_serializeHandler;
    }
    
    // session.cookie_lifetime

    protected $_cookieLifetime;

    public function setCookieLifetime($cookieLifetime)
    {
        if (!is_numeric($cookieLifetime)) {
            throw new SessionException('Invalid cookie_lifetime; must be numeric');
        }
        if (0 > $cookieLifetime) {
            throw new SessionException('Invalid cookie_lifetime; must be a positive integer or zero');
        }

        $this->_cookieLifetime = (int) $cookieLifetime;
        $this->setStorageOption('cookie_lifetime', $this->_cookieLifetime);
        return $this;
    }

    public function getCookieLifetime()
    {
        if (null === $this->_cookieLifetime) {
            $this->_cookieLifetime = $this->getStorageOption('cookie_lifetime');
        }
        return $this->_cookieLifetime;
    }
    
    // session.cookie_path

    protected $_cookiePath;

    public function setCookiePath($cookiePath)
    {
        $cookiePath = (string) $cookiePath;

        $test = parse_url($cookiePath, PHP_URL_PATH);
        if ($test != $cookiePath || '/' != $test[0]) {
            throw new SessionException('Invalid cookie path');
        }

        $this->_cookiePath = $cookiePath;
        $this->setStorageOption('cookie_path', $cookiePath);
        return $this;
    }

    public function getCookiePath()
    {
        if (null === $this->_cookiePath) {
            $this->_cookiePath = $this->getStorageOption('cookie_path');
        }
        return $this->_cookiePath;
    }
    
    // session.cookie_domain

    protected $_cookieDomain;

    public function setCookieDomain($cookieDomain)
    {
        if (!is_string($cookieDomain)) {
            throw new SessionException('Invalid cookie domain: must be a string');
        }

        $validator = new HostnameValidator(HostnameValidator::ALLOW_ALL);

        if (!empty($cookieDomain) && !$validator->isValid($cookieDomain)) {
            throw new SessionException('Invalid cookie domain: ' . implode('; ', $validator->getMessages()));
        }

        $this->_cookieDomain = $cookieDomain;
        $this->setStorageOption('cookie_domain', $cookieDomain);
        return $this;
    }

    public function getCookieDomain()
    {
        if (null === $this->_cookieDomain) {
            $this->_cookieDomain = $this->getStorageOption('cookie_domain');
        }
        return $this->_cookieDomain;
    }
    
    // session.cookie_secure

    protected $_cookieSecure;

    public function setCookieSecure($cookieSecure)
    {
        $this->_cookieSecure = (bool) $cookieSecure;
        $this->setStorageOption('cookie_secure', $this->_cookieSecure);
        return $this;
    }

    public function getCookieSecure()
    {
        if (null === $this->_cookieSecure) {
            $this->_cookieSecure = $this->getStorageOption('cookie_secure');
        }
        return $this->_cookieSecure;
    }
    
    // session.cookie_httponly

    protected $_cookieHTTPOnly;

    /**
     * case sensitive method lookups in setOptions means this method has an 
     * unusual casing
     */
    public function setCookieHttponly($cookieHTTPOnly)
    {
        $this->_cookieHTTPOnly = (bool) $cookieHTTPOnly;
        $this->setStorageOption('cookie_httponly', $this->_cookieHTTPOnly);
        return $this;
    }

    public function getCookieHTTPOnly()
    {
        if (null === $this->_cookieHTTPOnly) {
            $this->_cookieHTTPOnly = $this->getStorageOption('cookie_httponly');
        }
        return $this->_cookieHTTPOnly;
    }
    
    // session.use_cookies

    protected $_useCookies;

    public function setUseCookies($flag)
    {
        $this->_useCookies = (bool) $flag;
        $this->setStorageOption('use_cookies', $this->_useCookies);
        return $this;
    }

    public function getUseCookies()
    {
        if (null === $this->_useCookies) {
            $this->_useCookies = $this->getStorageOption('use_cookies');
        }
        return $this->_useCookies;
    }
    
    // session.use_only_cookies

    protected $_useOnlyCookies;

    public function setUseOnlyCookies($flag)
    {
        $this->_useOnlyCookies = (bool) $flag;
        $this->setStorageOption('use_only_cookies', $this->_useOnlyCookies);
        return $this;
    }

    public function getUseOnlyCookies()
    {
        if (null === $this->_useOnlyCookies) {
            $this->_useOnlyCookies = $this->getStorageOption('use_only_cookies');
        }
        return $this->_useOnlyCookies;
    }

    // session.referer_check

    protected $_refererCheck;

    public function setRefererCheck($referer_check)
    {
        $this->_refererCheck = (string) $referer_check;
        $this->setStorageOption('referer_check', $this->_refererCheck);
        return $this;
    }

    public function getRefererCheck()
    {
        if (null === $this->_refererCheck) {
            $this->_refererCheck = $this->getStorageOption('referer_check');
        }
        return $this->_refererCheck;
    }

    // session.entropy_file

    protected $_entropyFile;

    public function setEntropyFile($path)
    {
        if (!file_exists($path) || is_dir($path) || !is_readable($path)) {
            throw new SessionException('Invalid entropy_file provided');
        }
        $this->_entropyFile = $path;
        $this->setStorageOption('entropy_file', $path);
        return $this;
    }

    public function getEntropyFile()
    {
        if (null === $this->_entropyFile) {
            $this->_entropyFile = $this->getStorageOption('entropy_file');
        }
        return $this->_entropyFile;
    }
    
    // session.entropy_length

    protected $_entropyLength;

    public function setEntropyLength($entropyLength)
    {
        if (!is_numeric($entropyLength)) {
            throw new SessionException('Invalid entropy_length; must be numeric');
        }
        if (0 > $entropyLength) {
            throw new SessionException('Invalid entropy_length; must be a positive integer or zero');
        }

        $this->_entropyLength = (int) $entropyLength;
        $this->setStorageOption('entropy_length', $this->_entropyLength);
        return $this;
    }

    public function getEntropyLength()
    {
        if (null === $this->_entropyLength) {
            $this->_entropyLength = $this->getStorageOption('entropy_length');
        }
        return $this->_entropyLength;
    }

    // session.cache_limiter

    protected $_cacheLimiter;

    public function setCacheLimiter($cacheLimiter)
    {
        $this->_cacheLimiter = (string) $cacheLimiter;
        $this->setStorageOption('cache_limiter', $this->_cacheLimiter);
        return $this;
    }

    public function getCacheLimiter()
    {
        if (null === $this->_cacheLimiter) {
            $this->_cacheLimiter = $this->getStorageOption('cache_limiter');
        }
        return $this->_cacheLimiter;
    }
    
    // session.cache_expire

    protected $_cacheExpire;

    public function setCacheExpire($cacheExpire)
    {
        if (!is_numeric($cacheExpire)) {
            throw new SessionException('Invalid cache_expire; must be numeric');
        }

        $cacheExpire = (int) $cacheExpire;
        if (1 > $cacheExpire) {
            throw new SessionException('Invalid cache_expire; must be a positive integer');
        }

        $this->_cacheExpire = $cacheExpire;
        $this->setStorageOption('cache_expire', $cacheExpire);
        return $this;
    }

    public function getCacheExpire()
    {
        if (null === $this->_cacheExpire) {
            $this->_cacheExpire = $this->getStorageOption('cache_expire');
        }
        return $this->_cacheExpire;
    }
    
    // session.use_trans_sid

    protected $_useTransSid;

    public function setUseTransSid($flag)
    {
        $this->_useTransSid = (bool) $flag;
        $this->setStorageOption('use_trans_sid', $this->_useTransSid);
        return $this;
    }

    public function getUseTransSid()
    {
        if (null === $this->_useTransSid) {
            $this->_useTransSid = $this->getStorageOption('use_trans_sid');
        }
        return $this->_useTransSid;
    }

    // session.hash_function

    protected $_hashFunction;

    public function setHashFunction($hashFunction)
    {
        $this->_hashFunction = (string) $hashFunction;
        $this->setStorageOption('hash_function', $this->_hashFunction);
        return $this;
    }

    public function getHashFunction()
    {
        if (null === $this->_hashFunction) {
            $this->_hashFunction = $this->getStorageOption('hash_function');
        }
        return $this->_hashFunction;
    }

    // session.hash_bits_per_character

    protected $_hashBitsPerCharacter;

    public function setHashBitsPerCharacter($hashBitsPerCharacter)
    {
        if (!is_numeric($hashBitsPerCharacter)) {
            throw new SessionException('Invalid hash bits per character provided');
        }
        $this->_hashBitsPerCharacter = (int) $hashBitsPerCharacter;
        $this->setStorageOption('hash_bits_per_character', $this->_hashBitsPerCharacter);
        return $this;
    }

    public function getHashBitsPerCharacter()
    {
        if (null === $this->_hashBitsPerCharacter) {
            $this->_hashBitsPerCharacter = $this->getStorageOption('hash_bits_per_character');
        }
        return $this->_hashBitsPerCharacter;
    }

    // url_rewriter.tags

    protected $_urlRewriterTags;

    /**
     * @todo Probably should add more robust validation
     */
    public function setUrlRewriterTags($urlRewriterTags)
    {
        $this->_urlRewriterTags = (string) $urlRewriterTags;
        $this->setStorageOption('url_rewriter_tags', $this->_urlRewriterTags);
        return $this;
    }

    public function getUrlRewriterTags()
    {
        if (null === $this->_urlRewriterTags) {
            $this->_urlRewriterTags = $this->getStorageOption('url_rewriter_tags');
        }
        return $this->_urlRewriterTags;
    }

    // remember_me_seconds

    protected $_rememberMeSeconds;

    public function setRememberMeSeconds($rememberMeSeconds)
    {
        if (!is_numeric($rememberMeSeconds)) {
            throw new SessionException('Invalid remember_me_seconds; must be numeric');
        }

        $rememberMeSeconds = (int) $rememberMeSeconds;
        if (1 > $rememberMeSeconds) {
            throw new SessionException('Invalid remember_me_seconds; must be a positive integer');
        }

        $this->_rememberMeSeconds = $rememberMeSeconds;
        $this->setStorageOption('remember_me_seconds', $rememberMeSeconds);
        return $this;
    }

    public function getRememberMeSeconds()
    {
        if (null === $this->_rememberMeSeconds) {
            $this->_rememberMeSeconds = $this->getStorageOption('remember_me_seconds');
        }
        return $this->_rememberMeSeconds;
    }
 
    // set options

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            // translate key from underscore_separated to TitleCased
            $internalKey = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $method = 'set' . $internalKey;
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
}
