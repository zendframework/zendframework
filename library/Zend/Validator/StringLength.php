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
namespace Zend\Validator;

/**
 * @uses       \Zend\Validator\AbstractValidator
 * @uses       \Zend\Validator\Exception
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StringLength extends AbstractValidator
{
    const INVALID   = 'stringLengthInvalid';
    const TOO_SHORT = 'stringLengthTooShort';
    const TOO_LONG  = 'stringLengthTooLong';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "Invalid type given. String expected",
        self::TOO_SHORT => "'%value%' is less than %min% characters long",
        self::TOO_LONG  => "'%value%' is more than %max% characters long",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'min' => array('options' => 'min'),
        'max' => array('options' => 'max'),
    );

    protected $options = array(
        'min'      => 0, // Minimum length
        'max'      => null, // Maximum length, null if there is no length limitation
        'encoding' => null, // Encoding to use
    );

    /**
     * Sets validator options
     *
     * @param  integer|array|\Zend\Config\Config $options
     * @return void
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            $options     = func_get_args();
            $temp['min'] = array_shift($options);
            if (!empty($options)) {
                $temp['max'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['encoding'] = array_shift($options);
            }

            $options = $temp;
        }

        parent::__construct($options);
    }

    /**
     * Returns the min option
     *
     * @return integer
     */
    public function getMin()
    {
        return $this->options['min'];
    }

    /**
     * Sets the min option
     *
     * @param  integer $min
     * @throws \Zend\Validator\Exception
     * @return \Zend\Validator\StringLength Provides a fluent interface
     */
    public function setMin($min)
    {
        if (null !== $this->getMax() && $min > $this->getMax()) {
            throw new Exception\InvalidArgumentException("The minimum must be less than or equal to the maximum length, but $min >"
                                            . " " . $this->getMax());
        }

        $this->options['min'] = max(0, (integer) $min);
        return $this;
    }

    /**
     * Returns the max option
     *
     * @return integer|null
     */
    public function getMax()
    {
        return $this->options['max'];
    }

    /**
     * Sets the max option
     *
     * @param  integer|null $max
     * @throws \Zend\Validator\Exception
     * @return \Zend\Validator\StringLength Provides a fluent interface
     */
    public function setMax($max)
    {
        if (null === $max) {
            $this->options['max'] = null;
        } else if ($max < $this->getMin()) {
            throw new Exception\InvalidArgumentException("The maximum must be greater than or equal to the minimum length, but "
                                            . "$max < " . $this->getMin());
        } else {
            $this->options['max'] = (integer) $max;
        }

        return $this;
    }

    /**
     * Returns the actual encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->options['encoding'];
    }

    /**
     * Sets a new encoding to use
     *
     * @param string $encoding
     * @return \Zend\Validator\StringLength
     */
    public function setEncoding($encoding = null)
    {
        if ($encoding !== null) {
            $orig   = iconv_get_encoding('internal_encoding');
            $result = iconv_set_encoding('internal_encoding', $encoding);
            if (!$result) {
                throw new Exception\InvalidArgumentException('Given encoding not supported on this OS!');
            }

            iconv_set_encoding('internal_encoding', $orig);
        }

        $this->options['encoding'] = $encoding;
        return $this;
    }

    /**
     * Returns true if and only if the string length of $value is at least the min option and
     * no greater than the max option (when the max option is not null).
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
        if ($this->getEncoding() !== null) {
            $length = iconv_strlen($value, $this->getEncoding());
        } else {
            $length = iconv_strlen($value);
        }

        if ($length < $this->getMin()) {
            $this->error(self::TOO_SHORT);
        }

        if (null !== $this->getMax() && $this->getMax() < $length) {
            $this->error(self::TOO_LONG);
        }

        if (count($this->getMessages())) {
            return false;
        } else {
            return true;
        }
    }
}
