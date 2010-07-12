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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Validator\Barcode;
use Zend\Validator;

/**
 * @uses       \Zend\Loader
 * @uses       \Zend\Validator\AbstractValidator
 * @uses       \Zend\Validator\Exception
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Barcode extends Validator\AbstractValidator
{
    const INVALID        = 'barcodeInvalid';
    const FAILED         = 'barcodeFailed';
    const INVALID_CHARS  = 'barcodeInvalidChars';
    const INVALID_LENGTH = 'barcodeInvalidLength';

    protected $_messageTemplates = array(
        self::FAILED         => "'%value%' failed checksum validation",
        self::INVALID_CHARS  => "'%value%' contains invalid characters",
        self::INVALID_LENGTH => "'%value%' should have a length of %length% characters",
        self::INVALID        => "Invalid type given, value should be string",
    );

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageVariables = array(
        'length' => '_length'
    );

    /**
     * Length for the set subtype
     *
     * @var integer
     */
    protected $_length;

    /**
     * Barcode adapter
     *
     * @var Zend_Validate_Barcode_BarcodeAdapter
     */
    protected $_adapter;

    /**
     * Generates the standard validator object
     *
     * @param  string|\Zend\Config\Config|
     *         Zend_Validate_Barcode_BarcodeAdapter $adapter Barcode adapter to use
     * @return void
     * @throws \Zend\Validator\Exception
     */
    public function __construct($adapter)
    {
        if ($adapter instanceof \Zend\Config\Config) {
            $adapter = $adapter->toArray();
        }

        $options  = null;
        $checksum = null;
        if (is_array($adapter)) {
            if (array_key_exists('options', $adapter)) {
                $options = $adapter['options'];
            }

            if (array_key_exists('checksum', $adapter)) {
                $checksum = $adapter['checksum'];
            }

            if (array_key_exists('adapter', $adapter)) {
                $adapter = $adapter['adapter'];
            } else {
                throw new Validator\Exception("Missing option 'adapter'");
            }
        }

        $this->setAdapter($adapter, $options);
        if ($checksum !== null) {
            $this->setChecksum($checksum);
        }
    }

    /**
     * Returns the set adapter
     *
     * @return Zend_Validate_Barcode_BarcodeAdapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Sets a new barcode adapter
     *
     * @param  string|\Zend\Validator\Barcode\Barcode $adapter Barcode adapter to use
     * @param  array  $options Options for this adapter
     * @return void
     * @throws \Zend\Validator\Exception
     */
    public function setAdapter($adapter, $options = null)
    {
        $adapter = ucfirst(strtolower($adapter));
        $adapter = '\\Zend\\Validator\\Barcode\\' . $adapter;
        if (\Zend\Loader::isReadable('Zend/Validator/Barcode/' . $adapter . '.php')) {
            $adapter = '\\Zend\\Validator\\Barcode\\' . $adapter;
        }

        if (!class_exists($adapter)) {
            throw new Validator\Exception('Barcode adapter matching "' . $adapter . '" not found');
        }

        $this->_adapter = new $adapter($options);
        if (!$this->_adapter instanceof Adapter) {
            throw new Validator\Exception(
                "Adapter " . $adapter . " does not implement Zend\\Validate\\Barcode\\Adapter"
            );
        }

        return $this;
    }

    /**
     * Returns the checksum option
     *
     * @return boolean
     */
    public function getChecksum()
    {
        return $this->getAdapter()->getCheck();
    }

    /**
     * Sets the checksum option
     *
     * @param  boolean $checksum
     * @return \Zend\Validator\Barcode\Barcode
     */
    public function setChecksum($checksum)
    {
        $this->getAdapter()->setCheck($checksum);
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value contains a valid barcode
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);
        $adapter       = $this->getAdapter();
        $this->_length = $adapter->getLength();
        $result        = $adapter->checkLength($value);
        if (!$result) {
            $this->_error(self::INVALID_LENGTH);
            return false;
        }

        $result = $adapter->checkChars($value);
        if (!$result) {
            $this->_error(self::INVALID_CHARS);
            return false;
        }

        if ($this->getChecksum()) {
            $result = $adapter->checksum($value);
            if (!$result) {
                $this->_error(self::FAILED);
                return false;
            }
        }

        return true;
    }
}
