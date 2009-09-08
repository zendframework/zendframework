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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Validate_Date
 */
require_once 'Zend/Validate/Date.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_DateTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validate_Date object
     *
     * @var Zend_Validate_Date
     */
    protected $_validator;

    /**
     * Whether an error occurred
     *
     * @var boolean
     */
    protected $_errorOccurred = false;

    /**
     * Creates a new Zend_Validate_Date object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate_Date();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            '2007-01-01' => true,
            '2007-02-28' => true,
            '2007-02-29' => false,
            '2008-02-29' => true,
            '2007-02-30' => false,
            '2007-02-99' => false,
            '9999-99-99' => false,
            0            => false,
            999999999999 => false,
            'Jan 1 2007' => false,
            'asdasda'    => false,
            'sdgsdg'     => false
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->_validator->isValid($input),
                                "'$input' expected to be " . ($result ? '' : 'in') . 'valid');
        }
    }

    /**
     * Ensures that characters trailing an otherwise valid date cause the input to be invalid
     *
     * @see    http://framework.zend.com/issues/browse/ZF-1804
     * @return void
     */
    public function testCharactersTrailingInvalid()
    {
        $dateValid = '2007-08-02';
        $charactersTrailing = 'something';
        $this->assertTrue($this->_validator->isValid($dateValid));
        $this->assertFalse($this->_validator->isValid($dateValid . $charactersTrailing));
    }

    /**
     * Ensures that characters leading an otherwise valid date cause the input to be invalid
     *
     * @see    http://framework.zend.com/issues/browse/ZF-1804
     * @return void
     */
    public function testCharactersLeadingInvalid()
    {
        $dateValid = '2007-08-02';
        $charactersLeading = 'something';
        $this->assertTrue($this->_validator->isValid($dateValid));
        $this->assertFalse($this->_validator->isValid($charactersLeading . $dateValid));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * Ensures that the validator can handle different manual dateformats
     *
     * @see    http://framework.zend.com/issues/browse/ZF-2003
     * @return void
     */
    public function testUseManualFormat()
    {
        $this->assertTrue($this->_validator->setFormat('dd.MM.YYYY')->isValid('10.01.2008'));
        $this->assertEquals('dd.MM.YYYY', $this->_validator->getFormat());

        $this->assertTrue($this->_validator->setFormat('MM yyyy')->isValid('01 2010'));
        $this->assertFalse($this->_validator->setFormat('dd/MM/yyyy')->isValid('2008/10/22'));
        $this->assertTrue($this->_validator->setFormat('dd/MM/yy')->isValid('22/10/08'));
        $this->assertFalse($this->_validator->setFormat('dd/MM/yy')->isValid('22/10'));
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $result = $this->_validator->setFormat('s')->isValid(0);
        restore_error_handler();
        if (!$this->_errorOccurred) {
            $this->assertTrue($result);
        } else {
            $this->markTestSkipped('Affected by bug described in ZF-2789');
        }
        $this->_errorOccurred = false;
    }

    /**
     * Ensures that the validator can handle different dateformats from locale
     *
     * @see    http://framework.zend.com/issues/browse/ZF-2003
     * @return void
     */
    public function testUseLocaleFormat()
    {
        $errorOccurredLocal = false;
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $valuesExpected = array(
            '10.01.2008' => true,
            '32.02.2008' => false,
            '20 April 2008' => true,
            '1 Jul 2008' => true,
            '2008/20/03' => false,
            '99/99/2000' => false,
            0            => false,
            999999999999 => false,
            'Jan 1 2007' => false
            );
        foreach ($valuesExpected as $input => $resultExpected) {
            $resultActual = $this->_validator->setLocale('de_AT')->isValid($input);
            if (!$this->_errorOccurred) {
                $this->assertEquals($resultExpected, $resultActual, "'$input' expected to be "
                    . ($resultExpected ? '' : 'in') . 'valid');
            } else {
                $errorOccurredLocal = true;
            }
            $this->_errorOccurred = false;
        }
        $this->assertEquals('de_AT', $this->_validator->getLocale());
        restore_error_handler();
        if ($errorOccurredLocal) {
            $this->markTestSkipped('Affected by bug described in ZF-2789');
        }
    }

    /**
     * Ensures that the validator can handle different dateformats from locale
     *
     * @see    http://framework.zend.com/issues/browse/ZF-2003
     * @return void
     */
    public function testLocaleContructor()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $valid = new Zend_Validate_Date('dd.MM.YYYY', 'de');
        $this->assertTrue($valid->isValid('10.April.2008'));

        restore_error_handler();
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-6374
     */
    public function testUsingApplicationLocale()
    {
        Zend_Registry::set('Zend_Locale', new Zend_Locale('de'));
        $valid = new Zend_Validate_Date();
        $this->assertTrue($valid->isValid('10.April.2008'));
    }

    /**
     * ZF-7630
     */
    public function testDateObjectVerification()
    {
        $date = new Zend_Date();
        $this->assertTrue($this->_validator->isValid($date), "'$date' expected to be valid");
    }

    /**
     * ZF-6457
     */
    public function testArrayVerification()
    {
        $date  = new Zend_Date();
        $array = $date->toArray();
        $this->assertTrue($this->_validator->isValid($array), "array expected to be valid");
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred
     *
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  integer $errline
     * @param  array   $errcontext
     * @return void
     * @see    http://framework.zend.com/issues/browse/ZF-2789
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $this->_errorOccurred = true;
    }
}
