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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * Class for Database record validation
 *
 * @category   Zend
 * @package    Zend_Validate
 * @uses       Zend_Validate_Abstract
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Validate_Db_Abstract extends Zend_Validate_Abstract
{
    /**
     * Error constants
     */
    const ERROR_NO_RECORD_FOUND = 'noRecordFound';
    const ERROR_RECORD_FOUND    = 'recordFound';

    /**
     * @var array Message templates
     */
    protected $_messageTemplates = array(self::ERROR_NO_RECORD_FOUND => 'No record matching %value% was found',
                                         self::ERROR_RECORD_FOUND    => 'A record matching %value% was found');

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
     * @param array|Zend_Config $options Options to use for this validator
     */
    public function __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (func_num_args() > 1) {
// @todo: Preperation for 2.0... needs to be cleared with the dev-team
//          trigger_error('Multiple constructor options are deprecated in favor of a single options array', E_USER_NOTICE);

            $options = func_get_args();
            $temp['table'] = array_shift($options);
            $temp['field'] = array_shift($options);
            if (!empty($options)) {
                $options['exclude'] = array_shift($options);
            }

            if (!empty($options)) {
                $options['adapter'] = array_shift($options);
            }

            $options = $temp;
        }

        if (!array_key_exists('table', $options) && !array_key_exists('schema', $options)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Table or Schema option missing!');
        }

        if (!array_key_exists('field', $options)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception('Field option missing!');
        }

        if (array_key_exists('adapter', $options)) {
            if (!($options['adapter'] instanceof Zend_Db_Adapter_Abstract)) {
                require_once 'Zend/Validate/Exception.php';
                throw new Zend_Validate_Exception('Adapter option must be a database adapter!');
            }

            $this->_adapter = $options['adapter'];
        }

        if (array_key_exists('exclude', $options)) {
            $this->_exclude = $options['exclude'];
        }

        $this->_field   = (string) $options['field'];
        if (array_key_exists('table', $options)) {
            $this->_table = (string) $options['table'];
        }

        if (array_key_exists('schema', $options)) {
            $this->_schema = $options['schema'];
        }
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
            $this->_adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
            if (null === $this->_adapter) {
                require_once 'Zend/Validate/Exception.php';
                throw new Zend_Validate_Exception('No database adapter present');
            }
        }

        /**
         * Build select object
         */
        $select = new Zend_Db_Select($this->_adapter);
        $select->from($this->_table, array($this->_field), $this->_schema)
               ->where($this->_adapter->quoteIdentifier($this->_field).' = ?', $value);
        if ($this->_exclude !== null) {
            if (is_array($this->_exclude)) {
                $select->where($this->_adapter->quoteIdentifier($this->_exclude['field']).' != ?', $this->_exclude['value']);
            } else {
                $select->where($this->_exclude);
            }
        }
        $select->limit(1);

        /**
         * Run query
         */
        $result = $this->_adapter->fetchRow($select, array(), Zend_Db::FETCH_ASSOC);

        return $result;
    }
}
