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
 * @package    Zend_Text
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Text_FigletTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Text_TableTest::main");
}

/**
 * Test helper
 */


/**
 * @category   Zend
 * @package    Zend_Text
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Text
 */
class Zend_Text_TableTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Text_TableTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function tearDown()
    {
        Zend_Text_Table::setInputCharset('utf-8');
        Zend_Text_Table::setOutputCharset('utf-8');
    }

    public function testColumnAlignLeft()
    {
        $column = new Zend_Text_Table_Column("foobar\nfoo");

        $this->assertEquals($column->render(10), "foobar    \nfoo       ");
    }

    public function testColumnPadding()
    {
        $column = new Zend_Text_Table_Column("foobar\nfoo");

        $this->assertEquals($column->render(10, 1), " foobar   \n foo      ");
    }

    public function testColumnWordwrap()
    {
        $column = new Zend_Text_Table_Column("foobar");

        $this->assertEquals($column->render(3), "foo\nbar");
    }

    public function testColumnUnicodeWordwrap()
    {
        $column = new Zend_Text_Table_Column("Ömläüt");

        $this->assertEquals($column->render(3), "Öml\näüt");
    }

    public function testColumnAlignCenter()
    {
        $column = new Zend_Text_Table_Column("foobar\nfoo", Zend_Text_Table_Column::ALIGN_CENTER);

        $this->assertEquals($column->render(10), "  foobar  \n   foo    ");
    }

    public function testColumnAlignRight()
    {
        $column = new Zend_Text_Table_Column("foobar\nfoo", Zend_Text_Table_Column::ALIGN_RIGHT);

        $this->assertEquals($column->render(10), "    foobar\n       foo");
    }

    public function testColumnForcedEncoding()
    {
        if (PHP_OS == 'AIX') {
            // AIX cannot handle these charsets
            $this->markTestSkipped('Test case cannot run on AIX');
        }

        $iso885915 = iconv('utf-8', 'iso-8859-15', 'Ömläüt');

        $column = new Zend_Text_Table_Column($iso885915, null, null, 'iso-8859-15');

        $this->assertEquals($column->render(6), 'Ömläüt');
    }

    public function testColumnDefaultInputEncoding()
    {
        if (PHP_OS == 'AIX') {
            // AIX cannot handle these charsets
            $this->markTestSkipped('Test case cannot run on AIX');
        }

        $iso885915 = iconv('utf-8', 'iso-8859-15', 'Ömläüt');

        Zend_Text_Table::setInputCharset('iso-8859-15');
        $column = new Zend_Text_Table_Column($iso885915);

        $this->assertEquals($column->render(6), 'Ömläüt');
    }

    public function testColumnDefaultOutputEncoding()
    {
        if (PHP_OS == 'AIX') {
            // AIX cannot handle these charsets
            $this->markTestSkipped('Test case cannot run on AIX');
        }

        $iso885915 = iconv('utf-8', 'iso-8859-15', 'Ömläüt');

        Zend_Text_Table::setOutputCharset('iso-8859-15');
        $column = new Zend_Text_Table_Column('Ömläüt');

        $this->assertEquals($column->render(6), $iso885915);
    }

    public function testColumnSetContentInvalidArgument()
    {
        try {
            $column = new Zend_Text_Table_Column(1);
            $this->fail('An expected InvalidArgumentException has not been raised');
        } catch (Zend_Text_Table_Exception $expected) {
            $this->assertContains('$content must be a string', $expected->getMessage());
        }
    }

    public function testColumnSetAlignInvalidArgument()
    {
        try {
            $column = new Zend_Text_Table_Column(null, false);
            $this->fail('An expected InvalidArgumentException has not been raised');
        } catch (Zend_Text_Table_Exception $expected) {
            $this->assertContains('Invalid align supplied', $expected->getMessage());
        }
    }

    public function testColumnSetColSpanInvalidArgument()
    {
        try {
            $column = new Zend_Text_Table_Column(null, null, 0);
            $this->fail('An expected InvalidArgumentException has not been raised');
        } catch (Zend_Text_Table_Exception $expected) {
            $this->assertContains('$colSpan must be an integer and greater than 0', $expected->getMessage());
        }
    }

    public function testColumnRenderInvalidArgument()
    {
        try {
            $column = new Zend_Text_Table_Column();
            $column->render(0);
            $this->fail('An expected InvalidArgumentException has not been raised');
        } catch (Zend_Text_Table_Exception $expected) {
            $this->assertContains('$columnWidth must be an integer and greater than 0', $expected->getMessage());
        }
    }

    public function testUnicodeStringPadding()
    {
        $decorator = new Zend_Text_Table_Decorator_Unicode();

        $row = new Zend_Text_Table_Row();

        $row->appendColumn(new Zend_Text_Table_Column('Eté'));
        $row->appendColumn(new Zend_Text_Table_Column('Ete'));

        $this->assertEquals($row->render(array(10, 10), $decorator), "│Eté       │Ete       │\n");
    }

    public function testRowColumnsWithColSpan()
    {
        $decorator = new Zend_Text_Table_Decorator_Unicode();

        $row = new Zend_Text_Table_Row();

        $row->appendColumn(new Zend_Text_Table_Column('foobar'));
        $row->appendColumn(new Zend_Text_Table_Column('foobar', null, 2));

        $this->assertEquals($row->render(array(10, 10, 10), $decorator), "│foobar    │foobar               │\n");
    }

    public function testRowWithNoColumns()
    {
        $decorator = new Zend_Text_Table_Decorator_Unicode();

        $row = new Zend_Text_Table_Row();

        $this->assertEquals($row->render(array(10, 10, 10), $decorator), "│                                │\n");
    }

    public function testRowNotEnoughColumnWidths()
    {
        $decorator = new Zend_Text_Table_Decorator_Unicode();

        $row = new Zend_Text_Table_Row();
        $row->appendColumn(new Zend_Text_Table_Column());
        $row->appendColumn(new Zend_Text_Table_Column());

        try {
            $row->render(array(10), $decorator);
            $this->fail('An expected Zend_Text_Table_Exception has not been raised');
        } catch (Zend_Text_Table_Exception $expected) {
            $this->assertContains('Too many columns', $expected->getMessage());
        }
    }

    public function testRowGetColumnWidthsBeforeRendering()
    {
        $row = new Zend_Text_Table_Row();

        try {
            $row->getColumnWidths();
            $this->fail('An expected Zend_Text_Table_Exception has not been raised');
        } catch (Zend_Text_Table_Exception $expected) {
            $this->assertContains('No columns were rendered yet', $expected->getMessage());
        }
    }

    public function testRowAutoInsertColumns()
    {
        $decorator = new Zend_Text_Table_Decorator_Unicode();

        $row = new Zend_Text_Table_Row();
        $row->appendColumn(new Zend_Text_Table_Column('foobar'));

        $this->assertEquals($row->render(array(10, 10, 10), $decorator), "│foobar    │                     │\n");
    }

    public function testRowMultiLine()
    {
        $decorator = new Zend_Text_Table_Decorator_Unicode();

        $row = new Zend_Text_Table_Row();
        $row->appendColumn(new Zend_Text_Table_Column("foo\nbar"));
        $row->appendColumn(new Zend_Text_Table_Column("foobar"));

        $this->assertEquals($row->render(array(10, 10), $decorator), "│foo       │foobar    │\n│bar       │          │\n");
    }

    public function testTableConstructInvalidColumnWidths()
    {
        try {
            $table = new Zend_Text_Table(array('columnWidths' => array()));
            $this->fail('An expected Zend_Text_Table_Exception has not been raised');
        } catch (Zend_Text_Table_Exception $expected) {
            $this->assertContains('You must supply at least one column', $expected->getMessage());
        }
    }

    public function testTableConstructInvalidColumnWidthsItem()
    {
        try {
            $table = new Zend_Text_Table(array('columnWidths' => array('foo')));
            $this->fail('An expected Zend_Text_Table_Exception has not been raised');
        } catch (Zend_Text_Table_Exception $expected) {
            $this->assertContains('Column 0 has an invalid column width', $expected->getMessage());
        }
    }

    public function testTableDecoratorLoaderSimple()
    {
        $table = new Zend_Text_Table(array('columnWidths' => array(10), 'decorator' => 'ascii'));

        $row = new Zend_Text_Table_Row();
        $row->createColumn('foobar');
        $table->appendRow($row);

        $this->assertEquals($table->render(), "+----------+\n|foobar    |\n+----------+\n");
    }

    public function testTableDecoratorEncodingDefault()
    {
        Zend_Text_Table::setOutputCharset('iso-8859-15');

        $table = new Zend_Text_Table(array('columnWidths' => array(10)));

        $row = new Zend_Text_Table_Row();
        $row->createColumn('foobar');
        $table->appendRow($row);

        $this->assertEquals($table->render(), "+----------+\n|foobar    |\n+----------+\n");
    }

    public function testTableDecoratorLoaderAdvanced()
    {
        $table = new Zend_Text_Table(array('columnWidths' => array(10), 'decorator' => new Zend_Text_Table_Decorator_Ascii()));

        $row = new Zend_Text_Table_Row();
        $row->createColumn('foobar');
        $table->appendRow($row);

        $this->assertEquals($table->render(), "+----------+\n|foobar    |\n+----------+\n");
    }

    public function testTableSimpleRow()
    {
        $table = new Zend_Text_Table(array('columnWidths' => array(10)));

        $row = new Zend_Text_Table_Row();
        $row->createColumn('foobar');
        $table->appendRow($row);

        $this->assertEquals($table->render(), "┌──────────┐\n│foobar    │\n└──────────┘\n");
    }

    public function testDefaultColumnAlign()
    {
        $table = new Zend_Text_Table(array('columnWidths' => array(10)));

        $table->setDefaultColumnAlign(0, Zend_Text_Table_Column::ALIGN_CENTER);

        $table->appendRow(array('foobar'));

        $this->assertEquals($table->render(), "┌──────────┐\n│  foobar  │\n└──────────┘\n");
    }

    public function testRowGetColumns()
    {
        $row = new Zend_Text_Table_Row();
        $row->createColumn('foo')
            ->createColumn('bar');

        $this->assertEquals(2, count($row->getColumns()));
    }

    public function testRowGetColumn()
    {
        $row = new Zend_Text_Table_Row();
        $row->createColumn('foo');

        $this->assertTrue($row->getColumn(0) instanceof Zend_Text_Table_Column);
    }

    public function testRowGetInvalidColumn()
    {
        $row = new Zend_Text_Table_Row();
        $row->createColumn('foo');

        $this->assertEquals(null, $row->getColumn(1));
    }

    public function testTableWithoutRows()
    {
        $table = new Zend_Text_Table(array('columnWidths' => array(10)));

        try {
            $table->render();
            $this->fail('An expected Zend_Text_Table_Exception has not been raised');
        } catch (Zend_Text_Table_Exception $expected) {
            $this->assertContains('No rows were added to the table yet', $expected->getMessage());
        }
    }

    public function testTableColSpanWithMultipleRows()
    {
        $table = new Zend_Text_Table(array('columnWidths' => array(10, 10)));

        $row = new Zend_Text_Table_Row();
        $row->appendColumn(new Zend_Text_Table_Column('foobar'));
        $row->appendColumn(new Zend_Text_Table_Column('foobar'));
        $table->appendRow($row);

        $row = new Zend_Text_Table_Row();
        $row->appendColumn(new Zend_Text_Table_Column('foobar', null, 2));
        $table->appendRow($row);

        $this->assertEquals($table->render(),   "┌──────────┬──────────┐\n"
                                              . "│foobar    │foobar    │\n"
                                              . "├──────────┴──────────┤\n"
                                              . "│foobar               │\n"
                                              . "└─────────────────────┘\n");
    }

    public function testTableComplex()
    {
        $table = new Zend_Text_Table(array('columnWidths' => array(10, 10, 10)));

        $row = new Zend_Text_Table_Row();
        $row->appendColumn(new Zend_Text_Table_Column('foobar'));
        $row->appendColumn(new Zend_Text_Table_Column('foobar', null, 2));
        $table->appendRow($row);

        $row = new Zend_Text_Table_Row();
        $row->appendColumn(new Zend_Text_Table_Column('foobar'));
        $row->appendColumn(new Zend_Text_Table_Column('foobar', null, 2));
        $table->appendRow($row);

        $row = new Zend_Text_Table_Row();
        $row->appendColumn(new Zend_Text_Table_Column('foobar', null, 3));
        $table->appendRow($row);

        $row = new Zend_Text_Table_Row();
        $row->appendColumn(new Zend_Text_Table_Column('foobar'));
        $row->appendColumn(new Zend_Text_Table_Column('foobar'));
        $row->appendColumn(new Zend_Text_Table_Column('foobar'));
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
        $table = new Zend_Text_Table(array('columnWidths' => array(10)));

        $row = new Zend_Text_Table_Row();
        $row->appendColumn(new Zend_Text_Table_Column('foobar'));
        $table->appendRow($row);

        $this->assertEquals((string) $table, "┌──────────┐\n│foobar    │\n└──────────┘\n");
    }

    public function testDecoratorUnicode()
    {
        $decorator = new Zend_Text_Table_Decorator_Unicode();

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
        $decorator = new Zend_Text_Table_Decorator_Ascii();

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
}

// Call Zend_Text_TableTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Text_TableTest::main") {
    Zend_Text_TableTest::main();
}
