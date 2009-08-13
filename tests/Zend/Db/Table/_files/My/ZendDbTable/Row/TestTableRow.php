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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: TestTableRow.php 12004 2008-10-18 14:29:41Z mikaelkael $
 */


/**
 * @see Zend_Db_Table_Row_Abstract
 */
require_once 'Zend/Db/Table/Row/Abstract.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class My_ZendDbTable_Row_TestTableRow extends Zend_Db_Table_Row_Abstract
{
    protected $_tableClass = 'My_ZendDbTable_TableBugs';

    public function setInvalidColumn()
    {
        $this->_transformColumn(array('bug_id'));
    }

    public function setTableToFail()
    {
        $this->_tableClass = 'foo';
    }

    public function setTableColsToFail()
    {
        $this->_data = array();
    }

    public function setPrimaryKeyToFail1()
    {
        $this->_primary = 'foo';
    }

    public function setPrimaryKeyToFail2()
    {
        $this->_primary = array();
    }

    protected function _postUpdate()
    {
        $this->bug_id = 0;
    }
}
