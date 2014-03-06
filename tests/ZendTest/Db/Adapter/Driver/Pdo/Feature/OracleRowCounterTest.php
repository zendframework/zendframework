<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Adapter\Driver\Pdo\Feature;

use PHPUnit_Framework_TestCase;
use Zend\Db\Adapter\Driver\Pdo\Feature\OracleRowCounter;
use Zend\Db\Adapter\Driver\Pdo\Statement;

class OracleRowCounterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var OracleRowCounter
     */
    protected $rowcounter;

    public function setUp()
    {
        $this->rowcounter = new OracleRowCounter();
    }

    /**
     * @covers OracleRowCounter::getName
     */
    public function testGetName()
    {
        $this->assertEquals('OracleRowCounter', $this->rowcounter->getName());
    }

    /**
     * @covers OracleRowCounter::getCountForStatement
     */
    public function testGetCountForStatement()
    {
        $statement = new Statement;
        $statement->setDriver($this->getMock('Zend\Db\Adapter\Driver\Pdo\Pdo', array('prepare'), array(), '', false));

        $this->rowcounter->getCountForStatement($statement);
    }

    /**
     * @covers OracleRowCounter::getCountForSql
     */
    public function testGetCountForSql()
    {
        $this->markTestIncomplete('Need to Count Row');
        $this->rowcounter->getCountForSql("select * from foo");
    }

    /**
     * @covers OracleRowCounter::getRowCountClosure
     */
    public function testGetRowCountClosure()
    {
        $this->markTestIncomplete('Need to Count Row on $context ');

        $this->rowcounter->getRowCountClosure("select * from foo");
        $statement = new Statement;
        $statement->setDriver($this->getMock('Zend\Db\Adapter\Driver\Pdo\Pdo', array('prepare'), array(), '', false));
        $this->rowcounter->getRowCountClosure($statement);
    }
}
