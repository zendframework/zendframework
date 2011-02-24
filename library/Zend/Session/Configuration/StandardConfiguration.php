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

use Zend\Session\Configuration as Configurable,
    Zend\Session\Exception,
    Zend\Validator\Hostname as HostnameValidator,
    Zend\Filter\Word\CamelCaseToUnderscore as CamelCaseToUnderscoreFilter;

/**
 * Standard session configuration
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage Configuration
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StandardConfiguration implements Configurable
{
    /**
     * @var Zend\Filter Filter to convert CamelCase to underscore_separated
     */
    protected $_camelCaseToUnderscoreFilter;
    
    /**
     * @var string session.cookie_domain
     */
    protected $_cookieDomain;

    /**
     * @var bool session.cookie_httponly
     */
    protected $_cookieHTTPOnly;

    /**
     * @var int session.cookie_lifetime
     */
    protected $_cookieLifetime;

    /**
     * @var string session.cookie_path
     */
    protected $_cookiePath;

    /**
     * @var bool session.cookie_secure
     */
    protected $_cookieSecure;

    /**
     * @var string session.name
     */
    protected $_name;

    /**
     * @var array All options
     */
    protected $_options = array();

    /**
     * @var int remember_me_seconds
     */
    protected $_rememberMeSeconds;

    /**
     * @var string session.save_path
     */
    protected $_savePath;

    /**
     * @var bool session.use_cookies
     */
    protected $_useCookies;

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

    /**
     * Set session.save_path
     * 
     * @param  string $path 
     * @return StandardConfiguration
     * @throws SessionException on invalid path
     */
    public function setSavePath($path)
    {
        if (!is_dir($path)) {
            throw new Exception\InvalidArgumentException('Invalid save_path provided');
        }
        $this->_savePath = $path;
        $this->setStorageOption('save_path', $path);
        return $this;
    }

    /**
     * Set session.save_path
     * 
     * @return string|null
     */
    public function getSavePath()
    {
        if (null === $this->_savePath) {
            $this->_savePath = $this->getStorageOption('save_path');
        }
        return $this->_savePath;
    }

    /**
     * Set session.name
     * 
     * @param  string $name 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setName($name)
    {
        $this->_name = (string) $name;
        if (empty($this->_name)) {
            throw new Exception\InvalidArgumentException('Invalid session name; cannot be empty');
        }
        $this->setStorageOption('name', $this->_name);
        return $this;
    }

    /**
     * Get session.name
     * 
     * @return null|string
     */
    public function getName()
    {
        if (null === $this->_name) {
            $this->_name = $this->getStorageOption('name');
        }
        return $this->_name;
    }
    
    /**
     * Set session.gc_probability
     * 
     * @param  int $gcProbability 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setGcProbability($gcProbability)
    {
        if (!is_numeric($gcProbability)) {
            throw new Exception\InvalidArgumentException('Invalid gc_probability; must be numeric');
        }
        $gcProbability = (int) $gcProbability;
        if (1 > $gcProbability || 100 < $gcProbability) {
            throw new Exception\InvalidArgumentException('Invalid gc_probability; must be a percentage');
        }
        $this->setOption('gc_probability', $gcProbability);
        $this->setStorageOption('gc_probability', $gcProbability);
        return $this;
    }

    /**
     * Set session.gc_divisor
     * 
     * @param  int $gcDivisor 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setGcDivisor($gcDivisor)
    {
        if (!is_numeric($gcDivisor)) {
            throw new Exception\InvalidArgumentException('Invalid gc_divisor; must be numeric');
        }
        $gcDivisor = (int) $gcDivisor;
        if (1 > $gcDivisor) {
            throw new Exception\InvalidArgumentException('Invalid gc_divisor; must be a positive integer');
        }
        $this->setOption('gc_divisor', $gcDivisor);
        $this->setStorageOption('gc_divisor', $gcDivisor);
        return $this;
    }

    /**
     * Set gc.maxlifetime
     * 
     * @param  int $gcMaxlifetime 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setGcMaxlifetime($gcMaxlifetime)
    {
        if (!is_numeric($gcMaxlifetime)) {
            throw new Exception\InvalidArgumentException('Invalid gc_maxlifetime; must be numeric');
        }

        $gcMaxlifetime = (int) $gcMaxlifetime;
        if (1 > $gcMaxlifetime) {
            throw new Exception\InvalidArgumentException('Invalid gc_maxlifetime; must be a positive integer');
        }

        $this->setOption('gc_maxlifetime', $gcMaxlifetime);
        $this->setStorageOption('gc_maxlifetime', $gcMaxlifetime);
        return $this;
    }

    /**
     * Set session.cookie_lifetime
     * 
     * @param  int $cookieLifetime 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setCookieLifetime($cookieLifetime)
    {
        if (!is_numeric($cookieLifetime)) {
            throw new Exception\InvalidArgumentException('Invalid cookie_lifetime; must be numeric');
        }
        if (0 > $cookieLifetime) {
            throw new Exception\InvalidArgumentException('Invalid cookie_lifetime; must be a positive integer or zero');
        }

        $this->_cookieLifetime = (int) $cookieLifetime;
        $this->setStorageOption('cookie_lifetime', $this->_cookieLifetime);
        return $this;
    }

    /**
     * Get session.cookie_lifetime
     * 
     * @return int
     */
    public function getCookieLifetime()
    {
        if (null === $this->_cookieLifetime) {
            $this->_cookieLifetime = $this->getStorageOption('cookie_lifetime');
        }
        return $this->_cookieLifetime;
    }
    
    /**
     * Set session.cookie_path
     * 
     * @param  string $cookiePath 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setCookiePath($cookiePath)
    {
        $cookiePath = (string) $cookiePath;

        $test = parse_url($cookiePath, PHP_URL_PATH);
        if ($test != $cookiePath || '/' != $test[0]) {
            throw new Exception\InvalidArgumentException('Invalid cookie path');
        }

        $this->_cookiePath = $cookiePath;
        $this->setStorageOption('cookie_path', $cookiePath);
        return $this;
    }

    /**
     * Get session.cookie_path
     * 
     * @return string
     */
    public function getCookiePath()
    {
        if (null === $this->_cookiePath) {
            $this->_cookiePath = $this->getStorageOption('cookie_path');
        }
        return $this->_cookiePath;
    }
    
    /**
     * Set session.cookie_domain
     * 
     * @param  string $cookieDomain 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setCookieDomain($cookieDomain)
    {
        if (!is_string($cookieDomain)) {
            throw new Exception\InvalidArgumentException('Invalid cookie domain: must be a string');
        }

        $validator = new HostnameValidator(HostnameValidator::ALLOW_ALL);

        if (!empty($cookieDomain) && !$validator->isValid($cookieDomain)) {
            throw new Exception\InvalidArgumentException('Invalid cookie domain: ' . implode('; ', $validator->getMessages()));
        }

        $this->_cookieDomain = $cookieDomain;
        $this->setStorageOption('cookie_domain', $cookieDomain);
        return $this;
    }

    /**
     * Get session.cookie_domain
     * 
     * @return string
     */
    public function getCookieDomain()
    {
        if (null === $this->_cookieDomain) {
            $this->_cookieDomain = $this->getStorageOption('cookie_domain');
        }
        return $this->_cookieDomain;
    }
    
    /**
     * Set session.cookie_secure
     * 
     * @param  bool $cookieSecure 
     * @return StandardConfiguration
     */
    public function setCookieSecure($cookieSecure)
    {
        $this->_cookieSecure = (bool) $cookieSecure;
        $this->setStorageOption('cookie_secure', $this->_cookieSecure);
        return $this;
    }

    /**
     * Get session.cookie_secure
     * 
     * @return bool
     */
    public function getCookieSecure()
    {
        if (null === $this->_cookieSecure) {
            $this->_cookieSecure = $this->getStorageOption('cookie_secure');
        }
        return $this->_cookieSecure;
    }
    
    /**
     * Set session.cookie_httponly
     *
     * case sensitive method lookups in setOptions means this method has an 
     * unusual casing
     *
     * @param  bool $cookieHTTPOnly
     * @return StandardConfiguration
     */
    public function setCookieHttponly($cookieHTTPOnly)
    {
        $this->_cookieHTTPOnly = (bool) $cookieHTTPOnly;
        $this->setStorageOption('cookie_httponly', $this->_cookieHTTPOnly);
        return $this;
    }

    /**
     * Get session.cookie_httponly
     * 
     * @return bool
     */
    public function getCookieHTTPOnly()
    {
        if (null === $this->_cookieHTTPOnly) {
            $this->_cookieHTTPOnly = $this->getStorageOption('cookie_httponly');
        }
        return $this->_cookieHTTPOnly;
    }
    
    /**
     * Set session.use_cookies
     * 
     * @param  bool $flag 
     * @return StandardConfiguration
     */
    public function setUseCookies($flag)
    {
        $this->_useCookies = (bool) $flag;
        $this->setStorageOption('use_cookies', $this->_useCookies);
        return $this;
    }

    /**
     * Get session.use_cookies
     * 
     * @return bool
     */
    public function getUseCookies()
    {
        if (null === $this->_useCookies) {
            $this->_useCookies = $this->getStorageOption('use_cookies');
        }
        return $this->_useCookies;
    }
    
    /**
     * Set session.entropy_file
     * 
     * @param  string $path 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setEntropyFile($path)
    {
        if (!file_exists($path) || is_dir($path) || !is_readable($path)) {
            throw new Exception\InvalidArgumentException('Invalid entropy_file provided');
        }
        $this->setOption('entropy_file', $path);
        $this->setStorageOption('entropy_file', $path);
        return $this;
    }

    /**
     * set session.entropy_length
     * 
     * @param  int $entropyLength 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setEntropyLength($entropyLength)
    {
        if (!is_numeric($entropyLength)) {
            throw new Exception\InvalidArgumentException('Invalid entropy_length; must be numeric');
        }
        if (0 > $entropyLength) {
            throw new Exception\InvalidArgumentException('Invalid entropy_length; must be a positive integer or zero');
        }

        $this->setOption('entropy_length', $entropyLength);
        $this->setStorageOption('entropy_length', $entropyLength);
        return $this;
    }

    /**
     * Set session.cache_expire
     * 
     * @param  int $cacheExpire 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setCacheExpire($cacheExpire)
    {
        if (!is_numeric($cacheExpire)) {
            throw new Exception\InvalidArgumentException('Invalid cache_expire; must be numeric');
        }

        $cacheExpire = (int) $cacheExpire;
        if (1 > $cacheExpire) {
            throw new Exception\InvalidArgumentException('Invalid cache_expire; must be a positive integer');
        }

        $this->setOption('cache_expire', $cacheExpire);
        $this->setStorageOption('cache_expire', $cacheExpire);
        return $this;
    }

    /**
     * Set session.hash_bits_per_character
     * 
     * @param  int $hashBitsPerCharacter 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setHashBitsPerCharacter($hashBitsPerCharacter)
    {
        if (!is_numeric($hashBitsPerCharacter)) {
            throw new Exception\InvalidArgumentException('Invalid hash bits per character provided');
        }
        $hashBitsPerCharacter = (int) $hashBitsPerCharacter;
        $this->setOption('hash_bits_per_character', $hashBitsPerCharacter);
        $this->setStorageOption('hash_bits_per_character', $hashBitsPerCharacter);
        return $this;
    }

    /**
     * Set remember_me_seconds
     * 
     * @param  int $rememberMeSeconds 
     * @return StandardConfiguration
     * @throws SessionException
     */
    public function setRememberMeSeconds($rememberMeSeconds)
    {
        if (!is_numeric($rememberMeSeconds)) {
            throw new Exception\InvalidArgumentException('Invalid remember_me_seconds; must be numeric');
        }

        $rememberMeSeconds = (int) $rememberMeSeconds;
        if (1 > $rememberMeSeconds) {
            throw new Exception\InvalidArgumentException('Invalid remember_me_seconds; must be a positive integer');
        }

        $this->_rememberMeSeconds = $rememberMeSeconds;
        $this->setStorageOption('remember_me_seconds', $rememberMeSeconds);
        return $this;
    }

    /**
     * Get remember_me_seconds
     * 
     * @return int
     */
    public function getRememberMeSeconds()
    {
        if (null === $this->_rememberMeSeconds) {
            $this->_rememberMeSeconds = $this->getStorageOption('remember_me_seconds');
        }
        return $this->_rememberMeSeconds;
    }
 
    /**
     * Set many options at once
     *
     * If a setter method exists for the key, that method will be called; 
     * otherwise, a standard option will be set with the value provided via 
     * {@link setOption()}.
     * 
     * @param  array $options 
     * @return StandardConfiguration
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            // translate key from underscore_separated to TitleCased
            $methodKey = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $method = 'set' . $methodKey;
            if (in_array($method, $methods)) {
                $this->$method($value);
            } else {
                $this->setOption($key, $value);
            }
        }
        return $this;
    }

    /**
     * Set an individual option
     *
     * Keys are normalized to lowercase. After setting internally, calls
     * {@link setStorageOption()} to allow further processing.
     * 
     * 
     * @param  string $option 
     * @param  mixed $value 
     * @return StandardConfiguration
     */
    public function setOption($option, $value)
    {
        $option = $this->_normalizeOption($option);
        $this->_options[$option] = $value;
        $this->setStorageOption($option, $value);
        return $this;
    }

    /**
     * Get an individual option
     *
     * Keys are normalized to lowercase. If the option is not found, attempts 
     * to retrieve it via {@link getStorageOption()}; if a value is returned
     * from that method, it will be set as the internal value and returned.
     *
     * Returns null for unfound options
     * 
     * @param  string $option 
     * @return mixed
     */
    public function getOption($option)
    {
        $option = $this->_normalizeOption($option);
        if (array_key_exists($option, $this->_options)) {
            return $this->_options[$option];
        }

        $value = $this->getStorageOption($option);
        if (null !== $value) {
            $this->setOption($option, $value);
            return $value;
        }

        return null;
    }

    /**
     * Check to see if an internal option has been set for the key provided.
     * 
     * @param  string $option 
     * @return bool
     */
    public function hasOption($option)
    {
        $option = $this->_normalizeOption($option);
        return array_key_exists($option, $this->_options);
    }

    /**
     * Cast configuration to an array
     * 
     * @return array
     */
    public function toArray()
    {
        $options = $this->_options;
        $extraOpts = array(
            'cookie_domain'       => $this->getCookieDomain(),
            'cookie_httponly'     => $this->getCookieHTTPOnly(),
            'cookie_lifetime'     => $this->getCookieLifetime(),
            'cookie_path'         => $this->getCookiePath(),
            'cookie_secure'       => $this->getCookieSecure(),
            'name'                => $this->getName(),
            'remember_me_seconds' => $this->getRememberMeSeconds(),
            'save_path'           => $this->getSavePath(),
            'use_cookies'         => $this->getUseCookies(),
        );
        return array_merge($options, $extraOpts);
    }

    /**
     * Intercept get*() and set*() methods
     *
     * Intercepts getters and setters and passes them to getOption() and setOption(), 
     * respectively.
     * 
     * @param  string $method 
     * @param  array $args 
     * @return mixed
     * @throws BadMethodCallException on non-getter/setter method
     */
    public function __call($method, $args)
    {
        if ('get' == substr($method, 0, 3)) {
            // Call to a getter; return matching option.
            // Transform method from MixedCase to underscore_separated.
            $option = substr($method, 3);
            $key    = $this->_getCamelCaseToUnderscoreFilter()->filter($option);
            return $this->getOption($key);
        }
        if ('set' == substr($method, 0, 3)) {
            // Call to a setter; return matching option.
            // Transform method from MixedCase to underscore_separated.
            $option = substr($method, 3);
            $key    = $this->_getCamelCaseToUnderscoreFilter()->filter($option);
            $value  = array_shift($args);
            return $this->setOption($key, $value);
        }
        throw new Exception\BadMethodCallException(sprintf('Method "%s" does not exist', $method));
    }

    /**
     * Normalize an option name to lowercase
     * 
     * @param  string $option 
     * @return string
     */
    protected function _normalizeOption($option)
    {
        return strtolower((string) $option);
    }

    /**
     * Retrieve the CamelCaseToUnderscoreFilter
     * 
     * @return CamelCaseToUnderscoreFilter
     */
    protected function _getCamelCaseToUnderscoreFilter()
    {
        if (null === $this->_camelCaseToUnderscoreFilter) {
            $this->_camelCaseToUnderscoreFilter = new CamelCaseToUnderscoreFilter();
        }
        return $this->_camelCaseToUnderscoreFilter;
    }
}
