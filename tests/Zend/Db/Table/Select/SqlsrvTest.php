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
namespace ZendTest\Db\Table\Select;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Table
 * @group      Zend_Db_Table_Select
 */
class SqlsrvTest extends AbstractTest
{
    public function setup()
    {
        $this->markTestSkipped('This suite is skipped until Zend\DB can be refactored.');
    }
    
    public function testSelectQueryWithBinds()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support binding by name.');
    }

    public function testSelectColumnWithColonQuotedParameter()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support selecting int columns by varchar param.');
    }

    public function testSelectFromForUpdate()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support for update.');
    }

    public function testSelectFromQualified()
    {
        $this->markTestIncomplete($this->getDriver() . ' needs more syntax for qualified table names.');
    }

    public function testSelectJoinQualified()
    {
        $this->markTestIncomplete($this->getDriver() . ' needs more syntax for qualified table names.');
    }

    public function getDriver()
    {
        return 'Sqlsrv';
    }
}
