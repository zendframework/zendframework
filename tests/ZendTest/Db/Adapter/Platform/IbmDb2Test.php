<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace ZendTest\Db\Adapter\Platform;

use Zend\Db\Adapter\Platform\IbmDb2;

class IbmDb2Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IbmDb2
     */
    protected $platform;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->platform = new IbmDb2;
    }

    /**
     * @covers Zend\Db\Adapter\Platform\IbmDb2::getName
     */
    public function testGetName()
    {
        $this->assertEquals('IBM DB2', $this->platform->getName());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\IbmDb2::getQuoteIdentifierSymbol
     */
    public function testGetQuoteIdentifierSymbol()
    {
        $this->assertEquals('"', $this->platform->getQuoteIdentifierSymbol());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\IbmDb2::quoteIdentifier
     */
    public function testQuoteIdentifier()
    {
        $this->assertEquals('"identifier"', $this->platform->quoteIdentifier('identifier'));

        $platform = new IbmDb2(array('quote_identifiers' => false));
        $this->assertEquals('identifier', $platform->quoteIdentifier('identifier'));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\IbmDb2::quoteIdentifierChain
     */
    public function testQuoteIdentifierChain()
    {
        $this->assertEquals('"identifier"', $this->platform->quoteIdentifierChain('identifier'));
        $this->assertEquals('"identifier"', $this->platform->quoteIdentifierChain(array('identifier')));
        $this->assertEquals('"schema"."identifier"', $this->platform->quoteIdentifierChain(array('schema','identifier')));

        $platform = new IbmDb2(array('quote_identifiers' => false));
        $this->assertEquals('identifier', $platform->quoteIdentifierChain('identifier'));
        $this->assertEquals('identifier', $platform->quoteIdentifierChain(array('identifier')));
        $this->assertEquals('schema.identifier', $platform->quoteIdentifierChain(array('schema','identifier')));

        $platform = new IbmDb2(array('identifier_separator' => '\\'));
        $this->assertEquals('"schema"\"identifier"', $platform->quoteIdentifierChain(array('schema','identifier')));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\IbmDb2::getQuoteValueSymbol
     */
    public function testGetQuoteValueSymbol()
    {
        $this->assertEquals("'", $this->platform->getQuoteValueSymbol());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\IbmDb2::quoteValue
     */
    public function testQuoteValue()
    {
        $this->assertEquals("'value'", $this->platform->quoteValue('value'));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\IbmDb2::quoteValueList
     */
    public function testQuoteValueList()
    {
        $this->assertEquals("'Foo O\\'Bar'", $this->platform->quoteValueList("Foo O'Bar"));
        $this->assertEquals("'Foo O\\'Bar'", $this->platform->quoteValueList(array("Foo O'Bar")));
        $this->assertEquals("'value', 'Foo O\\'Bar'", $this->platform->quoteValueList(array('value',"Foo O'Bar")));
    }

    /**
     * @covers Zend\Db\Adapter\Platform\IbmDb2::getIdentifierSeparator
     */
    public function testGetIdentifierSeparator()
    {
        $this->assertEquals('.', $this->platform->getIdentifierSeparator());

        $platform = new IbmDb2(array('identifier_separator' => '\\'));
        $this->assertEquals('\\', $platform->getIdentifierSeparator());
    }

    /**
     * @covers Zend\Db\Adapter\Platform\IbmDb2::quoteIdentifierInFragment
     */
    public function testQuoteIdentifierInFragment()
    {
        $this->assertEquals('"foo"."bar"', $this->platform->quoteIdentifierInFragment('foo.bar'));
        $this->assertEquals('"foo" as "bar"', $this->platform->quoteIdentifierInFragment('foo as bar'));

        $platform = new IbmDb2(array('quote_identifiers' => false));
        $this->assertEquals('foo.bar', $platform->quoteIdentifierInFragment('foo.bar'));
        $this->assertEquals('foo as bar', $platform->quoteIdentifierInFragment('foo as bar'));
    }

    /**
     * @group ZF2-386
     * @covers Zend\Db\Adapter\Platform\IbmDb2::quoteIdentifierInFragment
     */
    public function testQuoteIdentifierInFragmentIgnoresSingleCharSafeWords()
    {
        $this->assertEquals('("foo"."bar" = "boo"."baz")', $this->platform->quoteIdentifierInFragment('(foo.bar = boo.baz)', array('(', ')', '=')));
    }

}
