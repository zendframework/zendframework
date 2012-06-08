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

use Zend\Math\BigInteger\Exception as BigIntegerException;

/**
 * GMP extension adapter
 *
 * @category   Zend
 * @package    Zend_Math
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Gmp implements AdapterInterface
{
    /**
     * Create GMP number resource representing big integer from string
     *
     * @param string $operand
     * @param integer|null $base
     * @return bool|resource
     */
    public function init($operand, $base = null)
    {
        $sign = (strpos($operand, '-') === 0) ? '-' : '';
        $operand = ltrim($operand, '-+');

        if (null === $base) {
            if (preg_match('#^(?:([1-9])\.)?([0-9]+)[eE]\+?([0-9]+)$#', $operand, $m)) {
                if (!empty($m[1])) {
                    if ($m[3] < strlen($m[2])) {
                        return false;
                    }
                } else {
                    $m[1] = '';
                }
                $operand = str_pad(($m[1] . $m[2]), ($m[3] + 1), '0', STR_PAD_RIGHT);
            } else {
                $base = 0;
            }
        }

        return gmp_init($sign . $operand, $base);
    }

    /**
     * Add two big integers
     *
     * @param string $leftOperand
     * @param string $rightOperand
     * @return string
     */
    public function add($leftOperand, $rightOperand)
    {
        $result = gmp_add($leftOperand, $rightOperand);
        return gmp_strval($result);
    }

    /**
     * Subtract two big integers
     *
     * @param string $leftOperand
     * @param string $rightOperand
     * @return string
     */
    public function sub($leftOperand, $rightOperand)
    {
        $result = gmp_sub($leftOperand, $rightOperand);
        return gmp_strval($result);
    }

    /**
     * Multiply two big integers
     *
     * @param string $leftOperand
     * @param string $rightOperand
     * @return string
     */
    public function mul($leftOperand, $rightOperand)
    {
        $result = gmp_mul($leftOperand, $rightOperand);
        return gmp_strval($result);
    }

    /**
     * Divide two big integers and return integer part result
     * Raises exception if the denominator is zero
     *
     * @param string $leftOperand
     * @param string $rightOperand
     * @return string|null
     * @throws \Zend\Math\BigInteger\Exception\DivisionByZeroException
     */
    public function div($leftOperand, $rightOperand)
    {
        $result = @gmp_div_q($leftOperand, $rightOperand);
        if (false === $result) {
            throw new BigIntegerException\DivisionByZeroException(
                "Division by zero: {$leftOperand} / {$rightOperand}"
            );
        }

        return gmp_strval($result);
    }

    /**
     * Raise a big integers to another
     *
     * @param string $operand
     * @param string $exp
     * @return string
     */
    public function pow($operand, $exp)
    {
        $result = gmp_pow($operand, $exp);
        return gmp_strval($result);
    }

    /**
     * Get the square root of a big integer
     *
     * @param string $operand
     * @return string
     */
    public function sqrt($operand)
    {
        $result = gmp_sqrt($operand);
        return gmp_strval($result);
    }

    /**
     * Get modulus of a big integer
     *
     * @param string $leftOperand
     * @param string $modulus
     * @return string
     */
    public function mod($leftOperand, $modulus)
    {
        $result = gmp_mod($leftOperand, $modulus);
        return gmp_strval($result);
    }

    /**
     * Raise a big integer to another, reduced by a specified modulus
     *
     * @param string $leftOperand
     * @param string $rightOperand
     * @param string $modulus
     * @return string
     */
    public function powmod($leftOperand, $rightOperand, $modulus)
    {
        $result = gmp_powm($leftOperand, $rightOperand, $modulus);
        return gmp_strval($result);
    }

    /**
     * Compare two big integers and returns result as an integer where
     * 0 means both are equal,
     * > 0 value - that leftOperand is larger,
     * < 0 value - rightOperand is larger.
     *
     * @param  string $leftOperand
     * @param  string $rightOperand
     * @return int
     */
    public function comp($leftOperand, $rightOperand)
    {
        $result = gmp_cmp($leftOperand, $rightOperand);
        return ($result !== 0) ? (($result > 0) ? 1: -1 ) : 0;
    }

    /**
     * Convert big integer into it's binary number representation
     *
     * @param string $int
     * @param bool $twoc  return in twos' compliment form
     * @return string
     */
    public function intToBin($int, $twoc = false)
    {
        $nb = chr(0);
        $isNegative = (strpos($int, '-') === 0) ? true : false;
        $int = ltrim($int, '+-0');

        if (empty($int)) {
            return $nb;
        }

        if ($isNegative && $twoc) {
            $int = gmp_sub($int, '1');
        }

        $hex  = gmp_strval($int, 16);
        if (strlen($hex) & 1) {
            $hex = '0' . $hex;
        }

        $bytes = pack('H*', $hex);
        $bytes = ltrim($bytes, $nb);

        if ($twoc) {
            if (ord($bytes[0]) & 0x80) {
                $bytes = $nb . $bytes;
            }
            return $isNegative ? ~$bytes : $bytes;
        } else {
            return $bytes;
        }
    }

    /**
     * Convert binary number into big integer
     *
     * @abstract
     * @param string $bytes
     * @param bool $twoc  whether binary number is in twos' compliment form
     * @return string
     */
    public function binToInt($bytes, $twoc = false)
    {
        $isNegative = ((ord($bytes[0]) & 0x80) && $twoc);

        $sign = '';
        if ($isNegative) {
            $bytes = ~$bytes;
            $sign = '-';
        }

        $result = gmp_init($sign . bin2hex($bytes), 16);

        if ($isNegative) {
            $result = gmp_sub($result, '1');
        }

        return gmp_strval($result);
    }

    /**
     * Base conversion. Bases 2..62 are supported
     *
     * @param string $operand
     * @param int    $fromBase
     * @param int    $toBase
     * @return string
     * @throws \Zend\Math\BigInteger\Exception\InvalidArgumentException
     */
    public function baseConvert($operand, $fromBase, $toBase = 10)
    {
        if ($fromBase == $toBase) {
            return $operand;
        }

        if ($fromBase < 2 || $fromBase > 62) {
            throw new BigIntegerException\InvalidArgumentException(
                "Unsupported base: {$fromBase}, should be 2..62"
            );
        } else   if ($toBase < 2 || $toBase > 62) {
            throw new BigIntegerException\InvalidArgumentException(
                "Unsupported base: {$toBase}, should be 2..62"
            );
        } else if ($fromBase <= 36 && $toBase <= 36) {
            return gmp_strval(gmp_init($operand, $fromBase), $toBase);
        }

        $sign = (strpos($operand, '-') === 0) ? '-' : '';
        $operand = ltrim($operand, '-+');

        $chars = self::BASE62_ALPHABET;

        // convert operand to decimal
        if ($fromBase !== 10 ) {
            $decimal = '0';
            for ($i = 0, $len  = strlen($operand); $i < $len; $i++) {
                $decimal = gmp_mul($decimal, $fromBase);
                $decimal = gmp_add($decimal, strpos($chars, $operand[$i]));
            }
        } else {
            $decimal = gmp_init($operand);
        }

        if ($toBase == 10) {
            return gmp_strval($decimal);
        }

        // convert decimal to base
        $result = '';
        do {
            list($decimal, $remainder) = gmp_div_qr($decimal, $toBase);
            $pos = gmp_strval($remainder);
            $result = $chars[$pos] . $result;
        } while (gmp_cmp($decimal, '0'));

        return $sign . $result;
    }
}