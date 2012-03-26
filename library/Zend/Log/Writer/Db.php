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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log\Writer;

use Zend\Log\Formatter,
    Zend\Log\Exception,
    Zend\Db\Adapter\Adapter;

/**
 * @uses       \Zend\Log\Exception\InvalidArgumentException
 * @uses       \Zend\Log\Exception\RuntimeException
 * @uses       \Zend\Log\Writer\AbstractWriter
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Db extends AbstractWriter
{   
    /**
     * Db adapter instance
     *
     * @var Adapter
     */
    protected $db;
    
    /**
     * Table name
     * 
     * @var string 
     */
    protected $table;

    /**
     * Relates database columns names to log data field keys.
     *
     * @var null|array
     */
    protected $columnMap;

    /**
     * Field separator for sub-elements
     * 
     * @var string 
     */
    protected $separator = '_';
    
    /**
     * Constructor
     *
     * @param Adapter $db 
     * @param string $table
     * @param array $columnMap
     * @param string $separator
     */
    public function __construct($db, $table, $columnMap = null, $separator = null)
    {
        $this->db        = $db;
        $this->table     = $table;
        $this->columnMap = $columnMap;
        
        if (!empty($separator)) {
            $this->separator = $separator;
        }
    }

    /**
     * Formatting is not possible on this writer
     *
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setFormatter(Formatter $formatter)
    {
        throw new Exception\InvalidArgumentException(get_class() . ' does not support formatting');
    }

    /**
     * Remove reference to database adapter
     *
     * @return void
     */
    public function shutdown()
    {
        $this->db = null;
    }

    /**
     * Write a message to the log.
     *
     * @param array $event event data
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function doWrite(array $event)
    {
        if (null === $this->db) {
            throw new Exception\RuntimeException('Database adapter is null');
        }

        // Transform the event array into fields
        $dataToInsert = array();
        if (null === $this->columnMap) {
            foreach ($event as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $key => $subvalue) {
                         $dataToInsert[$name . $this->separator . $key] = $subvalue;
                    }
                } else {
                    $dataToInsert[$name] = $value;
                }
            }
        } else {
            foreach ($event as $name => $value) {
                if (is_array($value)) {
                    foreach ($value as $key => $subvalue) {
                        if (isset($this->columnMap[$name][$key])) {
                            $dataToInsert[$this->columnMap[$name][$key]] = $subvalue;
                        }
                    }
                } elseif (isset($this->columnMap[$name])) {
                    $dataToInsert[$this->columnMap[$name]] = $value;
                } 
            }
        }

        $statement = $this->db->query($this->prepareInsert($this->db, $this->table, $dataToInsert));
        $statement->execute($dataToInsert);
        
    }
    /**
     * Prepare the INSERT SQL statement
     * 
     * @param  Adapter $db
     * @param  string $table
     * @param  array $fields
     * @return string 
     */
    protected function prepareInsert($db, $table, array $fields) 
    {               
        $sql = 'INSERT INTO ' . $db->platform->quoteIdentifier($table) . ' (' .
               implode(",",array_map(array($db->platform, 'quoteIdentifier'), $fields)) . ') VALUES (' .
               implode(",",array_map(array($db->driver, 'formatParameterName'), $fields)) . ')';
               
        return $sql;
    }
}
