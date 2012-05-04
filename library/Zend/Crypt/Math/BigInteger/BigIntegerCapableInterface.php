<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Package
 */

namespace Zend\Crypt\Math\BigInteger;

/**
 * Support for arbitrary precision mathematics in PHP.
 *
 * Interface for a wrapper across any PHP extension supporting arbitrary
 * precision maths.
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface BigIntegerCapableInterface
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
