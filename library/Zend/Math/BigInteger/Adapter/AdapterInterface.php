<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace Zend\Math\BigInteger\Adapter;

/**
 * @category   Zend
 * @package    Zend_Math
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface AdapterInterface
{
    /**
     * Base62 alphabet for arbitrary base conversion
     */
    const BASE62_ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Create adapter-specific representation of a big integer
     *
     * @abstract
     * @param string $operand
     * @param integer|null $base
     * @return mixed
     */
    public function init($operand, $base = null);

    /**
     * Add two big integers
     *
     * @abstract
     * @param string $leftOperand
     * @param string $rightOperand
     * @return string
     */
    public function add($leftOperand, $rightOperand);

    /**
     * Subtract two big integers
     *
     * @abstract
     * @param string $leftOperand
     * @param string $rightOperand
     * @return string
     */
    public function sub($leftOperand, $rightOperand);

    /**
     * Multiply two big integers
     *
     * @abstract
     * @param string $leftOperand
     * @param string $rightOperand
     * @return string
     */
    public function mul($leftOperand, $rightOperand);

    /**
     * Divide two big integers
     * (this method returns only int part of result)
     *
     * @abstract
     * @param string $leftOperand
     * @param string $rightOperand
     * @return string
     */
    public function div($leftOperand, $rightOperand);

    /**
     * Raise a big integers to another
     *
     * @param string $operand
     * @param string $exp
     * @return string
     */
    public function pow($operand, $exp);

    /**
     * Get the square root of a big integer
     *
     * @abstract
     * @param string $operand
     * @return string
     */
    public function sqrt($operand);

    /**
     * Get modulus of a big integer
     *
     * @abstract
     * @param string $leftOperand
     * @param string $modulus
     * @return string
     */
    public function mod($leftOperand, $modulus);

    /**
     * Raise a big integer to another, reduced by a specified modulus
     *
     * @abstract
     * @param string $leftOperand
     * @param string $rightOperand
     * @param string $modulus
     * @return string
     */
    public function powmod($leftOperand, $rightOperand, $modulus);

    /**
     * Compare two big integers
     *
     * @abstract
     * @param string $leftOperand
     * @param string $rightOperand
     * @return int
     */
    public function comp($leftOperand, $rightOperand);

    /**
     * Convert big integer into it's binary number representation
     *
     * @abstract
     * @param string $int
     * @param bool $twoc
     * @return string
     */
    public function intToBin($int, $twoc = false);

    /**
     * Convert binary number into big integer
     *
     * @abstract
     * @param string $bytes
     * @param bool $twoc
     * @return string
     */
    public function binToInt($bytes, $twoc = false);

    /**
     * Convert a number between arbitrary bases
     *
     * @abstract
     * @param string $operand
     * @param int $fromBase
     * @param int $toBase
     * @return string
     */
    public function baseConvert($operand, $fromBase, $toBase = 10);
}
