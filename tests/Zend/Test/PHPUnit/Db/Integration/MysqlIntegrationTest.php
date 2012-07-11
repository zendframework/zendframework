<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace ZendTest\Test\PHPUnit\Db\Integration;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
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
