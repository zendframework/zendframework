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
 * @see Zend_Db_Adapter_TestCommon
 */


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Adapter_Pdo_TestCommon extends Zend_Db_Adapter_TestCommon
{

    public function testAdapterAlternateStatement()
    {
        $this->_testAdapterAlternateStatement('Test_PdoStatement');
    }

    /**
     * Ensures that exec() throws an exception when given a bogus query
     * @group ZF-6185
     * @return void
     */
    public function testAdapterExecBogus()
    {
        try {
            $this->_db->exec('Bogus query');
            $this->fail('Expected exception not thrown');
        } catch (Zend_Db_Adapter_Exception $e) {
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expecting object of type Zend_Db_Adapter_Exception, got ' . get_class($e));
        }
    }

    /**
     * Ensures that exec() throws an exception when given a bogus table
     * @group ZF-6185
     * @return void
     */
    public function testAdapterExecBogusTable()
    {
        try {
            $this->_db->exec('DELETE FROM BogusTable');
            $this->fail('Expected exception not thrown');
        } catch (Zend_Db_Adapter_Exception $e) {
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expecting object of type Zend_Db_Adapter_Exception, got ' . get_class($e));
        }
    }

    /**
     * Ensures that exec() provides expected behavior when modifying no rows
     * @group ZF-6185
     * @return void
     */
    public function testAdapterExecModifiedNone()
    {
        $affected = $this->_db->exec('DELETE FROM ' . $this->_db->quoteIdentifier('zfbugs') . ' WHERE 1 = -1');

        $this->assertEquals(0, $affected,
            "Expected exec() to return zero affected rows; got $affected");
    }
}
