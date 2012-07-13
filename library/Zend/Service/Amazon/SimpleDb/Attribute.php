<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Amazon\SimpleDb;

/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage SimpleDb
 */
class Attribute
{
    protected $_itemName;
    protected $_name;
    protected $_values;

    /**
     * Constructor
     *
     * @param  string $itemName
     * @param  string $name
     * @param  array $values
     * @return void
     */
    public function __construct($itemName, $name, $values)
    {
        $this->_itemName = $itemName;
        $this->_name     = $name;

        if (!is_array($values)) {
            $this->_values = array($values);
        } else {
            $this->_values = $values;
        }
    }

    /**
     * Return the item name to which the attribute belongs
     *
     * @return string
     */
    public function getItemName ()
    {
        return $this->_itemName;
    }

    /**
     * Retrieve attribute values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Retrieve the attribute name
     *
     * @return string
     */
    public function getName ()
    {
        return $this->_name;
    }

    /**
     * Add value
     *
     * @param  mixed $value
     * @return void
     */
    public function addValue($value)
    {
        if (is_array($value)) {
             $this->_values += $value;
        } else {
            $this->_values[] = $value;
        }
    }

    public function setValues($values)
    {
        if (!is_array($values)) {
            $values = array($values);
        }
        $this->_values = $values;
    }
}
