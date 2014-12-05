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

class Whitelist extends AbstractFilter
{
    const TYPE_WHITELIST = 1;
    const TYPE_BLACKLIST = 2;

    /**
     * @var array
     */
    protected $types = array(
        'whitelist' => self::TYPE_WHITELIST,
        'blacklist' => self::TYPE_BLACKLIST,
    );

    /**
     * @var array
     */
    protected $options = array(
        'type' => self::TYPE_WHITELIST,
        'list' => array(),
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
     * Set list type
     *
     * @param $type
     * @throws Exception\InvalidArgumentException
     * @return self
     */
    public function setType($type)
    {
        if (is_string($type) && array_key_exists($type, $this->types)) {
            $type = $this->types[$type];
        }

        if ($type !== self::TYPE_WHITELIST && $type !== self::TYPE_BLACKLIST) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Unknown type value.',
                $type,
                gettype($type)
            ));
        }

        $this->options['type'] = $type;
        return $this;
    }

    /**
     * Returns the defined list type
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->options['type'];
    }

    /**
     * Set the list of items to white/black list
     *
     * @param array $list
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
     * Get the list of items to white/black list
     *
     * @return mixed
     */
    public function getList()
    {
        return $this->options['list'];
    }

    /**
     * Will return $value if its present (whitelist mode) or absent (blacklist mode) from the value list.
     *
     * If $value is rejected then it will return null.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function filter($value)
    {
        $type = $this->getType();
        $list = $this->getList();

        $exists = in_array($value, $list);
        $allow = $type == self::TYPE_WHITELIST ?
            $exists :
            !$exists;

        return $allow ? $value : null;
    }
}
