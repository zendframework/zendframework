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

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 */
interface AdapterInterface
{
    /**
     * Constructor
     *
     * @param  array|\Traversable $options Serializer options
     */
    public function __construct($options = array());

    /**
     * Set serializer options
     *
     * @param  array|\Traversable $options Serializer options
     * @return AdapterInterface
     */
    public function setOptions($options);

    /**
     * Set a serializer option
     *
     * @param  string $name Option name
     * @param  mixed $value Option value
     * @return AdapterInterface
     */
    public function setOption($name, $value);

    /**
     * Get serializer options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Get a serializer option
     *
     * @param  string $name
     * @return mixed
     * @throws AdapterInterface
     */
    public function getOption($name);

    /**
     * Generates a storable representation of a value.
     *
     * @param  mixed $value Data to serialize
     * @param  array $options Serialize options
     * @return string
     * @throws \Zend\Serializer\Exception\ExceptionInterface
     */
    public function serialize($value, array $options = array());

    /**
     * Creates a PHP value from a stored representation.
     *
     * @param  string $serialized Serialized string
     * @param  array $options Unserialize options
     * @return mixed
     * @throws \Zend\Serializer\Exception\ExceptionInterface
     */
    public function unserialize($serialized, array $options = array());
}
