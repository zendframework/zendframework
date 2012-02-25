<?php

namespace Zend\Db\TableGateway;

use Zend\Db\Adapter\Adapter;

class StaticAdapterTableGateway extends TableGateway
{
    /**
     * @var \Zend\Db\Adapter\Adapter[]
     */
    protected static $staticAdapters = array();

    public static function setStaticAdapter(Adapter $adapter)
    {
        $class = get_called_class();

        static::$staticAdapters[$class] = $adapter;
        if ($class === __CLASS__) {
            static::$staticAdapters[__CLASS__] = $adapter;
        }
    }

    public static function getStaticAdapter()
    {
        $class = get_called_class();

        // class specific adapter
        if (isset(static::$staticAdapters[$class])) {
            return static::$staticAdapters[$class];
        }

        // default adapter
        if (isset(static::$staticAdapters[__CLASS__])) {
            return static::$staticAdapters[__CLASS__];
        }

        throw new \Exception('No database adapter was found.');
    }

    public function __construct($tableName, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        $adapter = static::getStaticAdapter();
        parent::__construct($tableName, $adapter, $databaseSchema, $selectResultPrototype);
    }


}