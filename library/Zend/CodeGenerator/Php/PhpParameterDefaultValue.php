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
 * @subpackage Php
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\CodeGenerator\Php;

/**
 * A value-holder object for non-expressable parameter default values, such as null, booleans and empty array()
 *
 * @uses       \Zend\CodeGenerator\Php\Exception
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage Php
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhpParameterDefaultValue extends PhpValue
{
    
    protected $_outputMode = self::OUTPUT_SINGLE_LINE;
    
//    public function generate()
//    {
//        $indent = $this->_indentation;
//        $this->_indentation = '';
//        $output = parent::generate();
//        $output = str_replace(self::LINE_FEED, '', $output);
//        $this->_indentation = $indent;
//        return $output;
//    }
}
