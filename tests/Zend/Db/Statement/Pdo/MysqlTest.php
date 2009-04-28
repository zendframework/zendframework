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
 */

require_once 'Zend/Db/Statement/Pdo/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Statement_Pdo_MysqlTest extends Zend_Db_Statement_Pdo_TestCommon
{

    public function testStatementNextRowset()
    {
        $select = $this->_db->select()
            ->from('zfproducts');
        $stmt = $this->_db->prepare($select->__toString());
        try {
            $stmt->nextRowset();
        } catch (Zend_Db_Statement_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->assertEquals('SQLSTATE[HYC00]: Optional feature not implemented', $e->getMessage());
        }
        $stmt->closeCursor();
    }

    /**
     * Ensures that the character sequence ":0'" is handled properly
     *
     * @link   http://framework.zend.com/issues/browse/ZF-2059
     * @return void
     */
    public function testZF2059()
    {
        $sql = "SELECT bug_id FROM zfbugs WHERE bug_status != ':0'";
        $results = $this->_db->fetchAll($sql);
        $this->assertEquals(4, count($results));

        $select = $this->_db->select()->from('zfbugs', 'bug_id')
                                      ->where('bug_status != ?', ':0');
        $results = $this->_db->fetchAll($select);
        $this->assertEquals(4, count($results));
    }

    public function getDriver()
    {
        return 'Pdo_Mysql';
    }

}
