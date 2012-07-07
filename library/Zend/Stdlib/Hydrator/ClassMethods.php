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
 * @package    Zend_Stdlib
 * @subpackage Hydrator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Stdlib\Hydrator;

use Zend\Stdlib\Exception;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage Hydrator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ClassMethods implements HydratorInterface
{
    /**
     * Flag defining whether array keys are underscore-separated (true) or camel case (false)
     * @var boolean
     */
    protected $underscoreSeparatedKeys;
    
    /**
     * Define if extract values will use camel case or name with underscore
     * @param boolean $underscoreSeparatedKeys 
     */
    public function __construct($underscoreSeparatedKeys = true)
    {
        $this->underscoreSeparatedKeys = $underscoreSeparatedKeys;
    }
    
    /**
     * Extract values from an object with class methods
     *
     * Extracts the getter/setter of the given $object.
     * 
     * @param  object $object 
     * @return array
     * @throws Exception\BadMethodCallException for a non-object $object
     */
    public function extract($object)
    {
        if (!is_object($object)) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided $object to be a PHP object)',
                __METHOD__
            ));
        }
        
        $transform = function($letters)
        {
            $letter = array_shift($letters);
            return '_' . strtolower($letter);
        };
        $attributes = array();
        $methods = get_class_methods($object);
        foreach($methods as $method) {
            if(preg_match('/^get[A-Z]\w*/', $method)) {
                // setter verification
                $setter = preg_replace('/^get/', 'set', $method);
                if(!in_array($setter, $methods)) {
                    continue;
                }
                $attribute = substr($method, 3);
                $attribute = lcfirst($attribute);
                if ($this->underscoreSeparatedKeys) {
                    $attribute = preg_replace_callback('/([A-Z])/', $transform, $attribute);
                }

                $result = $object->$method();

                // Recursively extract if object contains itself other objects or arrays of objects
                if (is_object($result)) {
                    $result = $this->extract($result);
                } elseif (is_array($result)) {
                    foreach ($result as $key => $value) {
                        if (is_object($value)) {
                            $result[$key] = $this->extract($value);
                        }
                    }
                }

                $attributes[$attribute] = $result;
            }
        }
        
        return $attributes;
    }

    /**
     * Hydrate an object by populating getter/setter methods
     *
     * Hydrates an object by getter/setter methods of the object.
     * 
     * @param  array $data 
     * @param  object $object 
     * @return object
     * @throws Exception\BadMethodCallException for a non-object $object
     */
    public function hydrate(array $data, $object)
    {
        if (!is_object($object)) {
            throw new Exception\BadMethodCallException(sprintf(
                '%s expects the provided $object to be a PHP object)',
                __METHOD__
            ));
        }
        
        $transform = function($letters)
        {   
            $letter = substr(array_shift($letters), 1, 1);
            return ucfirst($letter);
        };

        foreach ($data as $property => $value) {
            if ($this->underscoreSeparatedKeys) {
                $property = preg_replace_callback('/(_[a-z])/', $transform, $property);
            }
            $method = 'set' . ucfirst($property);
            if (method_exists($object, $method)) {
                $object->$method($value);
            }
        }
        return $object;
    }
}
