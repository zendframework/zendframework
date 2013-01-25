<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Text
 */

namespace ZendTest\Text;

use Zend\Text\Table;
use Zend\Text\Table\Decorator;

/**
 * @category   Zend
 * @package    Zend_Text
 * @subpackage UnitTests
 * @group      Zend_Text
 */
class TableTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Table\Table::setInputCharset('utf-8');
        Table\Table::setOutputCharset('utf-8');
    }

    public function testColumnAlignLeft()
    {
        $column = new Table\Column("foobar\nfoo");

        $this->assertEquals($column->render(10), "foobar    \nfoo       ");
    }

    public function testColumnPadding()
    {
        $column = new Table\Column("foobar\nfoo");

        $this->assertEquals($column->render(10, 1), " foobar   \n foo      ");
    }

    public function testColumnWordwrap()
    {
        $column = new Table\Column("foobar");

        $this->assertEquals($column->render(3), "foo\nbar");
    }

    public function testColumnUnicodeWordwrap()
    {
        $column = new Table\Column("Ömläüt");

        $this->assertEquals($column->render(3), "Öml\näüt");
    }

    public function testColumnAlignCenter()
    {
        $column = new Table\Column("foobar\nfoo", Table\Column::ALIGN_CENTER);

        $this->assertEquals($column->render(10), "  foobar  \n   foo    ");
    }

    public function testColumnAlignRight()
    {
        $column = new Table\Column("foobar\nfoo", Table\Column::ALIGN_RIGHT);

        $this->assertEquals($column->render(10), "    foobar\n       foo");
    }

    public function testColumnForcedEncoding()
    {
        if (PHP_OS == 'AIX') {
            // AIX cannot handle these charsets
            $this->markTestSkipped('Test case cannot run on AIX');
        }

        $iso885915 = iconv('utf-8', 'iso-8859-15', 'Ömläüt');

        $column = new Table\Column($iso885915, null, null, 'iso-8859-15');

        $this->assertEquals($column->render(6), 'Ömläüt');
    }

    public function testColumnDefaultInputEncoding()
    {
        if (PHP_OS == 'AIX') {
            // AIX cannot handle these charsets
            $this->markTestSkipped('Test case cannot run on AIX');
        }

        $iso885915 = iconv('utf-8', 'iso-8859-15', 'Ömläüt');

        Table\Table::setInputCharset('iso-8859-15');
        $column = new Table\Column($iso885915);

        $this->assertEquals($column->render(6), 'Ömläüt');
    }

    public function testColumnDefaultOutputEncoding()
    {
        if (PHP_OS == 'AIX') {
            // AIX cannot handle these charsets
            $this->markTestSkipped('Test case cannot run on AIX');
        }

        $iso885915 = iconv('utf-8', 'iso-8859-15', 'Ömläüt');

        Table\Table::setOutputCharset('iso-8859-15');
        $column = new Table\Column('Ömläüt');

        $this->assertEquals($column->render(6), $iso885915);
    }

    public function testColumnSetContentInvalidArgument()
    {
        $this->setExpectedException('Zend\Text\Table\Exception\InvalidArgumentException', 'must be a string');
        $column = new Table\Column(1);
    }

    public function testColumnSetAlignInvalidArgument()
    {
        $this->setExpectedException('Zend\Text\Table\Exception\OutOfBoundsException', 'Invalid align supplied');
        $column = new Table\Column(null, false);
    }

    public function testColumnSetColSpanInvalidArgument()
    {
        $this->setExpectedException('Zend\Text\Table\Exception\InvalidArgumentException', 'must be an integer and greater than 0');
        $column = new Table\Column(null, null, 0);
    }

    public function testColumnRenderInvalidArgument()
    {
        $column = new Table\Column();

        $this->setExpectedException('Zend\Text\Table\Exception\InvalidArgumentException', 'must be an integer and greater than 0');
        $column->render(0);
    }

    public function testUnicodeStringPadding()
    {
        $decorator = new Decorator\Unicode();

        $row = new Table\Row();

        $row->appendColumn(new Table\Column('Eté'));
        $row->appendColumn(new Table\Column('Ete'));

        $this->assertEquals($row->render(array(10, 10), $decorator), "│Eté       │Ete       │\n");
    }

    public function testRowColumnsWithColSpan()
    {
        $decorator = new Decorator\Unicode();

        $row = new Table\Row();

        $row->appendColumn(new Table\Column('foobar'));
        $row->appendColumn(new Table\Column('foobar', null, 2));

        $this->assertEquals($row->render(array(10, 10, 10), $decorator), "│foobar    │foobar               │\n");
    }

    public function testRowWithNoColumns()
    {
        $decorator = new Decorator\Unicode();

        $row = new Table\Row();

        $this->assertEquals($row->render(array(10, 10, 10), $decorator), "│                                │\n");
    }

    public function testRowNotEnoughColumnWidths()
    {
        $decorator = new Decorator\Unicode();

        $row = new Table\Row();
        $row->appendColumn(new Table\Column());
        $row->appendColumn(new Table\Column());

        $this->setExpectedException('Zend\Text\Table\Exception\OverflowException', 'Too many columns');
        $row->render(array(10), $decorator);
    }

    public function testRowGetColumnWidthsBeforeRendering()
    {
        $row = new Table\Row();

        $this->setExpectedException('Zend\Text\Table\Exception\UnexpectedValueException', 'render() must be called');
        $row->getColumnWidths();
    }

    public function testRowAutoInsertColumns()
    {
        $decorator = new Decorator\Unicode();

        $row = new Table\Row();
        $row->appendColumn(new Table\Column('foobar'));

        $this->assertEquals($row->render(array(10, 10, 10), $decorator), "│foobar    │                     │\n");
    }

    public function testRowMultiLine()
    {
        $decorator = new Decorator\Unicode();

        $row = new Table\Row();
        $row->appendColumn(new Table\Column("foo\nbar"));
        $row->appendColumn(new Table\Column("foobar"));

        $this->assertEquals($row->render(array(10, 10), $decorator), "│foo       │foobar    │\n│bar       │          │\n");
    }

    public function testUnicodeRowMultiLine()
    {
        $decorator = new Decorator\Unicode();

        $row = new Table\Row();
        $row->appendColumn(new Table\Column("föö\nbär"));
        $row->appendColumn(new Table\Column("fööbär"));

        $this->assertEquals($row->render(array(3, 10), $decorator), "│föö│fööbär    │\n│bär│          │\n");
    }

    public function testTableConstructInvalidColumnWidthsItem()
    {
        $this->setExpectedException('Zend\Text\Table\Exception\InvalidArgumentException', 'invalid column width');
        $table = new Table\Table(array('columnWidths' => array('foo')));
    }

    public function testTableDecoratorLoaderSimple()
    {
        $table = new Table\Table(array('columnWidths' => array(10), 'decorator' => 'ascii'));

        $row = new Table\Row();
        $row->createColumn('foobar');
        $table->appendRow($row);

        $this->assertEquals($table->render(), "+----------+\n|foobar    |\n+----------+\n");
    }

    public function testTableDecoratorEncodingDefault()
    {
        Table\Table::setOutputCharset('iso-8859-15');

        $table = new Table\Table(array('columnWidths' => array(10)));

        $row = new Table\Row();
        $row->createColumn('foobar');
        $table->appendRow($row);

        $this->assertEquals($table->render(), "+----------+\n|foobar    |\n+----------+\n");
    }

    public function testTableDecoratorLoaderAdvanced()
    {
        $table = new Table\Table(array('columnWidths' => array(10), 'decorator' => new Decorator\Ascii()));

        $row = new Table\Row();
        $row->createColumn('foobar');
        $table->appendRow($row);

        $this->assertEquals($table->render(), "+----------+\n|foobar    |\n+----------+\n");
    }

    public function testTableSimpleRow()
    {
        $table = new Table\Table(array('columnWidths' => array(10)));

        $row = new Table\Row();
        $row->createColumn('foobar');
        $table->appendRow($row);

        $this->assertEquals($table->render(), "┌──────────┐\n│foobar    │\n└──────────┘\n");
    }

    public function testDefaultColumnAlign()
    {
        $table = new Table\Table(array('columnWidths' => array(10)));

        $table->setDefaultColumnAlign(0, Table\Column::ALIGN_CENTER);

        $table->appendRow(array('foobar'));

        $this->assertEquals($table->render(), "┌──────────┐\n│  foobar  │\n└──────────┘\n");
    }

    public function testRowGetColumns()
    {
        $row = new Table\Row();
        $row->createColumn('foo')
            ->createColumn('bar');

        $this->assertEquals(2, count($row->getColumns()));
    }

    public function testRowGetColumn()
    {
        $row = new Table\Row();
        $row->createColumn('foo');

        $this->assertTrue($row->getColumn(0) instanceof Table\Column);
    }

    public function testRowGetInvalidColumn()
    {
        $row = new Table\Row();
        $row->createColumn('foo');

        $this->assertEquals(null, $row->getColumn(1));
    }

    public function testTableWithoutRows()
    {
        $table = new Table\Table(array('columnWidths' => array(10)));

        $this->setExpectedException('Zend\Text\Table\Exception\UnexpectedValueException', 'No rows were added');
        $table->render();
    }

    public function testTableColSpanWithMultipleRows()
    {
        $table = new Table\Table(array('columnWidths' => array(10, 10)));

        $row = new Table\Row();
        $row->appendColumn(new Table\Column('foobar'));
        $row->appendColumn(new Table\Column('foobar'));
        $table->appendRow($row);

        $row = new Table\Row();
        $row->appendColumn(new Table\Column('foobar', null, 2));
        $table->appendRow($row);

        $this->assertEquals($table->render(),   "┌──────────┬──────────┐\n"
                                              . "│foobar    │foobar    │\n"
                                              . "├──────────┴──────────┤\n"
                                              . "│foobar               │\n"
                                              . "└─────────────────────┘\n");
    }

    public function testTableComplex()
    {
        $table = new Table\Table(array('columnWidths' => array(10, 10, 10)));

        $row = new Table\Row();
        $row->appendColumn(new Table\Column('foobar'));
        $row->appendColumn(new Table\Column('foobar', null, 2));
        $table->appendRow($row);

        $row = new Table\Row();
        $row->appendColumn(new Table\Column('foobar'));
        $row->appendColumn(new Table\Column('foobar', null, 2));
        $table->appendRow($row);

        $row = new Table\Row();
        $row->appendColumn(new Table\Column('foobar', null, 3));
        $table->appendRow($row);

        $row = new Table\Row();
        $row->appendColumn(new Table\Column('foobar'));
        $row->appendColumn(new Table\Column('foobar'));
        $row->appendColumn(new Table\Column('foobar'));
        $table->appendRow($row);

        $this->assertEquals($table->render(),   "┌──────────┬─────────────────────┐\n"
                                              . "│foobar    │foobar               │\n"
                                              . "├──────────┼─────────────────────┤\n"
                                              . "│foobar    │foobar               │\n"
                                              . "├──────────┴─────────────────────┤\n"
                                              . "│foobar                          │\n"
                                              . "├──────────┬──────────┬──────────┤\n"
                                              . "│foobar    │foobar    │foobar    │\n"
                                              . "└──────────┴──────────┴──────────┘\n");
    }

    public function testTableMagicToString()
    {
        $table = new Table\Table(array('columnWidths' => array(10)));

        $row = new Table\Row();
        $row->appendColumn(new Table\Column('foobar'));
        $table->appendRow($row);

        $this->assertEquals((string) $table, "┌──────────┐\n│foobar    │\n└──────────┘\n");
    }

    public function testDecoratorUnicode()
    {
        $decorator = new Decorator\Unicode();

        $chars = $decorator->getBottomLeft()
               . $decorator->getBottomRight()
               . $decorator->getCross()
               . $decorator->getHorizontal()
               . $decorator->getHorizontalDown()
               . $decorator->getHorizontalUp()
               . $decorator->getTopLeft()
               . $decorator->getTopRight()
               . $decorator->getVertical()
               . $decorator->getVerticalLeft()
               . $decorator->getVerticalRight();

        $this->assertEquals($chars, '└┘┼─┬┴┌┐│┤├');
    }

    public function testDecoratorAscii()
    {
        $decorator = new Decorator\Ascii();

        $chars = $decorator->getBottomLeft()
               . $decorator->getBottomRight()
               . $decorator->getCross()
               . $decorator->getHorizontal()
               . $decorator->getHorizontalDown()
               . $decorator->getHorizontalUp()
               . $decorator->getTopLeft()
               . $decorator->getTopRight()
               . $decorator->getVertical()
               . $decorator->getVerticalLeft()
               . $decorator->getVerticalRight();

        $this->assertEquals($chars, '+++-++++|++');
    }

    public function testDecoratorBlank()
    {
        $decoratorManager = new Table\DecoratorManager;
        $decorator = $decoratorManager->get('blank');

        $chars = $decorator->getBottomLeft()
               . $decorator->getBottomRight()
               . $decorator->getCross()
               . $decorator->getHorizontal()
               . $decorator->getHorizontalDown()
               . $decorator->getHorizontalUp()
               . $decorator->getTopLeft()
               . $decorator->getTopRight()
               . $decorator->getVertical()
               . $decorator->getVerticalLeft()
               . $decorator->getVerticalRight();

        $this->assertEquals($chars, '');
    }
}
