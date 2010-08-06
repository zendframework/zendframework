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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Validator\Db;

use Zend\Validator\AbstractValidator,
    Zend\Validator\Exception as ValidatorException,
    Zend\Config\Config,
    Zend\Db\Db,
    Zend\Db\Adapter\AbstractAdapter as AbstractDBAdapter,
    Zend\Db\Table\AbstractTable as AbstractTable,
    Zend\Db\Select as DBSelect;

/**
 * Class for Database record validation
 *
 * @uses       \Zend\Db\Db
 * @uses       \Zend\Db\Select
 * @uses       \Zend\Db\Table\AbstractTable
 * @uses       \Zend\Validator\AbstractValidator
 * @uses       \Zend\Validator\Exception
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractDb extends AbstractValidator
{
    /**
     * Error constants
     */
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';

    /**
     * @var array Message templates
     */
    protected $_messageTemplates = array(
        self::ERROR_NO_RECORD_FOUND => "No record matching '%value%' was found",
        self::ERROR_RECORD_FOUND    => "A record matching '%value%' was found",
    );

    /**
     * @var string
     */
    protected $_schema = null;

    /**
     * @var string
     */
    protected $_table = '';

    /**
     * @var string
     */
    protected $_field = '';

    /**
     * @var mixed
     */
    protected $_exclude = null;

    /**
     * Database adapter to use. If null isValid() will use Zend_Db::getInstance instead
     *
     * @var unknown_type
     */
    protected $_adapter = null;

    /**
     * Provides basic configuration for use with Zend_Validate_Db Validators
     * Setting $exclude allows a single record to be excluded from matching.
     * Exclude can either be a String containing a where clause, or an array with `field` and `value` keys
     * to define the where clause added to the sql.
     * A database adapter may optionally be supplied to avoid using the registered default adapter.
     *
     * The following option keys are supported:
     * 'table'   => The database table to validate against
     * 'schema'  => The schema keys
     * 'field'   => The field to check for a match
     * 'exclude' => An optional where clause or field/value pair to exclude from the query
     * 'adapter' => An optional database adapter to use
     *
     * @param array|\Zend\Config\Config $options Options to use for this validator
     */
    public function __construct($options)
    {
        if ($options instanceof Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
            $options       = func_get_args();
            $temp['table'] = array_shift($options);
            $temp['field'] = array_shift($options);
            if (!empty($options)) {
                $temp['exclude'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['adapter'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('table', $options) && !array_key_exists('schema', $options)) {
            throw new ValidatorException('Table or Schema option missing!');
        }

        if (!array_key_exists('field', $options)) {
            throw new ValidatorException('Field option missing!');
        }

        if (array_key_exists('adapter', $options)) {
            $this->setAdapter($options['adapter']);
        }

        if (array_key_exists('exclude', $options)) {
            $this->setExclude($options['exclude']);
        }

        $this->setField($options['field']);
        if (array_key_exists('table', $options)) {
            $this->setTable($options['table']);
        }

        if (array_key_exists('schema', $options)) {
            $this->setSchema($options['schema']);
        }
    }

    /**
     * Returns the set adapter
     *
     * @return Zend_Db_Adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Sets a new database adapter
     *
     * @param  \Zend\Db\Adapter\AbstractAdapter $adapter
     * @return \Zend\Validator\Db\AbstractDb
     */
    public function setAdapter($adapter)
    {
        if (!($adapter instanceof AbstractdBAdapter)) {
            throw new ValidatorException('Adapter option must be a database adapter!');
        }

        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Returns the set exclude clause
     *
     * @return string|array
     */
    public function getExclude()
    {
        return $this->_exclude;
    }

    /**
     * Sets a new exclude clause
     *
     * @param string|array $exclude
     * @return \Zend\Validator\Db\AbstractDb
     */
    public function setExclude($exclude)
    {
        $this->_exclude = $exclude;
        return $this;
    }

    /**
     * Returns the set field
     *
     * @return string|array
     */
    public function getField()
    {
        return $this->_field;
    }

    /**
     * Sets a new field
     *
     * @param string $field
     * @return \Zend\Validator\Db\AbstractDb
     */
    public function setField($field)
    {
        $this->_field = (string) $field;
        return $this;
    }

    /**
     * Returns the set table
     *
     * @return string
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Sets a new table
     *
     * @param string $table
     * @return \Zend\Validator\Db\AbstractDb
     */
    public function setTable($table)
    {
        $this->_table = (string) $table;
        return $this;
    }

    /**
     * Returns the set schema
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->_schema;
    }

    /**
     * Sets a new schema
     *
     * @param string $schema
     * @return \Zend\Validator\Db\AbstractDb
     */
    public function setSchema($schema)
    {
        $this->_schema = $schema;
        return $this;
    }

    /**
     * Run query and returns matches, or null if no matches are found.
     *
     * @param  String $value
     * @return Array when matches are found.
     */
    protected function _query($value)
    {
        /**
         * Check for an adapter being defined. if not, fetch the default adapter.
         */
        if ($this->_adapter === null) {
            $this->_adapter = AbstractTable::getDefaultAdapter();
            if (null === $this->_adapter) {
                throw new ValidatorException('No database adapter present');
            }
        }

        /**
         * Build select object
         */
        $select = new DBSelect($this->_adapter);
        $select->from($this->_table, array($this->_field), $this->_schema)
               ->where($this->_adapter->quoteIdentifier($this->_field, true).' = ?', $value);
        if ($this->_exclude !== null) {
            if (is_array($this->_exclude)) {
                $select->where($this->_adapter->quoteIdentifier($this->_exclude['field'], true).' != ?', $this->_exclude['value']);
            } else {
                $select->where($this->_exclude);
            }
        }
        $select->limit(1);

        /**
         * Run query
         */
        $result = $this->_adapter->fetchRow($select, array(), Db::FETCH_ASSOC);

        return $result;
    }
}
