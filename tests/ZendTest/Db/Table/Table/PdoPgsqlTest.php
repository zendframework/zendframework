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
 * @namespace
 */
namespace ZendTest\Db\Table\Table;



/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Table
 */
class PdoPgsqlTest extends AbstractTest
{
    public function setup()
    {
        $this->markTestSkipped('This suite is skipped until Zend\DB can be refactored.');
    }
    
    public function getDriver()
    {
        return 'Pdo\Pgsql';
    }

    public function testTableInsert()
    {
        $table = $this->_table['bugs'];
        $row = array (
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy'
        );
        $insertResult = $table->insert($row);
        $lastInsertId = $this->_db->lastInsertId('zfbugs', 'bug_id');
        $lastSequenceId = $this->_db->lastSequenceId('zfbugs_bug_id_seq');
        $this->assertEquals($insertResult, $lastInsertId);
        $this->assertEquals($insertResult, $lastSequenceId);
        $this->assertEquals(5, $lastInsertId);
    }

    public function testTableInsertPkNull()
    {
        $table = $this->_table['bugs'];
        $row = array (
            'bug_id'          => null,
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy'
        );
        $insertResult = $table->insert($row);
        $lastInsertId = $this->_db->lastInsertId('zfbugs', 'bug_id');
        $lastSequenceId = $this->_db->lastSequenceId('zfbugs_bug_id_seq');
        $this->assertEquals($insertResult, $lastInsertId);
        $this->assertEquals($insertResult, $lastSequenceId);
        $this->assertEquals(5, $lastInsertId);
    }

    public function testTableInsertSequence()
    {
        $table = $this->_getTable('\ZendTest\Db\Table\TestAsset\TableProducts',
            array(\Zend\Db\Table\AbstractTable::SEQUENCE => 'zfproducts_seq'));
        $row = array (
            'product_name' => 'Solaris'
        );
        $insertResult         = $table->insert($row);
        $lastInsertId         = $this->_db->lastInsertId('zfproducts');
        $lastSequenceId       = $this->_db->lastSequenceId('zfproducts_seq');
        $this->assertEquals($insertResult, $lastInsertId);
        $this->assertEquals($insertResult, $lastSequenceId);
        $this->assertEquals(4, $insertResult);
    }

    /**
     * Ensures that the schema is null if not specified
     *
     * @return void
     */
    public function testTableSchemaNotSetIsNull()
    {
        $tableInfo = $this->_table['bugs']->info();

        $this->assertNull($tableInfo['schema']);
    }

    /**
     * Ensures that the schema is set by the 'schema' constructor configuration directive
     *
     * @return void
     */
    public function testTableSchemaSetByConstructorConfigSchema()
    {
        $schema = 'public';

        $config = array(
            'db'        => $this->_db,
            'schema'    => $schema
            );

        $table = new \ZendTest\Db\Table\TestAsset\TableBugs($config);

        $tableInfo = $table->info();

        $this->assertEquals($schema, $tableInfo['schema']);
    }

    /**
     * Ensures that the schema is set by the 'name' constructor configuration directive
     *
     * @return void
     */
    public function testTableSchemaSetByConstructorConfigName()
    {
        $schema = 'public';

        $tableName = "$schema.zfbugs";

        $config = array(
            'db'        => $this->_db,
            'name'      => $tableName
            );

        $table = new \ZendTest\Db\Table\TestAsset\TableBugs($config);

        $tableInfo = $table->info();

        $this->assertEquals($schema, $tableInfo['schema']);
    }

    /**
     * Ensures that a schema given in the 'name' constructor configuration directive overrides any schema specified
     * by the 'schema' constructor configuration directive.
     *
     * @return void
     */
    public function testTableSchemaConstructorConfigNameOverridesSchema()
    {
        $schema = 'public';

        $tableName = "$schema.zfbugs";

        $config = array(
            'db'        => $this->_db,
            'schema'    => 'foo',
            'name'      => $tableName
            );

        $table = new \ZendTest\Db\Table\TestAsset\TableBugs($config);

        $tableInfo = $table->info();

        $this->assertEquals($schema, $tableInfo['schema']);
    }

    /**
     * Ensures that fetchAll() provides expected behavior when the schema is specified
     *
     * @return void
     */
    public function testTableFetchAllSchemaSet()
    {
        $schema = 'public';

        $config = array(
            'db'        => $this->_db,
            'schema'    => $schema,
            );

        $table = new \ZendTest\Db\Table\TestAsset\TableBugs($config);

        $rowset = $table->fetchAll();

        $this->assertThat(
            $rowset,
            $this->isInstanceOf('Zend_Db_Table_Rowset')
            );

        $this->assertEquals(
            4,
            count($rowset)
            );
    }
}
