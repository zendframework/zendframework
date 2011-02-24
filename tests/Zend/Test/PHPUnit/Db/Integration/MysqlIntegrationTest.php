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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Test\PHPUnit\Db\Integration;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class MysqlIntegrationTest extends AbstractTestCase
{
    public function setUp()
    {
        if (!TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED) {
            $this->markTestSkipped('Database tests are not enabled.');
            return;
        }

        if (!extension_loaded('pdo')) {
            $this->markTestSkipped('PDO is required for this test.');
            return;
        }

        if (!in_array('mysql', \PDO::getAvailableDrivers())) {
            $this->markTestSkipped('Mysql is not included in PDO in this PHP installation.');
            return;
        }

        $params = array(
            'host'     => TESTS_ZEND_DB_ADAPTER_MYSQL_HOSTNAME,
            'username' => TESTS_ZEND_DB_ADAPTER_MYSQL_USERNAME,
            'password' => TESTS_ZEND_DB_ADAPTER_MYSQL_PASSWORD,
            'dbname'   => TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE,
        );

        $this->dbAdapter = \Zend\Db\Db::factory('PdoMysql', $params);
        $this->dbAdapter->query("DROP TABLE IF EXISTS foo");
        $this->dbAdapter->query("DROP TABLE IF EXISTS bar");
        $this->dbAdapter->query(
            'CREATE TABLE foo (id INT(10) AUTO_INCREMENT PRIMARY KEY, foo VARCHAR(255), bar VARCHAR(255), baz VARCHAR(255)) AUTO_INCREMENT=1'
        );
        $this->dbAdapter->query(
            'CREATE TABLE bar (id INT(10) AUTO_INCREMENT PRIMARY KEY, foo VARCHAR(255), bar VARCHAR(255), baz VARCHAR(255)) AUTO_INCREMENT=1'
        );
    }
}
