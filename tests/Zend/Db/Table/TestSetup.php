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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Db_TestSetup
 */
require_once 'Zend/Db/TestSetup.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table_TestSetup extends Zend_Db_TestSetup
{

    /**
     * @var array of Zend_Db_Table_Abstract
     */
    protected $_table = array();

    public function setUp()
    {
        parent::setUp();

        $this->_table['accounts']      = $this->_getTable('Zend_Db_Table_TableAccounts');
        $this->_table['bugs']          = $this->_getTable('Zend_Db_Table_TableBugs');
        $this->_table['bugs_products'] = $this->_getTable('Zend_Db_Table_TableBugsProducts');
        $this->_table['products']      = $this->_getTable('Zend_Db_Table_TableProducts');
    }

    protected function _getTable($tableClass, $options = array())
    {
        if (is_array($options) && !isset($options['db'])) {
            $options['db'] = $this->_db;
        }
        Zend_Loader::loadClass($tableClass);
        $table = new $tableClass($options);
        return $table;
    }

}
