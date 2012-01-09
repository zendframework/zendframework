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
namespace Zend\Validator;

/**
 * @uses       \Zend\Loader
 * @uses       \Zend\Validator\AbstractValidator
 * @uses       \Zend\Validator\Exception
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Barcode extends AbstractValidator
{
    const INVALID        = 'barcodeInvalid';
    const FAILED         = 'barcodeFailed';
    const INVALID_CHARS  = 'barcodeInvalidChars';
    const INVALID_LENGTH = 'barcodeInvalidLength';

    protected $_messageTemplates = array(
        self::FAILED         => "'%value%' failed checksum validation",
        self::INVALID_CHARS  => "'%value%' contains invalid characters",
        self::INVALID_LENGTH => "'%value%' should have a length of %length% characters",
        self::INVALID        => "Invalid type given. String expected",
    );

    /**
     * Additional variables available for validation failure messages
     *
     * @var array
     */
    protected $_messageVariables = array(
        'length' => array('options' => 'length'),
    );

    protected $options = array(
        'adapter'     => null,  // Barcode adapter Zend\Validator\Barcode\AbstractAdapter
        'options'     => null,  // Options for this adapter
        'length'      => null,
        'useChecksum' => null,
    );

    /**
     * Constructor for barcodes
     *
     * @param array|string $options Options to use
     */
    public function __construct($options = null)
    {
        if (!is_array($options) && !($options instanceof \Zend\Config\Config)) {
            $options = array('adapter' => $options);
        }

        if (array_key_exists('options', $options)) {
            $options['options'] = array('options' => $options['options']);
        }

        parent::__construct($options);
    }

    /**
     * Returns the set adapter
     *
     * @return Zend\Validate\Barcode\Adapter
     */
    public function getAdapter()
    {
        if (!($this->options['adapter'] instanceof Barcode\Adapter)) {
            $this->setAdapter('Ean13');
        }

        return $this->options['adapter'];
    }

    /**
     * Sets a new barcode adapter
     *
     * @param  string|\Zend\Validator\Barcode\Adapter $adapter Barcode adapter to use
     * @param  array  $options Options for this adapter
     * @return Zend\Validator\Barcode
     * @throws \Zend\Validator\Exception
     */
    public function setAdapter($adapter, $options = null)
    {
        if (is_string($adapter)) {
            $adapter = ucfirst(strtolower($adapter));
            $adapter = 'Zend\Validator\Barcode\\' . $adapter;
            if (\Zend\Loader::isReadable('Zend/Validator/Barcode/' . $adapter . '.php')) {
                $adapter = 'Zend\Validator\Barcode\\' . $adapter;
            }

            if (!class_exists($adapter)) {
                throw new Exception\InvalidArgumentException('Barcode adapter matching "' . $adapter . '" not found');
            }

            $this->options['adapter'] = new $adapter($options);
        }

        if (!$this->options['adapter'] instanceof Barcode\Adapter) {
            throw new Exception\InvalidArgumentException(
                "Adapter " . $adapter . " does not implement Zend\Validate\Barcode\Adapter"
            );
        }

        return $this;
    }

    /**
     * Returns the checksum option
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->getAdapter()->getChecksum();
    }

    /**
     * Sets if checksum should be validated, if no value is given the actual setting is returned
     *
     * @param  boolean $checksum
     * @return boolean
     */
    public function useChecksum($checksum = null)
    {
        return $this->getAdapter()->useChecksum($checksum);
    }

    /**
     * Defined by Zend\Validator\Validator
     *
     * Returns true if and only if $value contains a valid barcode
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->error(self::INVALID);
            return false;
        }

        $this->setValue($value);
        $adapter                 = $this->getAdapter();
        $this->options['length'] = $adapter->getLength();
        $result                  = $adapter->hasValidLength($value);
        if (!$result) {
            if (is_array($this->options['length'])) {
                $temp = $this->options['length'];
                $this->options['length'] = "";
                foreach($temp as $length) {
                    $this->options['length'] .= "/";
                    $this->options['length'] .= $length;
                }

                $this->options['length'] = substr($this->options['length'], 1);
            }

            $this->error(self::INVALID_LENGTH);
            return false;
        }

        $result = $adapter->hasValidCharacters($value);
        if (!$result) {
            $this->error(self::INVALID_CHARS);
            return false;
        }

        if ($this->useChecksum(null)) {
            $result = $adapter->hasValidChecksum($value);
            if (!$result) {
                $this->error(self::FAILED);
                return false;
            }
        }

        return true;
    }
}
