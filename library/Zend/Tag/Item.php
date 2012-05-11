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
 * @package    Zend_Tag
 * @subpackage Item
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Tag;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Tag
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Item implements TaggableInterface
{
    /**
     * Title of the tag
     *
     * @var string
     */
    protected $_title = null;

    /**
     * Weight of the tag
     *
     * @var float
     */
    protected $_weight = null;

    /**
     * Custom parameters
     *
     * @var string
     */
    protected $_params = array();

    /**
     * Option keys to skip when calling setOptions()
     *
     * @var array
     */
    protected $_skipOptions = array(
        'options',
        'param'
    );

    /**
     * Create a new tag according to the options
     *
     * @param  array|Traversable $options
     * @throws \Zend\Tag\Exception\InvalidArgumentException When invalid options are provided
     * @throws \Zend\Tag\Exception\InvalidArgumentException When title was not set
     * @throws \Zend\Tag\Exception\InvalidArgumentException When weight was not set
     * @return void
     */
    public function __construct($options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException('Invalid options provided to constructor');
        }

        $this->setOptions($options);

        if ($this->_title === null) {
            throw new Exception\InvalidArgumentException('Title was not set');
        }

        if ($this->_weight === null) {
            throw new Exception\InvalidArgumentException('Weight was not set');
        }
    }

    /**
     * Set options of the tag
     *
     * @param  array $options
     * @return \Zend\Tag\Item
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if (in_array(strtolower($key), $this->_skipOptions)) {
                continue;
            }

            $method = 'set' . $key;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Defined by Zend\Tag\TaggableInterface
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set the title
     *
     * @param  string $title
     * @throws \Zend\Tag\Exception\InvalidArgumentException When title is no string
     * @return \Zend\Tag\Item
     */
    public function setTitle($title)
    {
        if (!is_string($title)) {
            throw new Exception\InvalidArgumentException('Title must be a string');
        }

        $this->_title = (string) $title;
        return $this;
    }

    /**
     * Defined by Zend\Tag\TaggableInterface
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->_weight;
    }

    /**
     * Set the weight
     *
     * @param  float $weight
     * @throws \Zend\Tag\Exception\InvalidArgumentException When weight is not numeric
     * @return \Zend\Tag\Item
     */
    public function setWeight($weight)
    {
        if (!is_numeric($weight)) {
            throw new Exception\InvalidArgumentException('Weight must be numeric');
        }

        $this->_weight = (float) $weight;
        return $this;
    }

    /**
     * Set multiple params at once
     *
     * @param  array $params
     * @return \Zend\Tag\Item
     */
    public function setParams(array $params)
    {
        foreach ($params as $name => $value) {
            $this->setParam($name, $value);
        }

        return $this;
    }

    /**
     * Defined by Zend\Tag\TaggableInterface
     *
     * @param  string $name
     * @param  mixed  $value
     * @return \Zend\Tag\Item
     */
    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
        return $this;
    }

    /**
     * Defined by Zend\Tag\TaggableInterface
     *
     * @param  string $name
     * @return mixed
     */
    public function getParam($name)
    {
        if (isset($this->_params[$name])) {
            return $this->_params[$name];
        }
        return null;
    }
}
