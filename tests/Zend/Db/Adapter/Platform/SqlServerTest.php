<?php
namespace ZendTest\Db\Adapter\Platform;

use Zend\Db\Adapter\Platform\SqlServer;

class SqlServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SqlServer
     */
    protected $platform;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->platform = new SqlServer;
    }

    /**
     * @covers Zend\Db\Adapter\Platform\SqlServer::getName
     */
    public function testGetName()
    {
        $this->assertEquals('SQLServer', $this->platform->getName());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\SqlServer::getQuoteIdentifierSymbol
     */
    public function testGetQuoteIdentifierSymbol()
    {
        $this->assertEquals(array('[', ']'), $this->platform->getQuoteIdentifierSymbol());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\SqlServer::quoteIdentifier
     */
    public function testQuoteIdentifier()
    {
        $this->assertEquals('[identifier]', $this->platform->quoteIdentifier('identifier'));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\SqlServer::quoteIdentifierChain
     */
    public function testQuoteIdentifierChain()
    {
        $this->assertEquals('[identifier]', $this->platform->quoteIdentifierChain('identifier'));
        $this->assertEquals('[identifier]', $this->platform->quoteIdentifierChain(array('identifier')));
        $this->assertEquals('[schema].[identifier]', $this->platform->quoteIdentifierChain(array('schema','identifier')));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\SqlServer::getQuoteValueSymbol
     */
    public function testGetQuoteValueSymbol()
    {
        $this->assertEquals("'", $this->platform->getQuoteValueSymbol());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\SqlServer::quoteValue
     */
    public function testQuoteValue()
    {
        $this->assertEquals("'value'", $this->platform->quoteValue('value'));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\SqlServer::quoteValueList
     */
    public function testQuoteValueList()
    {
        $this->assertEquals("'Foo O''Bar'", $this->platform->quoteValueList("Foo O'Bar"));
        $this->assertEquals("'Foo O''Bar'", $this->platform->quoteValueList(array("Foo O'Bar")));
        $this->assertEquals("'value', 'Foo O''Bar'", $this->platform->quoteValueList(array('value',"Foo O'Bar")));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\SqlServer::getIdentifierSeparator
     */
    public function testGetIdentifierSeparator()
    {
        $this->assertEquals('.', $this->platform->getIdentifierSeparator());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\SqlServer::quoteIdentifierInFragment
     */
    public function testQuoteIdentifierInFragment()
    {
        $this->assertEquals('[foo].[bar]', $this->platform->quoteIdentifierInFragment('foo.bar'));
        $this->assertEquals('[foo] as [bar]', $this->platform->quoteIdentifierInFragment('foo as bar'));
    }
}
