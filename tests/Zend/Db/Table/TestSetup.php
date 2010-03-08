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
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Db_TestSetup
 */


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Table
 */
abstract class Zend_Db_Table_TestSetup extends Zend_Db_TestSetup
{

    /**
     * @var array of Zend_Db_Table_Abstract
     */
    protected $_table = array();

    protected $_runtimeIncludePath = null;

    public function setUp()
    {
        parent::setUp();

        $this->_table['accounts']      = $this->_getTable('My_ZendDbTable_TableAccounts');
        $this->_table['bugs']          = $this->_getTable('My_ZendDbTable_TableBugs');
        $this->_table['bugs_products'] = $this->_getTable('My_ZendDbTable_TableBugsProducts');
        $this->_table['products']      = $this->_getTable('My_ZendDbTable_TableProducts');
    }

    public function tearDown()
    {
        if ($this->_runtimeIncludePath) {
            $this->_restoreIncludePath();
        }
        parent::tearDown();
    }

    protected function _getTable($tableClass, $options = array())
    {
        if (is_array($options) && !isset($options['db'])) {
            $options['db'] = $this->_db;
        }
        if (!class_exists($tableClass)) {
            $this->_useMyIncludePath();
            Zend_Loader::loadClass($tableClass);
            $this->_restoreIncludePath();
        }
        $table = new $tableClass($options);
        return $table;
    }

    protected function _useMyIncludePath()
    {
        $this->_runtimeIncludePath = get_include_path();
        set_include_path(dirname(__FILE__) . '/_files/' . PATH_SEPARATOR . $this->_runtimeIncludePath);
    }

    protected function _restoreIncludePath()
    {
        set_include_path($this->_runtimeIncludePath);
        $this->_runtimeIncludePath = null;
    }

}
