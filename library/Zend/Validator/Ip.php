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
class Ip extends AbstractValidator
{
    const INVALID        = 'ipInvalid';
    const NOT_IP_ADDRESS = 'notIpAddress';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID        => "Invalid type given. String expected",
        self::NOT_IP_ADDRESS => "'%value%' does not appear to be a valid IP address",
    );

    /**
     * internal options
     *
     * @var array
     */
    protected $_options = array(
        'allowipv6' => true,
        'allowipv4' => true
    );

    /**
     * Sets validator options
     *
     * @param array $options OPTIONAL Options to set, see the manual for all available options
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            $options = func_get_args();
            $temp['allowipv6'] = array_shift($options);
            if (!empty($options)) {
                $temp['allowipv4'] = array_shift($options);
            }

            $options = $temp;
        }

        $options += $this->_options;
        $this->setOptions($options);
    }

    /**
     * Returns all set options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets the options for this validator
     *
     * @param array $options
     * @return \Zend\Validator\Ip
     */
    public function setOptions($options)
    {
        if (array_key_exists('allowipv6', $options)) {
            $this->_options['allowipv6'] = (boolean) $options['allowipv6'];
        }

        if (array_key_exists('allowipv4', $options)) {
            $this->_options['allowipv4'] = (boolean) $options['allowipv4'];
        }

        if (!$this->_options['allowipv4'] && !$this->_options['allowipv6']) {
            throw new Exception\InvalidArgumentException('Nothing to validate. Check your options');
        }

        return $this;
    }

    /**
     * Returns true if and only if $value is a valid IP address
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $this->_setValue($value);
        if (($this->_options['allowipv4'] && !$this->_options['allowipv6'] && !$this->_validateIPv4($value)) ||
            (!$this->_options['allowipv4'] && $this->_options['allowipv6'] && !$this->_validateIPv6($value)) ||
            ($this->_options['allowipv4'] && $this->_options['allowipv6'] && !$this->_validateIPv4($value) && !$this->_validateIPv6($value))) {
            $this->_error(self::NOT_IP_ADDRESS);
            return false;
        }

        return true;
    }

    /**
     * Validates an IPv4 address
     *
     * @param string $value
     */
    protected function _validateIPv4($value) {
        if (preg_match('/^([01]{8}.){3}[01]{8}$/i', $value)) {
            // binary format  00000000.00000000.00000000.00000000
            $value = bindec(substr($value, 0, 8)) . "." . bindec(substr($value, 9, 8)) . "."
                   . bindec(substr($value, 18, 8)) . "." . bindec(substr($value, 27, 8));
        } else if (preg_match('/^([0-9]{3}.){3}[0-9]{3}$/i', $value)) {
            // octet format 777.777.777.777
            $value = (int) substr($value, 0, 3) . "." . (int) substr($value, 4, 3) . "."
                   . (int) substr($value, 8, 3) . "." . (int) substr($value, 12, 3);
        } else if (preg_match('/^([0-9a-f]{2}.){3}[0-9a-f]{2}$/i', $value)) {
            // hex format ff.ff.ff.ff
            $value = hexdec(substr($value, 0, 2)) . "." . hexdec(substr($value, 3, 2)) . "."
                   . hexdec(substr($value, 6, 2)) . "." . hexdec(substr($value, 9, 2));
        }

        $ip2long = ip2long($value);
        if($ip2long === false) {
            return false;
        }

        return $value == long2ip($ip2long);
    }

    /**
     * Validates an IPv6 address
     *
     * @param  string $value Value to check against
     * @return boolean True when $value is a valid ipv6 address
     *                 False otherwise
     */
    protected function _validateIPv6($value) {
        if (strlen($value) < 3) {
            return $value == '::';
        }

        if (strpos($value, '.')) {
            $lastcolon = strrpos($value, ':');
            if (!($lastcolon && $this->_validateIPv4(substr($value, $lastcolon + 1)))) {
                return false;
            }

            $value = substr($value, 0, $lastcolon) . ':0:0';
        }

        if (strpos($value, '::') === false) {
            return preg_match('/\A(?:[a-f0-9]{1,4}:){7}[a-f0-9]{1,4}\z/i', $value);
        }

        $colonCount = substr_count($value, ':');
        if ($colonCount < 8) {
            return preg_match('/\A(?::|(?:[a-f0-9]{1,4}:)+):(?:(?:[a-f0-9]{1,4}:)*[a-f0-9]{1,4})?\z/i', $value);
        }

        // special case with ending or starting double colon
        if ($colonCount == 8) {
            return preg_match('/\A(?:::)?(?:[a-f0-9]{1,4}:){6}[a-f0-9]{1,4}(?:::)?\z/i', $value);
        }

        return false;
    }
}
