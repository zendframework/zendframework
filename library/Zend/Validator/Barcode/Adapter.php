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
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Adapter
{
    /**
     * Checks the length of a barcode
     *
     * @param  string $value  The barcode to check for proper length
     * @return boolean
     */
    public function hasValidLength($value);

    /**
     * Checks for allowed characters within the barcode
     *
     * @param  string $value The barcode to check for allowed characters
     * @return boolean
     */
    public function hasValidCharacters($value);

    /**
     * Validates the checksum
     *
     * @param string $value The barcode to check the checksum for
     * @return boolean
     */
    public function hasValidChecksum($value);

    /**
     * Returns the allowed barcode length
     *
     * @return integer
     */
    public function getLength();

    /**
     * Returns the allowed characters
     *
     * @return integer|string|array
     */
    public function getCharacters();

    /**
     * Returns if barcode uses a checksum
     *
     * @return boolean
     */
    public function getChecksum();

    /**
     * Sets the checksum validation, if no value is given, the actual setting is returned
     *
     * @param  boolean $check
     * @return \Zend\Validator\Barcode\AbstractAdapter|boolean
     */
    public function useChecksum($check = null);
}
