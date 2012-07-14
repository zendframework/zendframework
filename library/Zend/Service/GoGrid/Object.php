<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\GoGrid;

class Object
{
     /**
     * Attributes of the result
     *
     * @var array
     */
    private $_attributes= array();

    /**
     * __construct
     *
     * @param array $data
     */
    public function __construct($data=array())
    {
        if (!empty($data) && is_array($data)) {
            $this->_attributes= $data;
        }
    }
    /**
     * Get Attribute with a specific key
     *
     * @param array $data
     * @return misc|boolean
     */
    public function getAttribute($key)
    {
        if (array_key_exists($key,$this->_attributes)) {
            return $this->_attributes[$key];
        }
        return false;
    }
}
