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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Log\Writer;

use Zend\Log\Formatter,
    Zend\Log\Exception,
    Zend\Db\Adapter\AbstractAdapter as DbAdapter;

/**
 * @uses       \Zend\Log\Exception\InvalidArgumentException
 * @uses       \Zend\Log\Exception\RuntimeException
 * @uses       \Zend\Log\Writer\AbstractWriter
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Writer
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Db extends AbstractWriter
{
    /**
     * Database adapter instance
     *
     * @var DbAdapter
     */
    protected $db;

    /**
     * Name of the log table in the database
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
     * Constructor
     *
     * @param DbAdapter $db Database adapter instance
     * @param string $table Log table in database
     * @param array $columnMap
     * @return Db
     */
    public function __construct($db, $table, $columnMap = null)
    {
        $this->db        = $db;
        $this->table     = $table;
        $this->columnMap = $columnMap;
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

        if (null === $this->columnMap) {
            $dataToInsert = $event;
        } else {
            $dataToInsert = array();
            foreach ($this->columnMap as $columnName => $fieldKey) {
                $dataToInsert[$columnName] = $event[$fieldKey];
            }
        }

        $this->db->insert($this->table, $dataToInsert);
    }
}
