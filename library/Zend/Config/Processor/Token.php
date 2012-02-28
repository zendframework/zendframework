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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Config\Processor;

use Zend\Config\Config,
    Zend\Config\Processor,
    Zend\Config\Exception,
    \Traversable,
    \ArrayObject;

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Token implements Processor
{
    /**
     * Token prefix.
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Token suffix.
     *
     * @var string
     */
    protected $suffix = '';

    /**
     * The registry of tokens
     *
     * @var array
     */
    protected $tokens = array();

    /**
     * Replacement map
     *
     * @var array
     */
    protected $map = null;

    /**
     * Token Processor walks through a Config structure and replaces all
     * occurences of tokens with supplied values.
     *
     * @param  array|\Zend\Config\Config|ArrayObject|\Traversable   $tokens  Associative array of TOKEN => value
     *                                                                      to replace it with
     * @param string $prefix
     * @param string $suffix
     * @internal param array $options
     * @return \Zend\Config\Processor\Token
     */
    public function __construct($tokens = array(), $prefix = '', $suffix = '')
    {
        $this->setTokens($tokens);
        $this->setPrefix($prefix);
        $this->setSuffix($suffix);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * @param string $prefix
     * @return mixed
     */
    public function setPrefix($prefix)
    {
        // reset map
        $this->map = null;

        return $this->prefix = $prefix;
    }

    /**
     * @param string $suffix
     * @return string
     */
    public function setSuffix($suffix)
    {
        // reset map
        $this->map = null;

        return $this->suffix = $suffix;
    }

    /**
     * Set token registry.
     *
     * @param  array|\Zend\Config\Config|\ArrayObject|\Traversable   $tokens  Associative array of TOKEN => value
     *                                                                      to replace it with
     * @throws \Zend\Config\Exception\InvalidArgumentException
     */
    public function setTokens($tokens)
    {
        if (is_array($tokens)) {
            $this->tokens = $tokens;
        } elseif ($tokens instanceof Config) {
            $this->tokens = $tokens->toArray();
        } elseif ($tokens instanceof \Traversable) {
            $this->tokens = array();
            foreach ($tokens as $key => $val) {
                $this->tokens[$key] = $val;
            }
        } else {
            throw new Exception\InvalidArgumentException('Cannot use ' . gettype($tokens) . ' as token registry.');
        }

        // reset map
        $this->map = null;
    }

    /**
     * Get current token registry.
     * @return array
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Add new token.
     *
     * @param $token
     * @param $value
     * @throws \Zend\Config\Exception\InvalidArgumentException
     */
    public function addToken($token, $value)
    {
        if (!is_scalar($token)) {
            throw new Exception\InvalidArgumentException('Cannot use ' . gettype($token) . ' as token name.');
        }
        $this->tokens[$token] = $value;

        // reset map
        $this->map = null;
    }

    /**
     * Add new token.
     *
     * @param $token
     * @param $value
     * @throws \Zend\Config\Exception\InvalidArgumentException
     */
    public function setToken($token, $value)
    {
        return $this->addToken($token, $value);
    }

    /**
     * Build replacement map
     */
    protected function buildMap()
    {
        if (!$this->suffix && !$this->prefix) {
            $this->map = $this->tokens;
        } else {
            $this->map = array();
            foreach ($this->tokens as $token => $value) {
                $this->map[$this->prefix . $token . $this->suffix] = $value;
            }
	}
    }

    /**
     * Process
     * 
     * @param  Config $config
     * @return Config 
     */
    public function process(Config $config)
    {
        if ($config->isReadOnly()) {
            throw new Exception\InvalidArgumentException('Cannot parse config because it is read-only');
        }

        if ($this->map === null) {
            $this->buildMap();
        }

        /**
         * Walk through config and replace values
         */
        $keys = array_keys($this->map);
        $values = array_values($this->map);
        foreach ($config as $key => $val) {
            if ($val instanceof Config) {
                $this->process($val);
            } else {
                $config->$key = str_replace($keys,$values,$val);
            }
        }

        return $config;
    }

    /**
     * Process a single value
     *
     * @param $value
     * @return mixed
     */
    public function processValue($value)
    {
        if ($this->map === null) {
            $this->buildMap();
        }
        $keys = array_keys($this->map);
        $values = array_values($this->map);
        return str_replace($keys,$values,$value);
    }
}