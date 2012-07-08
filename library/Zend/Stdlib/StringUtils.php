<?php

namespace Zend\Stdlib;

use Zend\Loader\Broker,
    Zend\Loader\PluginBroker,
    Zend\Stdlib\StringWrapper\StringWrapperInterface,
    Zend\Stdlib\StringWrapper\MbString as MbStringWrapper,
    Zend\Stdlib\StringWrapper\Iconv as IconvWrapper,
    Zend\Stdlib\StringWrapper\Native as NativeWrapper;

class StringUtils
{

    protected static $wrapperRegistry;
    protected static $singleByteCharsets = array(
        'ASCII', '7BIT', '8BIT',
        'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
        'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
        'ISO-8859-11', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
        'CP-1251', 'CP-1252'
        // TODO

    );

    /**
     * Get registered wrappers
     *
     * @return Zend\Stdlib\StringWrapper\StringWrapperInterface[]
     */
    public static function getRegisteredWrappers()
    {
        if (static::$wrapperRegistry === null) {
            static::$wrapperRegistry = array();

            if (extension_loaded('mbstring')) {
                static::$wrapperRegistry[] = new MbStringWrapper();
            }

            if (extension_loaded('iconv')) {
                static::$wrapperRegistry[] = new IconvWrapper();
            }

            static::$wrapperRegistry[] = new NativeWrapper();
        }

        return static::$wrapperRegistry;
    }

    public static function registerWrapper(StringWrapperInterface $wrapper)
    {
        if (!in_array($wrapper, static::$wrapperRegistry, true)) {
            static::$wrapperRegistry[] = $wrapper;
        }
    }

    public static function unregisterWrapper(StringWrapperInterface $wrapper)
    {
        $index = array_search($wrapper, static::$wrapperRegistry, true);
        if ($index !== false) {
            unset(static::$wrapperRegistry[$index]);
        }
    }

    public static function getWrapper($charset = 'UTF-8')
    {
        foreach (static::getRegisteredWrappers() as $wrapper) {
            if ($wrapper->isCharsetSupported($charset)) {
                return $wrapper;
            }
        }

        throw new Exception\RuntimeException("No wrapper found for charset '{$charset}'");
    }

    public static function isSingleByteCharset($charset)
    {
        return in_array(strtoupper($charset), static::$singleByteCharsets);
    }
}
