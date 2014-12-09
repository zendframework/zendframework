<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

use Traversable;
use Zend\Stdlib\ArrayUtils;

class Blacklist extends AbstractFilter
{
    /**
     * @var array
     */
    protected $options = array(
        'list' => array(),
        'strict' => null,
    );

    /**
     * @param null|array|Traversable $options
     */
    public function __construct($options = null)
    {
        if (!is_null($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Determine whether the in_array() call should be "strict" or not. See in_array docs.
     *
     * @param  bool $strict
     * @return self
     */
    public function setStrict($strict = true)
    {
        $this->options['strict'] = $strict;

        return $this;
    }

    /**
     * Returns whether the in_array() call should be "strict" or not. See in_array docs.
     *
     * @return boolean
     */
    public function getStrict()
    {
        return $this->options['strict'];
    }

    /**
     * Set the list of items to black-list.
     *
     * @param  array|Traversable $list
     * @return $this
     */
    public function setList($list = array())
    {
        if ($list instanceof Traversable) {
            $list = ArrayUtils::iteratorToArray($list);
        }

        if (is_null($list)) {
            $list = array();
        }

        if (!is_array($list)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'List must be an array or an instance of Traversable (received "%s")',
                gettype($list)
            ));
        }

        $this->options['list'] = $list;

        return $this;
    }

    /**
     * Get the list of items to black-list
     *
     * @return array|Traversable
     */
    public function getList()
    {
        return $this->options['list'];
    }

    /**
     * Will return null if $value is present in the black-list.
     *
     * If $value is NOT present then it will return $value.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        $list = $this->getList();
        $strict = $this->getStrict();

        return in_array($value, $list, $strict) ? null : $value;
    }
}
