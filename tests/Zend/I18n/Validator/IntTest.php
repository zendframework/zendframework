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

namespace ZendTest\I18n\Validator;

use Zend\I18n\Validator\Int as IntValidator;
use Locale;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class IntTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Int
     */
    protected $validator;

    public function setUp()
    {
        $this->locale    = Locale::getDefault();
        $this->validator = new IntValidator();
    }

    public function tearDown()
    {
        Locale::setDefault($this->locale);
    }

    public function intDataProvider()
    {
        return array(
            array(1.00,         true),
            array(0.00,         true),
            array(0.01,         false),
            array(-0.1,         false),
            array(-1,           true),
            array('10',         true),
            array(1,            true),
            array('not an int', false),
            array(true,         false),
            array(false,        false),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider intDataProvider()
     * @return void
     */
    public function testBasic($intVal, $expected)
    {
        $this->validator->setLocale('en');
        $this->assertEquals($expected, $this->validator->isValid($intVal));
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
     * Ensures that set/getLocale() works
     */
    public function testSettingLocales()
    {
        $this->validator->setLocale('de');
        $this->assertEquals('de', $this->validator->getLocale());
        $this->assertEquals(false, $this->validator->isValid('10 000'));
        $this->assertEquals(true, $this->validator->isValid('10.000'));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-7489
     */
    public function testUsingApplicationLocale()
    {
        Locale::setDefault('de');
        $valid = new IntValidator();
        $this->assertTrue($valid->isValid('10.000'));
    }

    /**
     * @ZF-7703
     */
    public function testLocaleDetectsNoEnglishLocaleOnOtherSetLocale()
    {
        Locale::setDefault('de');
        $valid = new IntValidator();
        $this->assertTrue($valid->isValid(1200));
        $this->assertFalse($valid->isValid('1,200'));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }
}
