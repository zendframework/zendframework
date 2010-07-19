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
namespace ZendTest\Db\Adapter;
use Zend\Db\Adapter;


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractPdoTest extends AbstractTest
{

    public function testAdapterAlternateStatement()
    {
        $this->_testAdapterAlternateStatement('\ZendTest\Db\Adapter\TestAsset\PdoStatement');
    }

    /**
     * Ensures that exec() throws an exception when given a bogus query
     * @group ZF-6185
     * @return void
     */
    public function testAdapterExecBogus()
    {
        $this->setExpectedException('Zend\Db\Adapter\Exception');
        $this->_db->exec('Bogus query');
        $this->fail('Expected exception not thrown');
    }

    /**
     * Ensures that exec() throws an exception when given a bogus table
     * @group ZF-6185
     * @return void
     */
    public function testAdapterExecBogusTable()
    {
        $this->setExpectedException('Zend\Db\Adapter\Exception');
        $this->_db->exec('DELETE FROM BogusTable');
        $this->fail('Expected exception not thrown');
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
