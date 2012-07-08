<?php

namespace Zend\Stdlib;

use Zend\Loader\Broker,
    Zend\Loader\PluginBroker,
    Zend\Stdlib\StringAdapter\StringAdapterInterface,
    Zend\Stdlib\StringAdapter\MbString as MbStringAdapter,
    Zend\Stdlib\StringAdapter\Iconv as IconvAdapter,
    Zend\Stdlib\StringAdapter\Native as NativeAdapter;

class StringUtils
{

    protected static $adapterRegistry;
    protected static $singleByteCharsets = array(
        'ASCII', '7BIT', '8BIT',
        'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
        'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
        'ISO-8859-11', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
        'CP-1251', 'CP-1252'
        // TODO

    );

    /**
     * Get registered string adapters
     *
     * @return Zend\Stdlib\StringAdapter\StringAdapterInterface[]
     */
    public static function getRegisteredAdapters()
    {
        if (static::$adapterRegistry === null) {
            static::$adapterRegistry = array();

            if (extension_loaded('mbstring')) {
                static::$adapterRegistry[] = new MbStringAdapter();
            }

            if (extension_loaded('iconv')) {
                static::$adapterRegistry[] = new IconvAdapter();
            }

            static::$adapterRegistry[] = new NativeAdapter();
        }

        return static::$adapterRegistry;
    }

    public static function registerAdapter(StringAdapterInterface $adapter)
    {
        if (!in_array($adapter, static::$adapterRegistry, true)) {
            static::$adapterRegistry[] = $adapter;
        }
    }

    public static function unregisterAdapter(StringAdapterInterface $adapter)
    {
        $index = array_search($adapter, static::$adapterRegistry, true);
        if ($index !== false) {
            unset(static::$adapterRegistry[$index]);
        }
    }

    public static function getAdapterByCharset($charset = 'UTF-8')
    {
        foreach (static::getRegisteredAdapters() as $adapter) {
            if ($adapter->isCharsetSupported($charset)) {
                return $adapter;
            }
        }

        throw new Exception\RuntimeException("No string adapter found for charset '{$charset}'");
    }

    public static function isSingleByteCharset($charset)
    {
        return in_array(strtoupper($charset), static::$singleByteCharsets);
    }
}
