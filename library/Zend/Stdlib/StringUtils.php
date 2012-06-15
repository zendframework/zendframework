<?php

namespace Zend\Stdlib;

use Zend\Loader\Broker,
    Zend\Loader\PluginBroker,
    Zend\Stdlib\StringAdapter\MbString as MbStringAdapter,
    Zend\Stdlib\StringAdapter\Iconv as IconvAdapter,
    Zend\Stdlib\StringAdapter\Native as NativeAdapter;

class StringUtils
{

    protected static $broker;
    protected static $singleByteCharsets = array(
        'ASCII', '7BIT', '8BIT',
        'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5',
        'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10',
        'ISO-8859-11', 'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16',
        'CP-1251', 'CP-1252'
        // TODO

    );

    /**
     * Get broker
     *
     * @return Zend\Loader\Broker
     */
    public static function getBroker()
    {
        if (static::$broker === null) {
            $broker = new PluginBroker();

            if (extension_loaded('mbstring')) {
                $broker->register('mbstring', new MbStringAdapter());
            }

            if (extension_loaded('iconv')) {
                $broker->register('iconv', new IconvAdapter());
            }

            $broker->register('native', new NativeAdapter());

            static::setBroker($broker);
        }
        return static::$broker;
    }

    public static function setBroker(Broker $broker)
    {
        static::$broker = $broker;
    }

    public static function resetBroker()
    {
        static::$broker = null;
    }

    public static function getAdapterByCharset($charset = 'UTF-8')
    {
        $broker = static::getBroker();
        foreach ($broker->getPlugins() as $adapter) {
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
