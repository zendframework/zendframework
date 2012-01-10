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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Filter;

/**
 * Compresses a given string
 *
 * @uses       Zend\Filter\Exception
 * @uses       Zend\Filter\AbstractFilter
 * @uses       Zend\Loader
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Compress extends AbstractFilter
{
    /**
     * Compression adapter
     */
    protected $_adapter = 'Gz';

    /**
     * Compression adapter constructor options
     */
    protected $_adapterOptions = array();

    /**
     * Class constructor
     *
     * @param string|array $options (Optional) Options to set
     */
    public function __construct($options = null)
    {
        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }
        if (is_string($options)) {
            $this->setAdapter($options);
        } elseif ($options instanceof Compress\CompressionAlgorithm) {
            $this->setAdapter($options);
        } elseif (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set filter setate
     *
     * @param  array $options
     * @return \Zend\Filter\Compress
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if ($key == 'options') {
                $key = 'adapterOptions';
            }
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    /**
     * Returns the current adapter, instantiating it if necessary
     *
     * @return string
     */
    public function getAdapter()
    {
        if ($this->_adapter instanceof Compress\CompressionAlgorithm) {
            return $this->_adapter;
        }

        $adapter = $this->_adapter;
        $options = $this->getAdapterOptions();
        if (!class_exists($adapter)) {
            $adapter = 'Zend\\Filter\\Compress\\' . ucfirst($adapter);
            if (!class_exists($adapter)) {
                throw new Exception\RuntimeException(sprintf(
                    '%s unable to load adapter; class "%s" not found',
                    __METHOD__,
                    $this->_adapter
                ));
            }
        }

        $this->_adapter = new $adapter($options);
        if (!$this->_adapter instanceof Compress\CompressionAlgorithm) {
            throw new Exception\InvalidArgumentException("Compression adapter '" . $adapter . "' does not implement Zend\\Filter\\Compress\\CompressionAlgorithm");
        }
        return $this->_adapter;
    }

    /**
     * Retrieve adapter name
     *
     * @return string
     */
    public function getAdapterName()
    {
        return $this->getAdapter()->toString();
    }

    /**
     * Sets compression adapter
     *
     * @param  string|\Zend\Filter\Compress\CompressInterface $adapter Adapter to use
     * @return \Zend\Filter\Compress\Compress
     */
    public function setAdapter($adapter)
    {
        if ($adapter instanceof Compress\CompressionAlgorithm) {
            $this->_adapter = $adapter;
            return $this;
        }
        if (!is_string($adapter)) {
            throw new Exception\InvalidArgumentException('Invalid adapter provided; must be string or instance of Zend\\Filter\\Compress\\CompressionAlgorithm');
        }
        $this->_adapter = $adapter;

        return $this;
    }

    /**
     * Retrieve adapter options
     *
     * @return array
     */
    public function getAdapterOptions()
    {
        return $this->_adapterOptions;
    }

    /**
     * Set adapter options
     *
     * @param  array $options
     * @return void
     */
    public function setAdapterOptions(array $options)
    {
        $this->_adapterOptions = $options;
        return $this;
    }

    /**
     * Calls adapter methods
     *
     * @param string       $method  Method to call
     * @param string|array $options Options for this method
     */
    public function __call($method, $options)
    {
        $adapter = $this->getAdapter();
        if (!method_exists($adapter, $method)) {
            throw new Exception\BadMethodCallException("Unknown method '{$method}'");
        }

        return call_user_func_array(array($adapter, $method), $options);
    }

    /**
     * Defined by Zend_Filter_Filter
     *
     * Compresses the content $value with the defined settings
     *
     * @param  string $value Content to compress
     * @return string The compressed content
     */
    public function filter($value)
    {
        return $this->getAdapter()->compress($value);
    }
}
