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
class SqLiteIntegrationTest extends AbstractTestCase
{
    public function setUp()
    {
        if (!extension_loaded('pdo')) {
            $this->markTestSkipped('PDO is required for this test.');
        }

        if(!in_array('sqlite', \PDO::getAvailableDrivers())) {
            $this->markTestSkipped('SqLite is not included in PDO in this PHP installation.');
        }

        $this->dbAdapter = \Zend\Db\Db::factory('Pdo\Sqlite', array('dbname' => ':memory:'));
        $this->dbAdapter->query(
            'CREATE TABLE "foo" (id INTEGER PRIMARY KEY AUTOINCREMENT, foo VARCHAR, bar VARCHAR, baz VARCHAR)'
        );
        $this->dbAdapter->query(
            'CREATE TABLE "bar" (id INTEGER PRIMARY KEY AUTOINCREMENT, foo VARCHAR, bar VARCHAR, baz VARCHAR)'
        );
    }
}
