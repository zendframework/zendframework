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
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__)."/../../../../../TestHelper.php";

require_once "AbstractTestCase.php";

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Test_PHPUnit_Db_Integration_SqLiteIntegrationTest extends Zend_Test_PHPUnit_Db_Integration_AbstractTestCase
{
    public function setUp()
    {
        if (!extension_loaded('pdo')) {
            $this->markTestSkipped('PDO is required for this test.');
        }

        if(!in_array('sqlite', PDO::getAvailableDrivers())) {
            $this->markTestSkipped('SqLite is not included in PDO in this PHP installation.');
        }

        $this->dbAdapter = Zend_Db::factory('pdo_sqlite', array('dbname' => ':memory:'));
        $this->dbAdapter->query(
            'CREATE TABLE "foo" (id INTEGER PRIMARY KEY AUTOINCREMENT, foo VARCHAR, bar VARCHAR, baz VARCHAR)'
        );
        $this->dbAdapter->query(
            'CREATE TABLE "bar" (id INTEGER PRIMARY KEY AUTOINCREMENT, foo VARCHAR, bar VARCHAR, baz VARCHAR)'
        );
    }
}
