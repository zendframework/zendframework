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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Issn extends AbstractAdapter
{
    /**
     * Constructor for this barcode adapter
     *
     * @return void
     */
    public function __construct()
    {
        $this->setLength(array(8, 13));
        $this->setCharacters('0123456789X');
        $this->setChecksum('_gtin');
    }

    /**
     * Allows X on length of 8 chars
     *
     * @param  string $value The barcode to check for allowed characters
     * @return boolean
     */
    public function hasValidCharacters($value)
    {
        if (strlen($value) != 8) {
            if (strpos($value, 'X') !== false) {
                return false;
            }
        }

        return parent::hasValidCharacters($value);
    }

    /**
     * Validates the checksum
     *
     * @param  string $value The barcode to check the checksum for
     * @return boolean
     */
    public function hasValidChecksum($value)
    {
        if (strlen($value) == 8) {
            $this->setChecksum('_issn');
        } else {
            $this->setChecksum('_gtin');
        }

        return parent::hasValidChecksum($value);
    }

    /**
     * Validates the checksum ()
     * ISSN implementation (reversed mod11)
     *
     * @param  string $value The barcode to validate
     * @return boolean
     */
    protected function _issn($value)
    {
        $checksum = substr($value, -1, 1);
        $values   = str_split(substr($value, 0, -1));
        $check    = 0;
        $multi    = 8;
        foreach($values as $token) {
            if ($token == 'X') {
                $token = 10;
            }

            $check += ($token * $multi);
            --$multi;
        }

        $check %= 11;
        $check  = ($check === 0 ? 0 : (11 - $check));
        if ($check == $checksum) {
            return true;
        } else if (($check == 10) && ($checksum == 'X')) {
            return true;
        }

        return false;
    }
}
