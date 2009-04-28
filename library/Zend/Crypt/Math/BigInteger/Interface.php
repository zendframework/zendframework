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
 * This class forms part of a proposal for the Zend Framework. The attached
 * copyright will be transferred to Zend Technologies USA Inc. upon future
 * acceptance of that proposal:
 *      http://framework.zend.com/wiki/pages/viewpage.action?pageId=20369
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage Math
 * @copyright  Copyright (c) 2007 Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Support for arbitrary precision mathematics in PHP.
 *
 * Interface for a wrapper across any PHP extension supporting arbitrary
 * precision maths.
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage Math
 * @author     Pádraic Brady (http://blog.astrumfutura.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Crypt_Math_BigInteger_Interface
{

    public function init($operand, $base = 10);
    public function add($left_operand, $right_operand);
    public function subtract($left_operand, $right_operand);
    public function compare($left_operand, $right_operand);
    public function divide($left_operand, $right_operand);
    public function modulus($left_operand, $modulus);
    public function multiply($left_operand, $right_operand);
    public function pow($left_operand, $right_operand);
    public function powmod($left_operand, $right_operand, $modulus);
    public function sqrt($operand);
    public function binaryToInteger($operand);
    public function integerToBinary($operand);
    public function hexToDecimal($operand);

}