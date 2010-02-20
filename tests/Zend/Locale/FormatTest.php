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
 * @package    Zend_Format
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * Zend_Locale_Format
 */
require_once 'Zend/Locale/Format.php';

/**
 * @category   Zend
 * @package    Zend_Locale
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Locale
 */
class Zend_Locale_FormatTest extends PHPUnit_Framework_TestCase
{
    /**
     * teardown / cleanup
     */
    public function tearDown()
    {
        // if the setlocale option is enabled, then don't change the setlocale below
        if (defined('TESTS_ZEND_LOCALE_FORMAT_SETLOCALE') && TESTS_ZEND_LOCALE_FORMAT_SETLOCALE === false) {
            // I'm anticipating possible platform inconsistencies, so I'm leaving some debug comments for now.
            //echo '<<<', setlocale(LC_NUMERIC, '0'); // show locale before changing
            setlocale(LC_ALL, 'C'); // attempt to restore global setting i.e. test teardown
            //echo '>>>', setlocale(LC_NUMERIC, '0'); // show locale after changing
            //echo "\n";
        } else if (defined('TESTS_ZEND_LOCALE_FORMAT_SETLOCALE')) {
            setlocale(LC_ALL, TESTS_ZEND_LOCALE_FORMAT_SETLOCALE);
        }
    }

    /**
     * test getNumber
     * expected integer
     */
    public function testGetNumber()
    {
        $this->assertEquals(       0,         Zend_Locale_Format::getNumber(       0)        );
        $this->assertEquals(-1234567,         Zend_Locale_Format::getNumber(-1234567)        );
        $this->assertEquals( 1234567,         Zend_Locale_Format::getNumber( 1234567)        );
        $this->assertEquals(       0.1234567, Zend_Locale_Format::getNumber(       0.1234567));
        $this->assertEquals(-1234567.12345,   Zend_Locale_Format::getNumber(-1234567.12345)  );
        $this->assertEquals( 1234567.12345,   Zend_Locale_Format::getNumber(1234567.12345)   );
        $options = array('locale' => 'de');
        $this->assertEquals(       0,         Zend_Locale_Format::getNumber(         '0',         $options));
        $this->assertEquals(-1234567,         Zend_Locale_Format::getNumber(  '-1234567',         $options));
        $this->assertEquals( 1234567,         Zend_Locale_Format::getNumber(   '1234567',         $options));
        $this->assertEquals(       0.1234567, Zend_Locale_Format::getNumber('0,1234567', $options));
        $this->assertEquals(-1234567.12345,   Zend_Locale_Format::getNumber('-1.234.567,12345',   $options));
        $this->assertEquals( 1234567.12345,   Zend_Locale_Format::getNumber('1.234.567,12345',   $options));
        $options = array('locale' => 'de_AT');
        $this->assertEquals(       0,         Zend_Locale_Format::getNumber(         '0',         $options));
        $this->assertEquals(-1234567,         Zend_Locale_Format::getNumber(  '-1234567',         $options));
        $this->assertEquals( 1234567,         Zend_Locale_Format::getNumber( '1.234.567',         $options));
        $this->assertEquals(       0.1234567, Zend_Locale_Format::getNumber(         '0,1234567', $options));
        $this->assertEquals(-1234567.12345,   Zend_Locale_Format::getNumber('-1.234.567,12345',   $options));
        $this->assertEquals( 1234567.12345,   Zend_Locale_Format::getNumber( '1.234.567,12345',   $options));
    }

    /**
     * test to number
     * expected string
     */
    public function testToNumber()
    {
        $this->assertEquals('0', Zend_Locale_Format::toNumber(0)                         );
        $this->assertEquals('0', Zend_Locale_Format::toNumber(0, array('locale' => 'de')));

        $options = array('locale' => 'de_AT');
        $this->assertEquals(          '0',         Zend_Locale_Format::toNumber(       0,        $options));
        $this->assertEquals( '-1.234.567',         Zend_Locale_Format::toNumber(-1234567,        $options));
        $this->assertEquals(  '1.234.567',         Zend_Locale_Format::toNumber( 1234567,        $options));
        $this->assertEquals(          '0,1234567', Zend_Locale_Format::toNumber(       0.1234567,$options));
        $this->assertEquals( '-1.234.567,12345',   Zend_Locale_Format::toNumber(-1234567.12345,  $options));
        $this->assertEquals(  '1.234.567,12345',   Zend_Locale_Format::toNumber( 1234567.12345,  $options));
        $this->assertEquals(    '1234567,12345',   Zend_Locale_Format::toNumber( 1234567.12345,  array('locale' => 'ar_QA')));
        $this->assertEquals(    '1234567,12345-',  Zend_Locale_Format::toNumber(-1234567.12345,  array('locale' => 'ar_QA')));
        $this->assertEquals(  '12,34,567.12345',   Zend_Locale_Format::toNumber( 1234567.12345,  array('locale' => 'dz_BT')));
        $this->assertEquals(  '-1.234.567,12345',  Zend_Locale_Format::toNumber(-1234567.12345,  array('locale' => 'mk_MK')));
        $this->assertEquals(        '452.25',      Zend_Locale_Format::toNumber(     452.25,     array('locale' => 'en_US')));
        $this->assertEquals(     '54,321.1234',    Zend_Locale_Format::toNumber(   54321.1234,   array('locale' => 'en_US')));
        $this->assertEquals(          '1,23',      Zend_Locale_Format::toNumber(       1.234567, array('locale' => 'de_DE', 'precision' => 2, 'number_format' => '0.00')));
        $this->assertEquals(         '-0,75',      Zend_Locale_Format::toNumber(      -0.75,     array('locale' => 'de_DE', 'precision' => 2)));
    }


    /**
     * test isNumber
     * expected boolean
     */
    public function testIsNumber()
    {
        $this->assertTrue( Zend_Locale_Format::isNumber('-1.234.567,12345',  array('locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::isNumber('textwithoutnumber', array('locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::isNumber('', array('locale' => 'de_AT')));
    }

    /**
     * test isNumber
     * expected boolean
     *
     * @group ZF-5879
     */
    public function testIsNumberENotation()
    {
        $this->assertTrue( Zend_Locale_Format::isNumber('5,0004E+5',  array('locale' => 'de_AT')));
        $this->assertTrue( Zend_Locale_Format::isNumber('2,34E-7',    array('locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::isNumber('2.34E-7E-7', array('locale' => 'de_AT')));
    }


    /**
     * test getFloat
     * expected exception
     */
    public function testgetFloat()
    {
        try {
            $value = Zend_Locale_Format::getFloat('nocontent');
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertEquals(       0,         Zend_Locale_Format::getFloat(       0        ));
        $this->assertEquals(-1234567,         Zend_Locale_Format::getFloat(-1234567        ));
        $this->assertEquals( 1234567,         Zend_Locale_Format::getFloat( 1234567        ));
        $this->assertEquals(       0.1234567, Zend_Locale_Format::getFloat(       0.1234567));
        $this->assertEquals(-1234567.12345,   Zend_Locale_Format::getFloat(-1234567.12345  ));
        $this->assertEquals( 1234567.12345,   Zend_Locale_Format::getFloat( 1234567.12345  ));

        $options = array('locale' => 'de');
        $this->assertEquals(       0,         Zend_Locale_Format::getFloat(         '0',         $options));
        $this->assertEquals(-1234567,         Zend_Locale_Format::getFloat(  '-1234567',         $options));
        $this->assertEquals( 1234567,         Zend_Locale_Format::getFloat(   '1234567',         $options));
        $this->assertEquals(       0.1234567, Zend_Locale_Format::getFloat(         '0,1234567', $options));
        $this->assertEquals(-1234567.12345,   Zend_Locale_Format::getFloat('-1.234.567,12345',   $options));
        $this->assertEquals( 1234567.12345,   Zend_Locale_Format::getFloat( '1.234.567,12345',   $options));

        $options = array('locale' => 'de_AT');
        $this->assertEquals(       0,         Zend_Locale_Format::getFloat(         '0',         $options));
        $this->assertEquals(-1234567,         Zend_Locale_Format::getFloat(  '-1234567',         $options));
        $this->assertEquals( 1234567,         Zend_Locale_Format::getFloat( '1.234.567',         $options));
        $this->assertEquals(       0.1234567, Zend_Locale_Format::getFloat(         '0,1234567', $options));
        $this->assertEquals(-1234567.12345,   Zend_Locale_Format::getFloat('-1.234.567,12345',   $options));
        $this->assertEquals( 1234567.12345,   Zend_Locale_Format::getFloat( '1.234.567,12345',   $options));

        $options = array('precision' => 2, 'locale' => 'de_AT');
        $this->assertEquals(       0,    Zend_Locale_Format::getFloat(         '0',         $options));
        $this->assertEquals(-1234567,    Zend_Locale_Format::getFloat(  '-1234567',         $options));
        $this->assertEquals( 1234567,    Zend_Locale_Format::getFloat( '1.234.567',         $options));
        $this->assertEquals(       0.12, Zend_Locale_Format::getFloat(         '0,1234567', $options));
        $this->assertEquals(-1234567.12, Zend_Locale_Format::getFloat('-1.234.567,12345',   $options));
        $this->assertEquals( 1234567.12, Zend_Locale_Format::getFloat( '1.234.567,12345',   $options));

        $options = array('precision' => 7, 'locale' => 'de_AT');
        $this->assertEquals('1234567.12345', Zend_Locale_Format::getFloat('1.234.567,12345', $options));
    }


    /**
     * test toFloat
     * expected string
     */
    public function testToFloat()
    {
        $this->assertEquals('0', Zend_Locale_Format::toFloat(0)                         );
        $this->assertEquals('0', Zend_Locale_Format::toFloat(0, array('locale' => 'de')));

        $options = array('locale' => 'de_AT');
        $this->assertEquals(         '0',         Zend_Locale_Format::toFloat(       0,         $options));
        $this->assertEquals('-1.234.567',         Zend_Locale_Format::toFloat(-1234567,         $options));
        $this->assertEquals( '1.234.567',         Zend_Locale_Format::toFloat( 1234567,         $options));
        $this->assertEquals(         '0,1234567', Zend_Locale_Format::toFloat(       0.1234567, $options));
        $this->assertEquals('-1.234.567,12345',   Zend_Locale_Format::toFloat(-1234567.12345,   $options));
        $this->assertEquals( '1.234.567,12345',   Zend_Locale_Format::toFloat( 1234567.12345,   $options));

        $options = array('locale' => 'ar_QA');
        $this->assertEquals(    '1234567,12345',  Zend_Locale_Format::toFloat( 1234567.12345, $options                  ));
        $this->assertEquals(    '1234567,12345-', Zend_Locale_Format::toFloat(-1234567.12345, $options                  ));
        $this->assertEquals(  '12,34,567.12345',  Zend_Locale_Format::toFloat( 1234567.12345, array('locale' => 'dz_BT')));
        $this->assertEquals(  '-1.234.567,12345', Zend_Locale_Format::toFloat(-1234567.12345, array('locale' => 'mk_MK')));

        $options = array('precision' => 2, 'locale' => 'de_AT');
        $this->assertEquals(         '0,00', Zend_Locale_Format::toFloat(       0,         $options));
        $this->assertEquals('-1.234.567,00', Zend_Locale_Format::toFloat(-1234567,         $options));
        $this->assertEquals( '1.234.567,00', Zend_Locale_Format::toFloat( 1234567,         $options));
        $this->assertEquals(         '0,12', Zend_Locale_Format::toFloat(       0.1234567, $options));
        $this->assertEquals('-1.234.567,12', Zend_Locale_Format::toFloat(-1234567.12345,   $options));
        $this->assertEquals( '1.234.567,12', Zend_Locale_Format::toFloat( 1234567.12345,   $options));

        $options = array('precision' => 7, 'locale' => 'de_AT');
        $this->assertEquals('1.234.567,1234500', Zend_Locale_Format::toFloat(1234567.12345, $options));
    }


    /**
     * test isFloat
     * expected boolean
     */
    public function testIsFloat()
    {
        $this->assertTrue( Zend_Locale_Format::isFloat('-1.234.567,12345',  array('locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::isFloat('textwithoutnumber', array('locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::isFloat(''));
        $this->assertFalse(Zend_Locale_Format::isFloat('', array('locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::isFloat(null));
        $this->assertFalse(Zend_Locale_Format::isFloat(null, array('locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::isFloat(',', array('locale' => 'de_AT')));
        $this->assertTrue(Zend_Locale_Format::isFloat('1e20', array('locale' => 'de_AT')));
        $this->assertTrue(Zend_Locale_Format::isFloat('1e-20', array('locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::isFloat('1-e20', array('locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::isFloat('1-20', array('locale' => 'de_AT')));
        $this->assertTrue(Zend_Locale_Format::isFloat('123.345'));
        $this->assertTrue(Zend_Locale_Format::isFloat('+123.345'));
    }


    /**
     * test getInteger
     * expected integer
     */
    public function testgetInteger()
    {
        $this->assertEquals(       0, Zend_Locale_Format::getInteger(       0        ));
        $this->assertEquals(-1234567, Zend_Locale_Format::getInteger(-1234567        ));
        $this->assertEquals( 1234567, Zend_Locale_Format::getInteger( 1234567        ));
        $this->assertEquals(       0, Zend_Locale_Format::getInteger(       0.1234567));
        $this->assertEquals(-1234567, Zend_Locale_Format::getInteger(-1234567.12345  ));
        $this->assertEquals( 1234567, Zend_Locale_Format::getInteger( 1234567.12345  ));

        $options = array('locale' => 'de');
        $this->assertEquals(       0, Zend_Locale_Format::getInteger(       '0',         $options));
        $this->assertEquals(-1234567, Zend_Locale_Format::getInteger('-1234567',         $options));
        $this->assertEquals( 1234567, Zend_Locale_Format::getInteger( '1234567',         $options));
        $this->assertEquals(       0, Zend_Locale_Format::getInteger(       '0,1234567', $options));
        $this->assertEquals(-1234567, Zend_Locale_Format::getInteger('-1.234.567,12345', $options));
        $this->assertEquals( 1234567, Zend_Locale_Format::getInteger( '1.234.567,12345', $options));

        $options = array('locale' => 'de_AT');
        $this->assertEquals(       0, Zend_Locale_Format::getInteger(         '0',         $options));
        $this->assertEquals(-1234567, Zend_Locale_Format::getInteger(  '-1234567',         $options));
        $this->assertEquals( 1234567, Zend_Locale_Format::getInteger( '1.234.567',         $options));
        $this->assertEquals(       0, Zend_Locale_Format::getInteger(         '0,1234567', $options));
        $this->assertEquals(-1234567, Zend_Locale_Format::getInteger('-1.234.567,12345',   $options));
        $this->assertEquals( 1234567, Zend_Locale_Format::getInteger( '1.234.567,12345',   $options));
    }


    /**
     * test toInteger
     * expected string
     */
    public function testtoInteger()
    {
        $this->assertEquals('0', Zend_Locale_Format::toInteger(0                         ));
        $this->assertEquals('0', Zend_Locale_Format::toInteger(0, array('locale' => 'de')));

        $options = array('locale' => 'de_AT');
        $this->assertEquals(          '0',  Zend_Locale_Format::toInteger(       0,         $options));
        $this->assertEquals( '-1.234.567',  Zend_Locale_Format::toInteger(-1234567,         $options));
        $this->assertEquals(  '1.234.567',  Zend_Locale_Format::toInteger( 1234567,         $options));
        $this->assertEquals(          '0',  Zend_Locale_Format::toInteger(       0.1234567, $options));
        $this->assertEquals( '-1.234.567',  Zend_Locale_Format::toInteger(-1234567.12345,   $options));
        $this->assertEquals(  '1.234.567',  Zend_Locale_Format::toInteger( 1234567.12345,   $options));
        $this->assertEquals(    '1234567',  Zend_Locale_Format::toInteger( 1234567.12345,   array('locale' => 'ar_QA')));
        $this->assertEquals(    '1234567-', Zend_Locale_Format::toInteger(-1234567.12345,   array('locale' => 'ar_QA')));
        $this->assertEquals(  '12,34,567',  Zend_Locale_Format::toInteger( 1234567.12345,   array('locale' => 'dz_BT')));
        $this->assertEquals(  '-1.234.567', Zend_Locale_Format::toInteger(-1234567.12345,   array('locale' => 'mk_MK')));

        $this->assertEquals('-45', Zend_Locale_Format::toInteger(-45.23, $options));
        $this->assertEquals('-46', Zend_Locale_Format::toInteger(-45.99, $options));
    }


    /**
     * test isInteger
     * expected boolean
     */
    public function testIsInteger()
    {
        $this->assertTrue( Zend_Locale_Format::isInteger('-1.234.567',  array('locale' => 'de_AT')));
        $this->assertFalse( Zend_Locale_Format::isInteger('-1.234.567,12345',  array('locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::isInteger('textwithoutnumber', array('locale' => 'de_AT')));
    }


    /**
     * test getDate
     * expected array
     */
    public function testgetDate()
    {
        try {
            $value = Zend_Locale_Format::getDate('no content');
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }
        $this->assertTrue(is_array(Zend_Locale_Format::getDate('10.10.06')));
        $this->assertEquals(5, count(Zend_Locale_Format::getDate('10.10.06', array('date_format' => 'dd.MM.yy'))));

        $value = Zend_Locale_Format::getDate('10.11.6', array('date_format' => 'dd.MM.yy'));
        $this->assertEquals(10, $value['day']  );
        $this->assertEquals(11, $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('10.11.06', array('date_format' => 'dd.MM.yy'));
        $this->assertEquals(10, $value['day']  );
        $this->assertEquals(11, $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('10.11.2006', array('date_format' => 'dd.MM.yy'));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        try {
            $value = Zend_Locale_Format::getDate('2006.13.01', array('date_format' => 'dd.MM.yy'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006.13.01', array('date_format' => 'dd.MM.yy', 'fix_date' => true));
        $this->assertEquals(13,   $value['day']  );
        $this->assertEquals(1,    $value['month']);
        $this->assertEquals(2006, $value['year'] );

        try {
            $value = Zend_Locale_Format::getDate('2006.01.13', array('date_format' => 'dd.MM.yy'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006.01.13', array('date_format' => 'dd.MM.yy', 'fix_date' => true));
        $this->assertEquals(13,   $value['day']  );
        $this->assertEquals(1,    $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('101106', array('date_format' => 'ddMMyy'));
        $this->assertEquals(10, $value['day']  );
        $this->assertEquals(11, $value['month']);
        $this->assertEquals(2006,  $value['year'] );

        $value = Zend_Locale_Format::getDate('10112006', array('date_format' => 'ddMMyyyy'));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('10 Nov 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT'));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('10 November 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT'));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        try {
            $value = Zend_Locale_Format::getDate('November 10 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('November 10 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );


        try {
            $value = Zend_Locale_Format::getDate('Nov 10 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('Nov 10 2006', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );


        try {
            $value = Zend_Locale_Format::getDate('2006 10 Nov', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006 10 Nov', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('2006 Nov 10', array('date_format' => 'dd.MMM.yy', 'locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('10.11.06', array('date_format' => 'yy.dd.MM'));
        $this->assertEquals(11, $value['day']  );
        $this->assertEquals(6,  $value['month']);
        $this->assertEquals(2010, $value['year'] );

        $value = Zend_Locale_Format::getDate('10.11.06', array('date_format' => 'dd.yy.MM'));
        $this->assertEquals(10, $value['day']  );
        $this->assertEquals(6,  $value['month']);
        $this->assertEquals(2011, $value['year'] );

        $value = Zend_Locale_Format::getDate('10.11.06', array('locale' => 'de_AT'));
        $this->assertEquals(10, $value['day']  );
        $this->assertEquals(11, $value['month']);
        $this->assertEquals(2006,  $value['year'] );

        $value = Zend_Locale_Format::getDate('10.11.2006', array('locale' => 'de_AT'));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        try {
            $value = Zend_Locale_Format::getDate('2006.13.01', array('locale' => 'de_AT'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006.13.01', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals(13,   $value['day']  );
        $this->assertEquals(1,    $value['month']);
        $this->assertEquals(2006, $value['year'] );

        try {
            $value = Zend_Locale_Format::getDate('2006.01.13', array('locale' => 'de_AT'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006.01.13', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals(13,   $value['day']  );
        $this->assertEquals(1,    $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('101106', array('locale' => 'de_AT'));
        $this->assertEquals(10, $value['day']  );
        $this->assertEquals(11, $value['month']);
        $this->assertEquals(2006,  $value['year'] );

        $value = Zend_Locale_Format::getDate('10112006', array('locale' => 'de_AT'));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('10 Nov 2006', array('locale' => 'de_AT'));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('10 November 2006', array('locale' => 'de_AT'));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        try {
            $value = Zend_Locale_Format::getDate('November 10 2006', array('locale' => 'de_AT'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('November 10 2006', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        try {
            $value = Zend_Locale_Format::getDate('April 10 2006', array('locale' => 'de_AT'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('April 10 2006', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(4,    $value['month']);
        $this->assertEquals(2006, $value['year'] );

        try {
            $value = Zend_Locale_Format::getDate('Nov 10 2006', array('locale' => 'de_AT'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('Nov 10 2006', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );


        try {
            $value = Zend_Locale_Format::getDate('Nov 10 2006', array('locale' => 'de_AT'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            $this->assertRegexp('/unable.to.parse/i', $e->getMessage());
            // success
        }
        $value = Zend_Locale_Format::getDate('2006 10 Nov', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('01.April.2006', array('date_format' => 'dd.MMMM.yy', 'locale' => 'de_AT'));
        $this->assertEquals(1,    $value['day']  );
        $this->assertEquals(4,    $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('Montag, 01.April.2006', array('date_format' => 'EEEE, dd.MMMM.yy', 'locale' => 'de_AT'));
        $this->assertEquals(1,    $value['day']  );
        $this->assertEquals(4,    $value['month']);
        $this->assertEquals(2006, $value['year'] );

        try {
            $value = Zend_Locale_Format::getDate('13.2006.11', array('date_format' => 'dd.MM.yy'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        Zend_Locale_Format::setOptions(array('format_type' => 'php'));
        $value = Zend_Locale_Format::getDate('10.11.06', array('date_format' => 'd.m.Y'));
        $this->assertEquals(10, $value['day']  );
        $this->assertEquals(11, $value['month']);
        $this->assertEquals(2006,  $value['year'] );

        $value = Zend_Locale_Format::getDate('10.11.06', array('date_format' => 'd.m.Y', 'fix_date' => true));
        $this->assertEquals(10, $value['day']  );
        $this->assertEquals(11, $value['month']);
        $this->assertEquals(2006,  $value['year'] );

        $this->assertTrue(is_array(Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'HH:mm:ss'))));
        Zend_Locale_Format::setOptions(array('format_type' => 'iso'));

        $value = Zend_Locale_Format::getDate('2006 Nov 10', array('locale' => 'de_AT', 'fix_date' => true));
        $this->assertEquals(10,   $value['day']  );
        $this->assertEquals(11,   $value['month']);
        $this->assertEquals(2006, $value['year'] );

        $value = Zend_Locale_Format::getDate('anything February 31 2007.', array('date_format' => 'M d Y', 'locale'=>'en'));
        $this->assertEquals(31,   $value['day']  );
        $this->assertEquals(2,    $value['month']);
        $this->assertEquals(2007, $value['year'] );
    }

    /**
     * test getTime
     * expected array
     */
    public function testgetTime()
    {
        try {
            $value = Zend_Locale_Format::getTime('no content');
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertTrue(is_array(Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'HH:mm:ss'))));
        $options = array('date_format' => 'h:mm:ss a', 'locale' => 'en');
        $this->assertTrue(is_array(Zend_Locale_Format::getTime('11:14:55 am', $options)));
        $this->assertTrue(is_array(Zend_Locale_Format::getTime('12:14:55 am', $options)));
        $this->assertTrue(is_array(Zend_Locale_Format::getTime('11:14:55 pm', $options)));
        $this->assertTrue(is_array(Zend_Locale_Format::getTime('12:14:55 pm', $options)));

        try {
            $value = Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'nocontent'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        try {
            $value = Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'ZZZZ'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $value = Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'HH:mm:ss.x'));
        $this->assertEquals(13, $value['hour']  );
        $this->assertEquals(14, $value['minute']);
        $this->assertEquals(55, $value['second']);

        $this->assertEquals(5, count(Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'HH:mm:ss'))));

        $value = Zend_Locale_Format::getTime('13:14:55', array('date_format' => 'HH:mm:ss'));
        $this->assertEquals(13, $value['hour']  );
        $this->assertEquals(14, $value['minute']);
        $this->assertEquals(55, $value['second']);

        $value = Zend_Locale_Format::getTime('131455', array('date_format' => 'HH:mm:ss'));
        $this->assertEquals(13, $value['hour']  );
        $this->assertEquals(14, $value['minute']);
        $this->assertEquals(55, $value['second']);
    }

    /**
     * test isDate
     * expected boolean
     */
    public function testIsDate()
    {
        $this->assertTrue( Zend_Locale_Format::checkDateFormat('13.Nov.2006', array('locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('13.XXX.2006', array('locale' => 'ar_EG')));
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('nodate'));

        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20.01.2006', array('date_format' => 'M-d-y')));
        $this->assertTrue( Zend_Locale_Format::checkDateFormat('20.01.2006', array('date_format' => 'd-M-y')));

        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20.April',      array('date_format' => 'dd.MMMM.YYYY')));
        $this->assertTrue(Zend_Locale_Format::checkDateFormat('20.April',      array('date_format' => 'MMMM.YYYY'   )));
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20',            array('date_format' => 'dd.MMMM.YYYY')));
        $this->assertTrue( Zend_Locale_Format::checkDateFormat('April.2007',    array('date_format' => 'MMMM.YYYY'   )));
        $this->assertTrue( Zend_Locale_Format::checkDateFormat('20.April.2007', array('date_format' => 'dd.YYYY'     )));

        $this->assertFalse(Zend_Locale_Format::checkDateFormat('2006.04',          array('date_format' => 'yyyy.MMMM.dd'         )));
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20.04.2007 10:11', array('date_format' => 'dd.MMMM.yyyy HH:mm:ss')));
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20.04.2007 10:20', array('date_format' => 'dd.MMMM.yyyy HH:ss:mm')));
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('20.04.2007 00:20', array('date_format' => 'dd.MMMM.yyyy ss:mm:HH')));
    }


    /**
     * test checkDateFormat -> time
     * expected boolean
     */
    public function testCheckTime()
    {
        $this->assertTrue( Zend_Locale_Format::checkDateFormat('13:10:55',    array('date_format' => 'HH:mm:ss', 'locale' => 'de_AT')));
        $this->assertTrue( Zend_Locale_Format::checkDateFormat('11:10:55 am', array('date_format' => 'HH:mm:ss', 'locale' => 'ar_EG')));
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('notime'));
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('13:10',       array('date_format' => 'HH:mm:ss', 'locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('13',          array('date_format' => 'HH:mm',    'locale' => 'de_AT')));
        $this->assertFalse(Zend_Locale_Format::checkDateFormat('00:13',       array('date_format' => 'ss:mm:HH', 'locale' => 'de_AT')));
    }


    /**
     * test toNumberSystem
     * expected string
     */
    public function testToNumberSystem()
    {
        try {
            $value = Zend_Locale_Format::convertNumerals('١١٠', 'xxxx');
            $this->fail("no conversion expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        try {
            $value = Zend_Locale_Format::convertNumerals('١١٠', 'Arab', 'xxxx');
            $this->fail("no conversion expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertEquals('110', Zend_Locale_Format::convertNumerals('١١٠', 'Arab'));
        $this->assertEquals('११०',  Zend_Locale_Format::convertNumerals('١١٠', 'Arab', 'Deva'));
        $this->assertEquals('११०',  Zend_Locale_Format::convertNumerals('١١٠', 'arab', 'dEVa'));
        $this->assertEquals('١١٠', Zend_Locale_Format::convertNumerals('110', 'Latn', 'Arab'));
        $this->assertEquals('١١٠', Zend_Locale_Format::convertNumerals('110', 'latn', 'Arab'));
    }

    /**
     * test toNumberFormat
     * expected string
     */
    public function testToNumberFormat()
    {
        $locale = new Zend_Locale('de_AT');
        $this->assertEquals('0', Zend_Locale_Format::toNumber(0                            ));
        $this->assertEquals('0', Zend_Locale_Format::toNumber(0, array('locale' => 'de'   )));
        $this->assertEquals('0', Zend_Locale_Format::toNumber(0, array('locale' => $locale)));

        $options = array('locale' => 'de_AT');
        $this->assertEquals('-1.234.567',         Zend_Locale_Format::toNumber(-1234567,         $options));
        $this->assertEquals( '1.234.567',         Zend_Locale_Format::toNumber( 1234567,         $options));
        $this->assertEquals(         '0,1234567', Zend_Locale_Format::toNumber(       0.1234567, $options));
        $this->assertEquals('-1.234.567,12345',   Zend_Locale_Format::toNumber(-1234567.12345,   $options));
        $this->assertEquals( '1.234.567,12345',   Zend_Locale_Format::toNumber( 1234567.12345,   $options));
        $this->assertEquals(   '1234567',         Zend_Locale_Format::toNumber( 1234567.12345,   array('number_format' => '##0',      'locale' => 'de_AT')));
        $this->assertEquals('1.23.45.67',         Zend_Locale_Format::toNumber( 1234567.12345,   array('number_format' => '#,#0',     'locale' => 'de_AT')));
        $this->assertEquals(   '1234567,12',      Zend_Locale_Format::toNumber( 1234567.12345,   array('number_format' => '##0.00',   'locale' => 'de_AT')));
        $this->assertEquals(   '1234567,12345',   Zend_Locale_Format::toNumber( 1234567.12345,   array('number_format' => '##0.###',  'locale' => 'de_AT')));
        $this->assertEquals('1.23.45.67',         Zend_Locale_Format::toNumber( 1234567.12345,   array('number_format' => '#,#0',     'locale' => 'de_AT')));
        $this->assertEquals( '12.34.567',         Zend_Locale_Format::toNumber( 1234567.12345,   array('number_format' => '#,##,##0', 'locale' => 'de_AT')));
        $this->assertEquals( '1.234.567,12',      Zend_Locale_Format::toNumber( 1234567.12345,   array('number_format' => '#,##0.00', 'locale' => 'de_AT')));
        $this->assertEquals('1.23.45.67,12',      Zend_Locale_Format::toNumber( 1234567.12345,   array('number_format' => '#,#0.00',  'locale' => 'de_AT')));
        $this->assertEquals(   '1234567-',        Zend_Locale_Format::toNumber(-1234567.12345,   array('number_format' => '##0;##0-', 'locale' => 'de_AT')));
        $this->assertEquals(   '1234567',         Zend_Locale_Format::toNumber( 1234567.12345,   array('number_format' => '##0;##0-', 'locale' => 'de_AT')));
        $this->assertEquals( '1.234.567,00',      Zend_Locale_Format::toNumber( 1234567,         array('number_format' => '#,##0.00', 'locale' => 'de_AT')));
        $this->assertEquals( '1.234.567,12',      Zend_Locale_Format::toNumber( 1234567.123,     array('precision' => 2,              'locale' => 'de_AT')));
        $this->assertEquals(   '1234567,12-',     Zend_Locale_Format::toNumber(-1234567.123,     array('number_format' => '#0.00-',   'locale' => 'de_AT')));
        $this->assertEquals(   '-12.345',         Zend_Locale_Format::toNumber(  -12345.67,      array('precision' => 0,              'locale' => 'de_AT')));
    }

    /**
     * test toNumberFormat2
     * expected string
     */
    public function testToNumberFormat2()
    {
        $this->assertEquals((double) 1.7, (double) Zend_Locale_Format::toNumber(1.7, array('locale' => 'en')));
        $this->assertEquals((double) 2.3, (double) Zend_Locale_Format::toNumber(2.3, array('locale' => 'en')));
    }

    /**
     * test setOption
     * expected boolean
     */
    public function testSetOption()
    {
        $this->assertEquals(8, count(Zend_Locale_Format::setOptions(array('format_type' => 'php'))));
        $this->assertTrue(is_array(Zend_Locale_Format::setOptions()));

        try {
            $this->assertTrue(Zend_Locale_Format::setOptions(array('format_type' => 'xxx')));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }
        try {
            $this->assertTrue(Zend_Locale_Format::setOptions(array('myformat' => 'xxx')));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $format = Zend_Locale_Format::setOptions(array('locale' => 'de', 'number_format' => Zend_Locale_FORMAT::STANDARD));
        $test   = Zend_Locale_Data::getContent('de', 'decimalnumber');
        $this->assertEquals($test, $format['number_format']);

        try {
            $this->assertFalse(Zend_Locale_Format::setOptions(array('number_format' => array('x' => 'x'))));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $format = Zend_Locale_Format::setOptions(array('locale' => 'de', 'date_format' => Zend_Locale_Format::STANDARD));
        $test   = Zend_Locale_Format::getDateFormat('de');
        $this->assertEquals($test, $format['date_format']);

        try {
            $this->assertFalse(Zend_Locale_Format::setOptions(array('date_format' => array('x' => 'x'))));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }
        try {
            $this->assertFalse(is_array(Zend_Locale_Format::setOptions(array('fix_date' => 'no'))));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $format = Zend_Locale_Format::setOptions(array('locale' => Zend_Locale_Format::STANDARD));
        $locale = new Zend_Locale();
        $this->assertEquals($locale->toString(), $format['locale']);

        try {
            $this->assertFalse(is_array(Zend_Locale_Format::setOptions(array('locale' => 'nolocale'))));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }
        try {
            $this->assertFalse(is_array(Zend_Locale_Format::setOptions(array('precision' => 50))));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }
        // test interaction between class-wide default date format and using locale's default format
        try {
            $result = array('date_format' => 'MMM d, y', 'locale' => 'en_US', 'month' => '7',
                    'day' => '4', 'year' => '2007');
            Zend_Locale_Format::setOptions(array('format_type' => 'iso', 'date_format' => 'MMM d, y', 'locale' => 'en_US')); // test setUp
        } catch (Zend_Locale_Exception $e) {
            $this->fail("exception expected");
        }
        try {
            // uses global date_format with global locale
            $this->assertSame($result, Zend_Locale_Format::getDate('July 4, 2007'));
        } catch (Zend_Locale_Exception $e) {
            $this->fail("exception expected");
        }
        try {
            // uses global date_format with given locale
            $this->assertSame($result, Zend_Locale_Format::getDate('July 4, 2007', array('locale' => 'en_US')));
        } catch (Zend_Locale_Exception $e) {
            $this->fail("exception expected");
        }
        try {
            // sets a new global date format
            Zend_Locale_Format::setOptions(array('date_format' => 'M-d-y'));
        } catch (Zend_Locale_Exception $e) {
            $this->fail("exception expected");
        }
        try {
            // uses global date format with given locale
            // but this date format differs from the original set... (MMMM d, yyyy != M-d-y)
            $expected = Zend_Locale_Format::getDate('July 4, 2007', array('locale' => 'en_US'));
            $this->assertSame($expected['year'],  $result['year'] );
            $this->assertSame($expected['month'], $result['month']);
            $this->assertSame($expected['day'],   $result['day']  );
        } catch (Zend_Locale_Exception $e) {
            $this->fail("exception expected");
        }
        try {
            // the following should not be used... instead of null, Zend_Locale::ZFDEFAULT should be used
            // uses given local with standard format from this locale
            $this->assertSame($result,
                Zend_Locale_Format::getDate('July 4, 2007', array('locale' => 'en_US', 'date_format' => null)));
        } catch (Zend_Locale_Exception $e) {
            $this->fail("exception expected");
        }
        try {
            // uses given locale with standard format from this locale
            $this->assertSame($result,
                Zend_Locale_Format::getDate('July 4, 2007',
                    array('locale' => 'en_US', 'date_format' => Zend_Locale_Format::STANDARD)));
        } catch (Zend_Locale_Exception $e) {
            $this->fail("exception expected");
        }
        try {
            // uses standard locale with standard format from this locale
            $expect = Zend_Locale_Format::getDate('July 4, 2007', array('locale' => Zend_Locale_Format::STANDARD));
            $testlocale = new Zend_Locale();
            $this->assertEquals($testlocale->toString(), $expect['locale']);
        } catch (Zend_Locale_Exception $e) {
            $this->fail("exception expected");
        }
        Zend_Locale_Format::setOptions(array('date_format' => null, 'locale' => null)); // test tearDown
    }


    /**
     * test convertPhpToIso
     * expected boolean
     */
    public function testConvertPhpToIso()
    {
        $this->assertSame('dd',   Zend_Locale_Format::convertPhpToIsoFormat('d'));
        $this->assertSame('EE',   Zend_Locale_Format::convertPhpToIsoFormat('D'));
        $this->assertSame('d',    Zend_Locale_Format::convertPhpToIsoFormat('j'));
        $this->assertSame('EEEE', Zend_Locale_Format::convertPhpToIsoFormat('l'));
        $this->assertSame('eee',  Zend_Locale_Format::convertPhpToIsoFormat('N'));
        $this->assertSame('SS',   Zend_Locale_Format::convertPhpToIsoFormat('S'));
        $this->assertSame('e',    Zend_Locale_Format::convertPhpToIsoFormat('w'));
        $this->assertSame('D',    Zend_Locale_Format::convertPhpToIsoFormat('z'));
        $this->assertSame('ww',   Zend_Locale_Format::convertPhpToIsoFormat('W'));
        $this->assertSame('MMMM', Zend_Locale_Format::convertPhpToIsoFormat('F'));
        $this->assertSame('MM',   Zend_Locale_Format::convertPhpToIsoFormat('m'));
        $this->assertSame('MMM',  Zend_Locale_Format::convertPhpToIsoFormat('M'));
        $this->assertSame('M',    Zend_Locale_Format::convertPhpToIsoFormat('n'));
        $this->assertSame('ddd',  Zend_Locale_Format::convertPhpToIsoFormat('t'));
        $this->assertSame('l',    Zend_Locale_Format::convertPhpToIsoFormat('L'));
        $this->assertSame('YYYY', Zend_Locale_Format::convertPhpToIsoFormat('o'));
        $this->assertSame('yyyy', Zend_Locale_Format::convertPhpToIsoFormat('Y'));
        $this->assertSame('yy',   Zend_Locale_Format::convertPhpToIsoFormat('y'));
        $this->assertSame('a',    Zend_Locale_Format::convertPhpToIsoFormat('a'));
        $this->assertSame('a',    Zend_Locale_Format::convertPhpToIsoFormat('A'));
        $this->assertSame('B',    Zend_Locale_Format::convertPhpToIsoFormat('B'));
        $this->assertSame('h',    Zend_Locale_Format::convertPhpToIsoFormat('g'));
        $this->assertSame('H',    Zend_Locale_Format::convertPhpToIsoFormat('G'));
        $this->assertSame('hh',   Zend_Locale_Format::convertPhpToIsoFormat('h'));
        $this->assertSame('HH',   Zend_Locale_Format::convertPhpToIsoFormat('H'));
        $this->assertSame('mm',   Zend_Locale_Format::convertPhpToIsoFormat('i'));
        $this->assertSame('ss',   Zend_Locale_Format::convertPhpToIsoFormat('s'));
        $this->assertSame('zzzz', Zend_Locale_Format::convertPhpToIsoFormat('e'));
        $this->assertSame('I',    Zend_Locale_Format::convertPhpToIsoFormat('I'));
        $this->assertSame('Z',    Zend_Locale_Format::convertPhpToIsoFormat('O'));
        $this->assertSame('ZZZZ', Zend_Locale_Format::convertPhpToIsoFormat('P'));
        $this->assertSame('z',    Zend_Locale_Format::convertPhpToIsoFormat('T'));
        $this->assertSame('X',    Zend_Locale_Format::convertPhpToIsoFormat('Z'));
        $this->assertSame('yyyy-MM-ddTHH:mm:ssZZZZ', Zend_Locale_Format::convertPhpToIsoFormat('c'));
        $this->assertSame('r',    Zend_Locale_Format::convertPhpToIsoFormat('r'));
        $this->assertSame('U',    Zend_Locale_Format::convertPhpToIsoFormat('U'));
        $this->assertSame('HHmmss', Zend_Locale_Format::convertPhpToIsoFormat('His'));
    }


    /**
     * Test toFloat()/toNumber() when a different setlocale() is in effect,
     * where the locale does not use '.' as the decimal place separator.
     * expected string
     */
    public function testToFloatSetlocale()
    {
        setlocale(LC_ALL, 'fr_FR@euro'); // test setup

        //var_dump( setlocale(LC_NUMERIC, '0')); // this is the specific setting of interest
        $locale_fr = new Zend_Locale('fr_FR');
        $locale_en = new Zend_Locale('en_US');
        $params_fr = array('precision' => 2, 'locale' => $locale_fr);
        $params_en = array('precision' => 2, 'locale' => $locale_en);
        $myFloat = 1234.5;
        $test1 = Zend_Locale_Format::toFloat($myFloat, $params_fr);
        $test2 = Zend_Locale_Format::toFloat($myFloat, $params_en);
        $this->assertEquals("1 234,50", $test1);
        $this->assertEquals("1,234.50", $test2);
        // placing tearDown here (i.e. restoring locale) won't work, if test already failed/aborted above.
    }

    /**
     * ZF-3473
     */
    public function testRoundingOfNear10Values()
    {
        $this->assertEquals(9.72, Zend_Locale_Format::getNumber(9.72));
        $this->assertEquals(-9.72, Zend_Locale_Format::getNumber(-9.72));
        $this->assertEquals(9.72, Zend_Locale_Format::getNumber(9.72, array('locale' => 'de')));
        $this->assertEquals(-9.72, Zend_Locale_Format::getNumber('-9,72', array('locale' => 'de')));
        $this->assertEquals('9,72',   Zend_Locale_Format::toNumber(9.72, array('locale' => 'de')));
        $this->assertEquals('-9,72',   Zend_Locale_Format::toNumber(-9.72, array('locale' => 'de')));
        $this->assertTrue(Zend_Locale_Format::isNumber('9,72', array('locale' => 'de')));
        $this->assertTrue(Zend_Locale_Format::isNumber('-9,72', array('locale' => 'de')));
        $this->assertEquals(9.72,   Zend_Locale_Format::getFloat(9.72));
        $this->assertEquals('14,23',   Zend_Locale_Format::toNumber(14.2278, array('precision' => 2, 'locale' => 'pt_PT')));
    }

    /**
     * Tests getDateTime
     */
    public function testgetDateTime()
    {
        try {
            $value = Zend_Locale_Format::getDateTime('no content');
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $this->assertTrue(is_array(Zend_Locale_Format::getDateTime('10.11.2006 13:14:55', array('date_format' => 'dd.MM.yyyy HH:mm:ss'))));
        $options = array('date_format' => 'dd.MM.yy h:mm:ss a', 'locale' => 'en');
        $this->assertTrue(is_array(Zend_Locale_Format::getDateTime('15.10.09 11:14:55 am', $options)));
        $this->assertTrue(is_array(Zend_Locale_Format::getDateTime('15.10.09 12:14:55 am', $options)));
        $this->assertTrue(is_array(Zend_Locale_Format::getDateTime('15.10.09 11:14:55 pm', $options)));
        $this->assertTrue(is_array(Zend_Locale_Format::getDateTime('15.10.09 12:14:55 pm', $options)));

        try {
            $value = Zend_Locale_Format::getDateTime('15.10.09 13:14:55', array('date_format' => 'nocontent'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        try {
            $value = Zend_Locale_Format::getDateTime('15.10.09 13:14:55', array('date_format' => 'ZZZZ'));
            $this->fail("exception expected");
        } catch (Zend_Locale_Exception $e) {
            // success
        }

        $value = Zend_Locale_Format::getDateTime('15.10.09 13:14:55', array('date_format' => 'dd.MM.yy HH:mm:ss.x'));
        $this->assertEquals(15, $value['day']  );
        $this->assertEquals(10, $value['month']);
        $this->assertEquals(2009, $value['year']);
        $this->assertEquals(13, $value['hour']  );
        $this->assertEquals(14, $value['minute']);
        $this->assertEquals(55, $value['second']);

        $this->assertEquals(8, count(Zend_Locale_Format::getDateTime('15.10.09 13:14:55', array('date_format' => 'dd.MM.yy HH:mm:ss'))));

        $value = Zend_Locale_Format::getDateTime('15.10.09 13:14:55', array('date_format' => 'dd.MM.yy HH:mm:ss'));
        $this->assertEquals(15, $value['day']  );
        $this->assertEquals(10, $value['month']);
        $this->assertEquals(2009, $value['year']);
        $this->assertEquals(13, $value['hour']  );
        $this->assertEquals(14, $value['minute']);
        $this->assertEquals(55, $value['second']);

        $value = Zend_Locale_Format::getDateTime('151009131455', array('date_format' => 'dd.MM.yy HH:mm:ss'));
        $this->assertEquals(15, $value['day']  );
        $this->assertEquals(10, $value['month']);
        $this->assertEquals(2009, $value['year']);
        $this->assertEquals(13, $value['hour']  );
        $this->assertEquals(14, $value['minute']);
        $this->assertEquals(55, $value['second']);
    }

    /**
     * Tests conversion from scientific numbers to decimal notation
     */
    public function testScientificNumbers()
    {
        $this->assertEquals('0,0', Zend_Locale_Format::toNumber(  1E-2, array('precision' => 1, 'locale' => 'de_AT')));
        $this->assertEquals('0,0100', Zend_Locale_Format::toNumber(  1E-2, array('precision' => 4, 'locale' => 'de_AT')));
        $this->assertEquals('100,0', Zend_Locale_Format::toNumber(  1E+2, array('precision' => 1, 'locale' => 'de_AT')));
        $this->assertEquals('100,0000', Zend_Locale_Format::toNumber(  1E+2, array('precision' => 4, 'locale' => 'de_AT')));
        $this->assertEquals('0', Zend_Locale_Format::toNumber(  1E-5, array('precision' => 0, 'locale' => 'de_AT')));
        $this->assertEquals('0,00001', Zend_Locale_Format::toNumber(  1.3E-5, array('precision' => 5, 'locale' => 'de_AT')));
        $this->assertEquals('0,000013', Zend_Locale_Format::toNumber(  1.3E-5, array('precision' => 6, 'locale' => 'de_AT')));
    }

    public function testShortNotation()
    {
        $this->assertEquals(.12345, Zend_Locale_Format::getNumber(.12345));
        $options = array('locale' => 'de');
        $this->assertEquals(.12345, Zend_Locale_Format::getNumber(',12345', $options));
        $options = array('locale' => 'de_AT');
        $this->assertEquals(.12345, Zend_Locale_Format::getNumber(',12345', $options));

        $this->assertEquals('0,75', Zend_Locale_Format::toNumber(.75, array('locale' => 'de_DE', 'precision' => 2)));

        $this->assertTrue(Zend_Locale_Format::isNumber(',12345',  array('locale' => 'de_AT')));

        $this->assertEquals(.12345, Zend_Locale_Format::getFloat(.12345));
        $options = array('locale' => 'de');
        $this->assertEquals(.12345, Zend_Locale_Format::getFloat(',12345', $options));
        $options = array('locale' => 'de_AT');
        $this->assertEquals(.12345, Zend_Locale_Format::getFloat(',12345', $options));

        $options = array('locale' => 'de_AT');
        $this->assertEquals('0,12345', Zend_Locale_Format::toFloat(.12345, $options));
        $options = array('locale' => 'ar_QA');
        $this->assertEquals('0,12345',  Zend_Locale_Format::toFloat(.12345, $options));

        $this->assertTrue(Zend_Locale_Format::isFloat(',12345',  array('locale' => 'de_AT')));

        $this->assertEquals(0, Zend_Locale_Format::getInteger(.1234567));
        $options = array('locale' => 'de');
        $this->assertEquals(0, Zend_Locale_Format::getInteger(',12345', $options));
        $options = array('locale' => 'de_AT');
        $this->assertEquals(0, Zend_Locale_Format::getInteger(',12345', $options));

        $this->assertEquals('0', Zend_Locale_Format::toInteger(.123, array('locale' => 'de')));
        $options = array('locale' => 'de_AT');
        $this->assertEquals('0',  Zend_Locale_Format::toInteger(.12345, $options));

        $this->assertFalse(Zend_Locale_Format::isInteger(',12345', array('locale' => 'de_AT')));

        $options = array('locale' => 'de_AT');
        $this->assertEquals('0,567', Zend_Locale_Format::toNumber(.567, $options));
    }

    /**
     * @group ZF-9160
     */
    public function testGetNumberWithZeroPrecision()
    {
        $this->assertEquals(1234, Zend_Locale_Format::getNumber('1234.567', array('locale' => 'en_US', 'precision' => 0)));
    }
}
