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
 * @category  Zend
 * @package   Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Validator\File;
use Zend\Validator,
    Zend\Validator\Exception;

/**
 * Validator for counting all given files
 *
 * @uses      \Zend\Validator\AbstractValidator
 * @uses      \Zend\Validator\Exception
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Count extends Validator\AbstractValidator
{
    /**#@+
     * @const string Error constants
     */
    const TOO_MANY = 'fileCountTooMany';
    const TOO_FEW  = 'fileCountTooFew';
    /**#@-*/

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::TOO_MANY => "Too many files, maximum '%max%' are allowed but '%count%' are given",
        self::TOO_FEW  => "Too few files, minimum '%min%' are expected but '%count%' are given",
    );

    /**
     * @var array Error message template variables
     */
    protected $_messageVariables = array(
        'min'   => array('options' => 'min'),
        'max'   => array('options' => 'max'),
        'count' => '_count'
    );

    /**
     * Actual filecount
     *
     * @var integer
     */
    protected $_count;

    /**
     * Internal file array
     * @var array
     */
    protected $_files;

    /**
     * Options for this validator
     *
     * @var array
     */
    protected $options = array(
        'min' => null,  // Minimum file count, if null there is no minimum file count
        'max' => null,  // Maximum file count, if null there is no maximum file count
    );

    /**
     * Sets validator options
     *
     * Min limits the file count, when used with max=null it is the maximum file count
     * It also accepts an array with the keys 'min' and 'max'
     *
     * If $options is a integer, it will be used as maximum file count
     * As Array is accepts the following keys:
     * 'min': Minimum filecount
     * 'max': Maximum filecount
     *
     * @param  integer|array|\Zend\Config\Config $options Options for the adapter
     * @return void
     */
    public function __construct($options = null)
    {
        if (is_string($options) || is_numeric($options)) {
            $options = array('max' => $options);
        }

        if (1 < func_num_args()) {
            $options['min'] = func_get_arg(0);
            $options['max'] = func_get_arg(1);
        }

        parent::__construct($options);
    }

    /**
     * Returns the minimum file count
     *
     * @return integer
     */
    public function getMin()
    {
        return $this->options['min'];
    }

    /**
     * Sets the minimum file count
     *
     * @param  integer|array $min The minimum file count
     * @return \Zend\Validator\File\Count Provides a fluent interface
     * @throws \Zend\Validator\Exception When min is greater than max
     */
    public function setMin($min)
    {
        if (is_array($min) and isset($min['min'])) {
            $min = $min['min'];
        }

        if (!is_string($min) and !is_numeric($min)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        $min = (integer) $min;
        if (($this->getMax() !== null) && ($min > $this->getMax())) {
            throw new Exception\InvalidArgumentException("The minimum must be less than or equal to the maximum file count, but $min >"
                                            . " {$this->getMax()}");
        }

        $this->options['min'] = $min;
        return $this;
    }

    /**
     * Returns the maximum file count
     *
     * @return integer
     */
    public function getMax()
    {
        return $this->options['max'];
    }

    /**
     * Sets the maximum file count
     *
     * @param  integer|array $max The maximum file count
     * @return \Zend\Validator\StringLength Provides a fluent interface
     * @throws \Zend\Validator\Exception When max is smaller than min
     */
    public function setMax($max)
    {
        if (is_array($max) and isset($max['max'])) {
            $max = $max['max'];
        }

        if (!is_string($max) and !is_numeric($max)) {
            throw new Exception\InvalidArgumentException('Invalid options to validator provided');
        }

        $max = (integer) $max;
        if (($this->getMin() !== null) && ($max < $this->getMin())) {
            throw new Exception\InvalidArgumentException("The maximum must be greater than or equal to the minimum file count, but "
                                            . "$max < {$this->getMin()}");
        }

        $this->options['max'] = $max;
        return $this;
    }

    /**
     * Adds a file for validation
     *
     * @param string|array $file
     */
    public function addFile($file)
    {
        if (is_string($file)) {
            $file = array($file);
        }

        if (is_array($file)) {
            foreach ($file as $name) {
                if (!isset($this->_files[$name]) && !empty($name)) {
                    $this->_files[$name] = $name;
                }
            }
        }

        return $this;
    }

    /**
     * Returns true if and only if the file count of all checked files is at least min and
     * not bigger than max (when max is not null). Attention: When checking with set min you
     * must give all files with the first call, otherwise you will get an false.
     *
     * @param  string|array $value Filenames to check for count
     * @param  array        $file  File data from \Zend\File\Transfer\Transfer
     * @return boolean
     */
    public function isValid($value, $file = null)
    {
        if (($file !== null) && !array_key_exists('destination', $file)) {
            $file['destination'] = dirname($value);
        }

        if (($file !== null) && array_key_exists('tmp_name', $file)) {
            $value = $file['destination'] . DIRECTORY_SEPARATOR . $file['name'];
        }

        if (($file === null) || !empty($file['tmp_name'])) {
            $this->addFile($value);
        }

        $this->_count = count($this->_files);
        if (($this->getMax() !== null) && ($this->_count > $this->getMax())) {
            return $this->_throw($file, self::TOO_MANY);
        }

        if (($this->getMin() !== null) && ($this->_count < $this->getMin())) {
            return $this->_throw($file, self::TOO_FEW);
        }

        return true;
    }

    /**
     * Throws an error of the given type
     *
     * @param  string $file
     * @param  string $errorType
     * @return false
     */
    protected function _throw($file, $errorType)
    {
        if ($file !== null) {
            if (is_array($file)) {
                if(array_key_exists('name', $file)) {
                    $this->value = $file['name'];
                }
            } else if (is_string($file)) {
                $this->value = $file;
            }
        }

        $this->error($errorType);
        return false;
    }
}
