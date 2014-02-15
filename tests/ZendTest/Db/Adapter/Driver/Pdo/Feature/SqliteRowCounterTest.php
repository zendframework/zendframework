<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Adapter\Driver\Pdo;

use PHPUnit_Framework_TestCase;
use Zend\Db\Adapter\Driver\Pdo\Feature\SqliteRowCounter;
use Zend\Db\Adapter\Adapter;

class SqliteRowCounterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SqliteRowCounter
     */
    protected $rowcounter;

    public function setUp()
    {
        $this->rowcounter = new SqliteRowCounter();
    }

    public function testGetName()
    {
        $this->assertEquals('SqliteRowCounter', $this->rowcounter->getName());
    }
}
