<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\TableGateway;

use Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage TableGateway
 */
class StaticAdapterTableGateway extends TableGateway
{
    /**
     * @var \Zend\Db\Adapter\Adapter[]
     */
    protected static $staticAdapters = array();

    /**
     * Set static adapter
     * 
     * @param Adapter $adapter 
     */
    public static function setStaticAdapter(Adapter $adapter)
    {
        $class = get_called_class();

        static::$staticAdapters[$class] = $adapter;
        if ($class === __CLASS__) {
            static::$staticAdapters[__CLASS__] = $adapter;
        }
    }

    /**
     * Get static adapter
     * 
     * @return type 
     */
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

    /**
     * Constructor
     * 
     * @param string $table
     * @param string $databaseSchema
     * @param ResultSet $selectResultPrototype 
     */
    public function __construct($table, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        $adapter = static::getStaticAdapter();
        parent::__construct($table, $adapter, $databaseSchema, $selectResultPrototype);
    }

}
