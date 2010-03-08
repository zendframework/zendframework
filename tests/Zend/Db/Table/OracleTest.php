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
 * @version    $Id $
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
class Zend_Db_Table_OracleTest extends Zend_Db_Table_TestCommon
{

    public function testTableInsert()
    {
        $this->markTestSkipped($this->getDriver().' does not support auto-increment columns.');
    }

    public function testIsIdentity()
    {
        $this->markTestSkipped($this->getDriver().' does not support auto-increment columns.');
    }

    /**
     * ZF-4330: Oracle needs sequence
     */
    public function testTableInsertWithSchema()
    {
        $schemaName = $this->_util->getSchema();
        $tableName = 'zfbugs';
        $identifier = join('.', array_filter(array($schemaName, $tableName)));
        $table = $this->_getTable('My_ZendDbTable_TableSpecial',
            array('name' => $tableName, 'schema' => $schemaName, Zend_Db_Table_Abstract::SEQUENCE => 'zfbugs_seq')
        );

        $row = array (
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy',
            'verified_by'     => 'dduck'
        );

        $profilerEnabled = $this->_db->getProfiler()->getEnabled();
        $this->_db->getProfiler()->setEnabled(true);
        $insertResult = $table->insert($row);
        $this->_db->getProfiler()->setEnabled($profilerEnabled);

        $qp = $this->_db->getProfiler()->getLastQueryProfile();
        $tableSpec = $this->_db->quoteIdentifier($identifier, true);
        $this->assertContains("INSERT INTO $tableSpec ", $qp->getQuery());
    }

    public function testTableInsertSequence()
    {
        $table = $this->_getTable('My_ZendDbTable_TableBugs',
            array(Zend_Db_Table_Abstract::SEQUENCE => 'zfbugs_seq'));
        $row = array (
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => new Zend_Db_Expr(
                $this->_db->quoteInto('DATE ?', '2007-04-02')),
            'updated_on'      => new Zend_Db_Expr(
                $this->_db->quoteInto('DATE ?', '2007-04-02')),
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy'
        );
        $insertResult         = $table->insert($row);
        $lastInsertId         = $this->_db->lastInsertId('zfbugs');
        $lastSequenceId       = $this->_db->lastSequenceId('zfbugs_seq');
        $this->assertEquals($insertResult, $lastInsertId);
        $this->assertEquals($insertResult, $lastSequenceId);
        $this->assertEquals(5, $insertResult);
    }

    protected function _getRowForTableAndIdentityWithVeryLongName()
    {
        return array('thisisalongtablenameidentity' => 1, 'stuff' => 'information');
    }

    public function getDriver()
    {
        return 'Oracle';
    }

}
