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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\Date as DateValidator;
use Zend\Date;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class DateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DateValidator
     */
    protected $validator;

    /**
     * Whether an error occurred
     *
     * @var boolean
     */
    protected $errorOccurred = false;

    public function setUp()
    {
        $this->errorOccurred = false;
        $this->validator     = new DateValidator();
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
            $this->assertEquals($result, $this->validator->isValid($input),
                                "'$input' expected to be " . ($result ? '' : 'in') . 'valid');
        }
    }

    /**
     * Ensures that characters trailing an otherwise valid date cause the input to be invalid
     *
     * @group  ZF-1804
     * @return void
     */
    public function testCharactersTrailingInvalid()
    {
        $dateValid = '2007-08-02';
        $charactersTrailing = 'something';
        $this->assertTrue($this->validator->isValid($dateValid));
        $this->assertFalse($this->validator->isValid($dateValid . $charactersTrailing));
    }

    /**
     * Ensures that characters leading an otherwise valid date cause the input to be invalid
     *
     * @group  ZF-1804
     * @return void
     */
    public function testCharactersLeadingInvalid()
    {
        $dateValid = '2007-08-02';
        $charactersLeading = 'something';
        $this->assertTrue($this->validator->isValid($dateValid));
        $this->assertFalse($this->validator->isValid($charactersLeading . $dateValid));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * Ensures that the validator can handle different manual dateformats
     *
     * @group  ZF-2003
     * @return void
     */
    public function testUseManualFormat()
    {
        $this->assertTrue($this->validator->setFormat('dd.MM.YYYY')->isValid('10.01.2008'));
        $this->assertEquals('dd.MM.YYYY', $this->validator->getFormat());

        $this->assertTrue($this->validator->setFormat('MM yyyy')->isValid('01 2010'));
        $this->assertFalse($this->validator->setFormat('dd/MM/yyyy')->isValid('2008/10/22'));
        $this->assertTrue($this->validator->setFormat('dd/MM/yy')->isValid('22/10/08'));
        $this->assertFalse($this->validator->setFormat('dd/MM/yy')->isValid('22/10'));
        $this->assertFalse($this->validator->setFormat('s')->isValid(0));
    }

    /**
     * Ensures that the validator can handle different dateformats from locale
     *
     * @group  ZF-2003
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
            $resultActual = $this->validator->setLocale('de_AT')->isValid($input);
            if (!$this->errorOccurred) {
                $this->assertEquals($resultExpected, $resultActual, "'$input' expected to be "
                    . ($resultExpected ? '' : 'in') . 'valid');
            } else {
                $errorOccurredLocal = true;
            }
            $this->errorOccurred = false;
        }
        $this->assertEquals('de_AT', $this->validator->getLocale());
        restore_error_handler();
        if ($errorOccurredLocal) {
            $this->markTestSkipped('Affected by bug described in ZF-2789');
        }
    }

    /**
     * Ensures that the validator can handle different dateformats from locale
     *
     * @group  ZF-2003
     * @return void
     */
    public function testLocaleContructor()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $valid = new DateValidator('dd.MM.YYYY', 'de');
        $this->assertTrue($valid->isValid('10.April.2008'));

        restore_error_handler();
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-6374
     */
    public function testUsingApplicationLocale()
    {
        $this->markTestSkipped('Depends on system-specific locale');
        $valid = new DateValidator();
        $this->assertTrue($valid->isValid('10.April.2008'));
    }

    /**
     * @group ZF-7630
     */
    public function testDateObjectVerification()
    {
        $date = new Date\Date();
        $this->assertTrue($this->validator->isValid($date), "'$date' expected to be valid");
    }

    /**
     * ZF-6457
     */
    public function testArrayVerification()
    {
        $date  = new Date\Date();
        $array = $date->toArray();
        $this->assertTrue($this->validator->isValid($array), "array expected to be valid");
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
     * @group  ZF-2789
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $this->errorOccurred = true;
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageVariables'),
                                     'messageVariables', $validator);
    }
}
