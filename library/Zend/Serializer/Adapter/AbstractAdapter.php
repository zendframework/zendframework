<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Serializer
 */

namespace Zend\Serializer\Adapter;

use Traversable;
use Zend\Serializer\Adapter\AdapterInterface as SerializationAdapter;
use Zend\Serializer\Exception\InvalidArgumentException;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 */
abstract class AbstractAdapter implements SerializationAdapter
{
    /**
     * Serializer options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Constructor
     *
     * @param  array|Traversable $options Serializer options
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Set serializer options
     *
     * @param  array|Traversable $options Serializer options
     * @return AbstractAdapter
     */
    public function setOptions($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        } else {
            $options = (array) $options;
        }

        foreach ($options as $k => $v) {
            $this->setOption($k, $v);
        }
        return $this;
    }

    /**
     * Set a serializer option
     *
     * @param  string $name Option name
     * @param  mixed $value Option value
     * @return AbstractAdapter
     */
    public function setOption($name, $value)
    {
        $this->_options[(string) $name] = $value;
        return $this;
    }

    /**
     * Get serializer options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Get a serializer option
     *
     * @param  string $name
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getOption($name)
    {
        $name = (string) $name;
        if (!array_key_exists($name, $this->_options)) {
            throw new InvalidArgumentException("Unknown option '{$name}'");
        }

        return $this->_options[$name];
    }
}
