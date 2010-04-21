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
 * @package    Zend_CodeGenerator
 * @subpackage PHP
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\CodeGenerator\PHP;

/**
 * @uses       \Zend\CodeGenerator\PHP\AbstractPHP
 * @uses       \Zend\CodeGenerator\PHP\Exception
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PHPPropertyValue extends PHPValue
{
    
    protected $_topMostValue = true;
    
    public function setValue($value)
    {
        parent::setValue($value);
        $type = $this->_type;
        if ($type == self::TYPE_AUTO) {
            $type = $this->_getAutodeterminedType($value);
        }
        
        if ($type == self::TYPE_ARRAY) {
            foreach (new \RecursiveArrayIterator($value) as $value) {
                if ((is_object($value) && (!$value instanceof self)) || is_resource($value)) {
                    throw new Exception('PHPPropertyValue values must be of a non-object, non-resource type inside an array');
                }
                if ($value instanceof self) {
                    $value->_topMostValue = false;
                }
            }
        } else {
            if (is_object($value) || is_resource($value)) {
                throw new Exception('PHPPropertyValue values must be of a non-object, non-resource type');
            }
        }

        return $this;
    }
    
    public function generate()
    {
        $output = parent::generate();
        if ($this->_topMostValue) {
            $output .= ';';
        }
        return $output;
    }
}
