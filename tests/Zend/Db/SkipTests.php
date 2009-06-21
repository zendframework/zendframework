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
 * @version    $Id$
 */

require_once 'PHPUnit/Framework/TestCase.php';

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Skip_CommonTest extends PHPUnit_Framework_TestCase
{
    public $message = null;

    abstract public function getDriver();

    public function setUp()
    {
        $driver = $this->getDriver();
        $message = 'Skipping ' . $this->getDriver();
        if ($this->message) {
            $message .= ': ' . $this->message;
        }
        $this->markTestSkipped($message);
    }

    public function testDb()
    {
        // this is here only so we have at least one test
    }
}

class Zend_Db_Skip_StaticTest extends Zend_Db_Skip_CommonTest
{
    public function getDriver()
    {
        return 'Static';
    }
}

class Zend_Db_Skip_Db2Test extends Zend_Db_Skip_CommonTest
{
    public function getDriver()
    {
        return 'Db2';
    }
}

class Zend_Db_Skip_MysqliTest extends Zend_Db_Skip_CommonTest
{
    public function getDriver()
    {
        return 'Mysqli';
    }
}

class Zend_Db_Skip_OdbcTest extends Zend_Db_Skip_CommonTest
{
    public function getDriver()
    {
        return 'Odbc';
    }
}

class Zend_Db_Skip_OracleTest extends Zend_Db_Skip_CommonTest
{
    public function getDriver()
    {
        return 'Oracle';
    }
}

class Zend_Db_Skip_FirebirdTest extends Zend_Db_Skip_CommonTest
{
    public function getDriver()
    {
        return 'Firebird';
    }
}

class Zend_Db_Skip_Pdo_IbmTest extends Zend_Db_Skip_CommonTest
{
    function getDriver()
    {
        return 'Pdo_Ibm';
    }
}

class Zend_Db_Skip_Pdo_MssqlTest extends Zend_Db_Skip_CommonTest
{
    function getDriver()
    {
        return 'Pdo_Mssql';
    }
}

class Zend_Db_Skip_Pdo_MysqlTest extends Zend_Db_Skip_CommonTest
{
    public function getDriver()
    {
        return 'Pdo_Mysql';
    }
}

class Zend_Db_Skip_Pdo_OciTest extends Zend_Db_Skip_CommonTest
{
    public function getDriver()
    {
        return 'Pdo_Oci';
    }
}

class Zend_Db_Skip_Pdo_FirebirdTest extends Zend_Db_Skip_CommonTest
{
    public function getDriver()
    {
        return 'Pdo_Firebird';
    }
}

class Zend_Db_Skip_Pdo_PgsqlTest extends Zend_Db_Skip_CommonTest
{
    public function getDriver()
    {
        return 'Pdo_Pgsql';
    }
}

class Zend_Db_Skip_Pdo_SqliteTest extends Zend_Db_Skip_CommonTest
{
    public function getDriver()
    {
        return 'Pdo_Sqlite';
    }
}
