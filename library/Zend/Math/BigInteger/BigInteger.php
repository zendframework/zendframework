<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Math
 */

namespace Zend\Math\BigInteger;

use Zend\Math\BigInteger\Adapter\AdapterInterface;
use Zend\Math\BigInteger\AdapterBroker;
use Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Math
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BigInteger
{
    /**
     * Broker for loading adapters
     *
     * @var null|Zend\Loader\Broker
     */
    protected static $adapterBroker = null;

    /**
     * The default adapter.
     *
     * @var Zend\Math\BigInteger\Adapter\AdapterInterface
     */
    protected static $defaultAdapter = null;

    /**
     * Create a BigInteger adapter instance
     *
     * @static
     * @param string|null $adapterName
     * @return AdapterInterface
     */
    public static function factory($adapterName = null)
    {
        if (null === $adapterName) {
            return static::getDefaultAdapter();
        } else if ($adapterName instanceof AdapterInterface) {
            return $adapterName;
        } else {
            return self::getAdapterBroker()->load($adapterName);
        }
    }

    /**
     * Set adapter broker
     *
     * @static
     * @param Broker $broker
     */
    public static function setAdapterBroker(Broker $broker)
    {
        self::$adapterBroker = $broker;
    }

    /**
     * Get the adapter broker
     *
     * @return Broker
     */
    public static function getAdapterBroker()
    {
        if (static::$adapterBroker === null) {
            static::$adapterBroker = new AdapterBroker();
        }
        return static::$adapterBroker;
    }

    /**
     * Set default BigInteger adapter
     *
     * @static
     * @param string|AdapterInterface $adapter
     */
    public static function setDefaultAdapter($adapter)
    {
        static::$defaultAdapter = static::factory($adapter);
    }

    /**
     * Get default BigInteger adapter
     *
     * @static
     * @return null|AdapterInterface
     */
    public static function getDefaultAdapter()
    {
        if (null === static::$defaultAdapter) {
            static::$defaultAdapter = static::getAvailableAdapter();
        }
        return static::$defaultAdapter;
    }

    /**
     * Determine and return available adapter
     *
     * @static
     * @return AdapterInterface
     * @throws Exception\RuntimeException
     */
    public static function getAvailableAdapter()
    {
        if (extension_loaded('gmp')) {
            $adapterName = 'Gmp';
        } elseif (extension_loaded('bcmath')) {
            $adapterName = 'Bcmath';
        } else {
            throw new Exception\RuntimeException('Big integer math support is not detected');
        }
        return static::factory($adapterName);
    }

    /**
     * Cal adapter methods statically
     *
     * @static
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $adapter = static::getDefaultAdapter();
        return call_user_func_array(array($adapter, $method), $args);
    }
}