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
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Serializer\Adapter;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
