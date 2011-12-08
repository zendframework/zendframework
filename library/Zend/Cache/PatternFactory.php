<?php

namespace Zend\Cache;
use Zend\Cache\Exception\RuntimeException,
    Zend\Loader\Broker;

class PatternFactory
{

    /**
     * The pattern broker
     *
     * @var null|Zend\Loader\Broker
     */
    protected static $broker = null;

    /**
     * Instantiate a cache pattern
     *
     * @param string|Zend\Cache\Pattern $patternName
     * @param array|Zend\Config $options
     * @return Zend\Cache\Pattern
     * @throws Zend\Cache\RuntimeException
     */
    public static function factory($patternName, $options = array())
    {
        if ($patternName instanceof Pattern) {
            $patternName->setOptions($options);
            return $patternName;
        }

        return self::getBroker()->load($patternName, $options);
    }

    /**
     * Get the pattern broker
     *
     * @return Zend\Loader\Broker
     */
    public static function getBroker()
    {
        if (self::$broker === null) {
            self::$broker = self::_getDefaultBroker();
        }

        return self::$broker;
    }

    /**
     * Set the pattern broker
     *
     * @param Zend\Loader\Broker $broker
     * @return void
     */
    public static function setBroker(Broker $broker)
    {
        self::$broker = $broker;
    }

    /**
     * Reset pattern broker to default
     *
     * @return void
     */
    public static function resetBroker()
    {
        self::$broker = null;
    }

    /**
     * Get internal pattern broker
     *
     * @return Zend\Loader\Broker
     */
    protected static function _getDefaultBroker()
    {
        return new PatternBroker();
    }

}
