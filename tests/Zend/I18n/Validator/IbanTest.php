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

use Zend\I18n\Validator\Iban as IbanValidator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class IbanTest extends \PHPUnit_Framework_TestCase
{
    public function ibanDataProvider()
    {
        return array(
            array('AD1200012030200359100100', true),
            array('AT611904300234573201',     true),
            array('AT61 1904 3002 3457 3201', false),
            array('AD1200012030200354100100', false),
        );
    }
    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider ibanDataProvider
     * @return void
     */
    public function testBasic($iban, $expected)
    {
        $validator = new IbanValidator();
        $this->assertEquals($expected, $validator->isValid($iban));
    }

    public function testSettingAndGettingLocale()
    {
        $validator = new IbanValidator();

        $validator->setLocale('de_DE');
        $this->assertEquals('de_DE', $validator->getLocale());

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Locale must contain a region');
        $validator->setLocale('foobar')->isValid('AD1200012030200354100100');
    }

    public function testInstanceWithLocale()
    {
        $validator = new IbanValidator(array('locale' => 'de_AT'));
        $this->assertTrue($validator->isValid('AT611904300234573201'));
    }

    public function testIbanNotSupported()
    {
        $validator = new IbanValidator(array('locale' => 'en_US'));
        $this->assertFalse($validator->isValid('AT611904300234573201'));
    }

    /**
     * @group ZF-10556
     */
    public function testIbanDetectionWithoutLocale()
    {
        $validator = new IbanValidator();
        $this->assertTrue($validator->isValid('AT611904300234573201'));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new IbanValidator();
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }
}
