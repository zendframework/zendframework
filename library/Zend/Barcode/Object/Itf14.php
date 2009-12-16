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
 * @package    Zend_Barcode
 * @subpackage Object
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** @see Zend_Barcode_Object_Int25 */
require_once 'Zend/Barcode/Object/Int25.php';

/** @see Zend_Validate_Barcode */
require_once 'Zend/Validate/Barcode.php';

/**
 * Class for generate Itf14 barcode
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Barcode_Object_Itf14 extends Zend_Barcode_Object_Int25
{
    /**
     * Constructor
     * @param array|Zend_Config $options
     * @return void
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        // Checksum is mandatory with Itf14
        $this->_withChecksum = true;

        // Default to true but not mandatory
        $this->_withChecksumInText = true;
    }

    /**
     * Activate/deactivate the automatic generation
     * of the checksum character
     * added to the barcode text
     * @param boolean $value
     * @return Zend_Barcode_Object
     */
    public function setWithChecksum($value)
    {
        // Checksum is mandatory with Itf14
        return $this;
    }

    /**
     * Activate/deactivate the automatic generation
     * of the checksum character
     * added to the barcode text
     * @param boolean $value
     * @return Zend_Barcode_Object
     * @throw Zend_Barcode_Object_Exception
     */
    public function setWithChecksumInText($value)
    {
        return $this;
    }

    /**
     * Retrieve text to encode
     * @return string
     */
    public function getText()
    {
        $text = $this->_getTextWithChecksum();
        if (strlen($text) < 14) {
            $text = str_repeat('0', 14 - strlen($text)) . $text;
        }
        return $text;
    }

    /**
     * Retrieve text to display
     * @return string
     */
    public function getTextToDisplay()
    {
        return $this->getText();
    }

    /**
     * Check allowed characters
     * @param string $value
     * @return string
     * @throw Zend_Barcode_Object_Exception
     */
    public function validateText($value)
    {
        $validator = new Zend_Validate_Barcode(array(
            'adapter'  => 'itf14',
            'checksum' => false,
        ));

        // prepend '0'
        if (strlen($value) < 13) {
            $value = str_repeat('0', 13 - strlen($value)) . $value;
        }

        // add a '0' because checksum is mandatory
        if (!$validator->isValid($value . '0')) {
            $message = implode("\n", $validator->getMessages());

            /**
             * @see Zend_Barcode_Object_Exception
             */
            require_once 'Zend/Barcode/Object/Exception.php';
            throw new Zend_Barcode_Object_Exception($message);
        }
    }
}
