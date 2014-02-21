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
use Zend\Db\Adapter\Adapter;
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

    public function testGetName()
    {
        $this->assertEquals('OracleRowCounter', $this->rowcounter->getName());
    }

    public function testGetCountForStatement()
    {
        $statement = new Statement;
        $statement->setDriver($this->getMock('Zend\Db\Adapter\Driver\Pdo\Pdo', array('prepare'), array(), '', false));

        $this->rowcounter->getCountForStatement($statement);
    }
}
