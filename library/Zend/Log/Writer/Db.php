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
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Log\Writer;
use Zend\Log;

/**
 * @uses       \Zend\Log\Exception
 * @uses       \Zend\Log\Writer\AbstractWriter
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Db extends AbstractWriter
{
    /**
     * Database adapter instance
     * @var Zend_Db_Adapter
     */
    private $_db;

    /**
     * Name of the log table in the database
     * @var string
     */
    private $_table;

    /**
     * Relates database columns names to log data field keys.
     *
     * @var null|array
     */
    private $_columnMap;

    /**
     * Class constructor
     *
     * @param Zend_Db_Adapter $db   Database adapter instance
     * @param string $table         Log table in database
     * @param array $columnMap
     */
    public function __construct($db, $table, $columnMap = null)
    {
        $this->_db        = $db;
        $this->_table     = $table;
        $this->_columnMap = $columnMap;
    }

    /**
     * Create a new instance of Zend_Log_Writer_Db
     * 
     * @param  array|\Zend\Config\Config $config
     * @return \Zend\Log\Writer\Db
     * @throws \Zend\Log\Exception
     */
    static public function factory($config = array())
    {
        $config = self::_parseConfig($config);
        $config = array_merge(array(
            'db'        => null,
            'table'     => null,
            'columnMap' => null,
        ), $config);

        if (isset($config['columnmap'])) {
            $config['columnMap'] = $config['columnmap'];
        }

        return new self(
            $config['db'],
            $config['table'],
            $config['columnMap']
        );
    }

    /**
     * Formatting is not possible on this writer
     */
    public function setFormatter(\Zend\Log\Formatter $formatter)
    {
        throw new Log\Exception(get_class() . ' does not support formatting');
    }

    /**
     * Remove reference to database adapter
     *
     * @return void
     */
    public function shutdown()
    {
        $this->_db = null;
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  event data
     * @return void
     */
    protected function _write($event)
    {
        if ($this->_db === null) {
            throw new Log\Exception('Database adapter is null');
        }

        if ($this->_columnMap === null) {
            $dataToInsert = $event;
        } else {
            $dataToInsert = array();
            foreach ($this->_columnMap as $columnName => $fieldKey) {
                $dataToInsert[$columnName] = $event[$fieldKey];
            }
        }

        $this->_db->insert($this->_table, $dataToInsert);
    }
}
