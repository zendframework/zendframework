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

namespace Zend\Log\Writer;

use Zend\Log\Formatter,
    Zend\Log\Exception,
    Zend\Db\Adapter\Adapter;

/**
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
    protected $tableName;

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
     * We used the Adapter instead of Zend\Db for a performance reason.
     * 
     * @param Adapter $db 
     * @param string $tableName
     * @param array $columnMap
     * @param string $separator
     */
    public function __construct(Adapter $db, $tableName, array $columnMap = null, $separator = null)
    {
        if ($db === null) {
            throw new Exception\InvalidArgumentException('You must pass a valid Zend\Db\Adapter\Adapter');
        }
        
        $this->db        = $db;
        $this->tableName = $tableName;
        $this->columnMap = $columnMap;
        
        if (!empty($separator)) {
            $this->separator = $separator;
        }
    }

    /**
     * Formatting is not possible on this writer
     *
     * @param Formatter\FormatterInterface $formatter
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function setFormatter(Formatter\FormatterInterface $formatter)
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
        if (null === $this->columnMap) {
            $dataToInsert = $this->eventIntoColumn($event);
        } else {
            $dataToInsert = $this->mapEventIntoColumn($event, $this->columnMap);
        }

        $statement = $this->db->query($this->prepareInsert($this->db, $this->tableName, $dataToInsert));
        $statement->execute($dataToInsert);
        
    }
    /**
     * Prepare the INSERT SQL statement
     * 
     * @param  Adapter $db
     * @param  string $tableName
     * @param  array $fields
     * @return string 
     */
    protected function prepareInsert(Adapter $db, $tableName, array $fields) 
    {               
        $sql = 'INSERT INTO ' . $db->platform->quoteIdentifier($tableName) . ' (' .
               implode(",",array_map(array($db->platform, 'quoteIdentifier'), $fields)) . ') VALUES (' .
               implode(",",array_map(array($db->driver, 'formatParameterName'), $fields)) . ')';
               
        return $sql;
    }
    /**
     * Map event into column using the $columnMap array
     * 
     * @param  array $event
     * @param  array $columnMap
     * @return array 
     */
    protected function mapEventIntoColumn(array $event, array $columnMap = null) 
    {
        if (empty($event)) {
            return array();
        }
        $data = array();
        foreach ($event as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $key => $subvalue) {
                    if (isset($columnMap[$name][$key])) {
                        $data[$columnMap[$name][$key]] = $subvalue;
                    }
                }
            } elseif (isset($columnMap[$name])) {
                $data[$columnMap[$name]] = $value;
            } 
        }
        return $data;
    }
    /**
     * Transform event into column for the db table
     * 
     * @param  array $event
     * @return array 
     */
    protected function eventIntoColumn(array $event) 
    {
        if (empty($event)) {
            return array();
        }
        $data = array();
        foreach ($event as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $key => $subvalue) {
                    $data[$name . $this->separator . $key] = $subvalue;
                }
            } else {
                $data[$name] = $value;
            }
        }
        return $data;
    }
}
