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
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\TimeSync;

use ArrayObject;
use DateTime;
use IteratorAggregate;
use Zend\TimeSync\Exception;

/**
 * @category   Zend
 * @package    Zend_TimeSync
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TimeSync implements IteratorAggregate
{
    /**
     * Set the default timeserver protocol to "Ntp". This will be called
     * when no protocol is specified
     */
    const DEFAULT_PROTOCOL = 'Ntp';

    /**
     * Contains array of timeserver objects
     *
     * @var array
     */
    protected $_timeservers = array();

    /**
     * Holds a reference to the timeserver that is currently being used
     *
     * @var object
     */
    protected $_current;

    /**
     * Allowed timeserver schemes
     *
     * @var array
     */
    protected $_allowedSchemes = array(
        'Ntp',
        'Sntp',
    );

    /**
     * Configuration array, set using the constructor or using
     * ::setOptions() or ::setOption()
     *
     * @var array
     */
    public static $options = array(
        'timeout' => 1
    );

    /**
     * Zend_TimeSync constructor
     *
     * @param  string|array $target - OPTIONAL single timeserver, or an array of timeservers.
     * @param  string       $alias  - OPTIONAL an alias for this timeserver
     */
    public function __construct($target = null, $alias = null)
    {
        if ($target !== null) {
            $this->addServer($target, $alias);
        }
    }

    /**
     * getIterator() - return an iteratable object for use in foreach and the like,
     * this completes the IteratorAggregate interface
     *
     * @return ArrayObject
     */
    public function getIterator()
    {
        return new ArrayObject($this->_timeservers);
    }

    /**
     * Add a timeserver or multiple timeservers
     *
     * Server should be a single string representation of a timeserver,
     * or a structured array listing multiple timeservers.
     *
     * If you provide an array of timeservers in the $target variable,
     * $alias will be ignored. you can enter these as the array key
     * in the provided array, which should be structured as follows:
     *
     * <code>
     * $example = array(
     *   'server_a' => 'ntp://127.0.0.1',
     *   'server_b' => 'ntp://127.0.0.1:123',
     *   'server_c' => 'ntp://[2000:364:234::2.5]',
     *   'server_d' => 'ntp://[2000:364:234::2.5]:123'
     * );
     * </code>
     *
     * If no port number has been suplied, the default matching port
     * number will be used.
     *
     * Supported protocols are:
     * - ntp
     * - sntp
     *
     * @param  string|array $target - Single timeserver, or an array of timeservers.
     * @param  string       $alias  - OPTIONAL an alias for this timeserver
     * @throws Exception\ExceptionInterface
     */
    public function addServer($target, $alias = null)
    {
        if (is_array($target)) {
            foreach ($target as $key => $server) {
                $this->_addServer($server, $key);
            }
        } else {
            $this->_addServer($target, $alias);
        }
    }

    /**
     * Sets the value for the given options
     *
     * This will replace any currently defined options.
     *
     * @param   array $options - An array of options to be set
     */
    public static function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            self::$options[$key] = $value;
        }
    }

    /**
     * Marks a nameserver as current
     *
     * @param   string|integer $alias - The alias from the timeserver to set as current
     * @throws  Exception\InvalidArgumentException
     */
    public function setServer($alias)
    {
        if (isset($this->_timeservers[$alias]) === true) {
            $this->_current = $this->_timeservers[$alias];
        } else {
            throw new Exception\InvalidArgumentException("'$alias' does not point to valid timeserver");
        }
    }

    /**
     * Returns the value to the option
     *
     * @param   string $key - The option's identifier
     * @return  mixed
     * @throws  Exception\OutOfBoundsException
     */
    public static function getOptions($key = null)
    {
        if ($key == null) {
            return self::$options;
        }

        if (isset(self::$options[$key]) === true) {
            return self::$options[$key];
        } else {
            throw new Exception\OutOfBoundsException("'$key' does not point to valid option");
        }
    }

    /**
     * Return a specified timeserver by alias
     * If no alias is given it will return the current timeserver
     *
     * @param   string|integer $alias - The alias from the timeserver to return
     * @return  object
     * @throws  Exception\InvalidArgumentException
     */
    public function getServer($alias = null)
    {
        if ($alias === null) {
            if (isset($this->_current) && $this->_current !== false) {
                return $this->_current;
            } else {
                throw new Exception\InvalidArgumentException('there is no timeserver set');
            }
        }
        if (isset($this->_timeservers[$alias]) === true) {
            return $this->_timeservers[$alias];
        } else {
            throw new Exception\InvalidArgumentException("'$alias' does not point to valid timeserver");
        }
    }

    /**
     * Returns information sent/returned from the current timeserver
     *
     * @return  array
     */
    public function getInfo()
    {
        return $this->getServer()->getInfo();
    }

    /**
     * Query the timeserver list using the fallback mechanism
     *
     * If there are multiple servers listed, this method will act as a
     * facade and will try to return the date from the first server that
     * returns a valid result.
     *
     * @return DateTime
     * @throws Exception\RuntimeException
     */
    public function getDate()
    {
        foreach ($this->_timeservers as $alias => $server) {
            $this->_current = $server;
            try {
                return $server->getDate();
            } catch (Exception\ExceptionInterface $e) {
                if (!isset($masterException)) {
                    $masterException = new Exception\RuntimeException($e->getMessage(), $e->getCode());
                } else {
                    $masterException = new Exception\RuntimeException($e->getMessage(), $e->getCode(), $masterException);
                }
            }
        }

        throw new Exception\RuntimeException('All timeservers are bogus', 0, $masterException);
    }

    /**
     * Adds a timeserver object to the timeserver list
     *
     * @param  string|array $target   - Single timeserver, or an array of timeservers.
     * @param  string       $alias    - An alias for this timeserver
     * @throws Exception\RuntimeException
     */
    protected function _addServer($target, $alias)
    {
        $pos = strpos($target, '://');
        if ($pos) {
            $protocol = substr($target, 0, $pos);
            $address  = substr($target, $pos + 3);
        } else {
            $address  = $target;
            $protocol = self::DEFAULT_PROTOCOL;
        }

        $pos = strrpos($address, ':');
        if ($pos) {
            $posbr = strpos($address, ']');
            if ($posbr and ($pos > $posbr)) {
                $port = substr($address, $pos + 1);
                $address = substr($address, 0, $pos);
            } else if (!$posbr and $pos) {
                $port = substr($address, $pos + 1);
                $address = substr($address, 0, $pos);
            } else {
                $port = null;
            }
        } else {
            $port = null;
        }

        $protocol = ucfirst(strtolower($protocol));
        if (!in_array($protocol, $this->_allowedSchemes)) {
            throw new Exception\RuntimeException("'$protocol' is not a supported protocol");
        }

        $className = 'Zend\\TimeSync\\' . $protocol;
        $timeServerObj = new $className($address, $port);

        $this->_timeservers[$alias] = $timeServerObj;
    }
}
