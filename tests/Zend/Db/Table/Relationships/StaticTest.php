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
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * PHPUnit_Util_Filter
 */
require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @see Zend_Db_Table_Row_TestMockRow
 */
require_once dirname(__FILE__) . '/../_files/My/ZendDbTable/Row/TestMockRow.php';

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Table
 * @group      Zend_Db_Table_Relationships
 */
class Zend_Db_Table_Relationships_StaticTest extends PHPUnit_Framework_TestCase
{

    public function testTableRelationshipsFindDependentMagic()
    {
        $row = new My_ZendDbTable_Row_TestMockRow();

        $this->assertNull($row->dependentTable);
        $this->assertNull($row->ruleKey);

        $row->findTable1();
        $this->assertEquals('Table1', $row->dependentTable);
        $this->assertNull($row->ruleKey);

        $row->findTable2ByRule1();
        $this->assertEquals('Table2', $row->dependentTable);
        $this->assertEquals('Rule1', $row->ruleKey);
    }

    public function testTableRelationshipsFindParentMagic()
    {
        $row = new My_ZendDbTable_Row_TestMockRow();

        $this->assertNull($row->parentTable);
        $this->assertNull($row->ruleKey);

        $row->findParentTable1();
        $this->assertEquals('Table1', $row->parentTable);
        $this->assertNull($row->ruleKey);

        $row->findParentTable2ByRule1();
        $this->assertEquals('Table2', $row->parentTable);
        $this->assertEquals('Rule1', $row->ruleKey);
    }

    public function testTableRelationshipsFindManyToManyMagic()
    {
        $row = new My_ZendDbTable_Row_TestMockRow();

        $this->assertNull($row->matchTable);
        $this->assertNull($row->intersectionTable);
        $this->assertNull($row->callerRefRuleKey);
        $this->assertNull($row->matchRefRuleKey);

        $row->findTable1ViaTable2();
        $this->assertEquals('Table1', $row->matchTable);
        $this->assertEquals('Table2', $row->intersectionTable);
        $this->assertNull($row->callerRefRuleKey);
        $this->assertNull($row->matchRefRuleKey);

        $row->findTable3ViaTable4ByRule1();
        $this->assertEquals('Table3', $row->matchTable);
        $this->assertEquals('Table4', $row->intersectionTable);
        $this->assertEquals('Rule1', $row->callerRefRuleKey);
        $this->assertNull($row->matchRefRuleKey);

        $row->findTable5ViaTable6ByRule2AndRule3();
        $this->assertEquals('Table5', $row->matchTable);
        $this->assertEquals('Table6', $row->intersectionTable);
        $this->assertEquals('Rule2', $row->callerRefRuleKey);
        $this->assertEquals('Rule3', $row->matchRefRuleKey);
    }

    public function getDriver()
    {
        return 'Static';
    }

}
