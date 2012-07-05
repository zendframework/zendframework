<?php
namespace ZendTest\Db\Adapter\Platform;

use Zend\Db\Adapter\Platform\Mysql;

class MysqlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Mysql
     */
    protected $platform;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->platform = new Mysql;
    }

    /**
     * @covers Zend\Db\Adapter\Platform\Mysql::getName
     */
    public function testGetName()
    {
        $this->assertEquals('MySQL', $this->platform->getName());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\Mysql::getQuoteIdentifierSymbol
     */
    public function testGetQuoteIdentifierSymbol()
    {
        $this->assertEquals('`', $this->platform->getQuoteIdentifierSymbol());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\Mysql::quoteIdentifier
     */
    public function testQuoteIdentifier()
    {
        $this->assertEquals('`identifier`', $this->platform->quoteIdentifier('identifier'));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\Mysql::quoteIdentifierChain
     */
    public function testQuoteIdentifierChain()
    {
        $this->assertEquals('`identifier`', $this->platform->quoteIdentifierChain('identifier'));
        $this->assertEquals('`identifier`', $this->platform->quoteIdentifierChain(array('identifier')));
        $this->assertEquals('`schema`.`identifier`', $this->platform->quoteIdentifierChain(array('schema','identifier')));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\Mysql::getQuoteValueSymbol
     */
    public function testGetQuoteValueSymbol()
    {
        $this->assertEquals("'", $this->platform->getQuoteValueSymbol());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\Mysql::quoteValue
     */
    public function testQuoteValue()
    {
        $this->assertEquals("'value'", $this->platform->quoteValue('value'));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\Mysql::quoteValueList
     */
    public function testQuoteValueList()
    {
        $this->assertEquals("'Foo O\\'Bar'", $this->platform->quoteValueList("Foo O'Bar"));
        $this->assertEquals("'Foo O\\'Bar'", $this->platform->quoteValueList(array("Foo O'Bar")));
        $this->assertEquals("'value', 'Foo O\\'Bar'", $this->platform->quoteValueList(array('value',"Foo O'Bar")));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\Mysql::getIdentifierSeparator
     */
    public function testGetIdentifierSeparator()
    {
        $this->assertEquals('.', $this->platform->getIdentifierSeparator());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\Mysql::quoteIdentifierInFragment
     */
    public function testQuoteIdentifierInFragment()
    {
        $this->assertEquals('`foo`.`bar`', $this->platform->quoteIdentifierInFragment('foo.bar'));
        $this->assertEquals('`foo` as `bar`', $this->platform->quoteIdentifierInFragment('foo as bar'));
    }
}
