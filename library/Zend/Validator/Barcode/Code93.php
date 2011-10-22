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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Validator\Barcode;

/**
 * @uses       \Zend\Validator\Barcode\AbstractAdapter
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Code93 extends AbstractAdapter
{
    /**
     * Note that the characters !"§& are only synonyms
     * @var array
     */
    protected $_check = array(
        '0' =>  0, '1' =>  1, '2' =>  2, '3' =>  3, '4' =>  4, '5' =>  5, '6' =>  6,
        '7' =>  7, '8' =>  8, '9' =>  9, 'A' => 10, 'B' => 11, 'C' => 12, 'D' => 13,
        'E' => 14, 'F' => 15, 'G' => 16, 'H' => 17, 'I' => 18, 'J' => 19, 'K' => 20,
        'L' => 21, 'M' => 22, 'N' => 23, 'O' => 24, 'P' => 25, 'Q' => 26, 'R' => 27,
        'S' => 28, 'T' => 29, 'U' => 30, 'V' => 31, 'W' => 32, 'X' => 33, 'Y' => 34,
        'Z' => 35, '-' => 36, '.' => 37, ' ' => 38, '$' => 39, '/' => 40, '+' => 41,
        '%' => 42, '!' => 43, '"' => 44, '§' => 45, '&' => 46,
    );

    /**
     * Constructor for this barcode adapter
     *
     * @return void
     */
    public function __construct()
    {
        $this->setLength(-1);
        $this->setCharacters('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ -.$/+%');
        $this->setChecksum('_code93');
        $this->useChecksum(false);
    }

    /**
     * Validates the checksum (Modulo CK)
     *
     * @param  string $value The barcode to validate
     * @return boolean
     */
    protected function _code93($value)
    {
        $checksum = substr($value, -2, 2);
        $value    = str_split(substr($value, 0, -2));
        $count    = 0;
        $length   = count($value) % 20;
        foreach($value as $char) {
            if ($length == 0) {
                $length = 20;
            }

            $count += $this->_check[$char] * $length;
            --$length;
        }

        $check   = array_search(($count % 47), $this->_check);
        $value[] = $check;
        $count   = 0;
        $length  = count($value) % 15;
        foreach($value as $char) {
            if ($length == 0) {
                $length = 15;
            }

            $count += $this->_check[$char] * $length;
            --$length;
        }
        $check .= array_search(($count % 47), $this->_check);

        if ($check == $checksum) {
            return true;
        }

        return false;
    }
}
