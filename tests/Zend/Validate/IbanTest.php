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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */

/**
 * @see Zend_Validate_Iban
 */

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_IbanTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $validator = new Zend_Validate_Iban();
        $valuesExpected = array(
            'AD1200012030200359100100' => true,
            'AT611904300234573201'     => true,
            'AT61 1904 3002 3457 3201' => false,
            'AD1200012030200354100100' => false,
        );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $validator->isValid($input),
                                "'$input' expected to be " . ($result ? '' : 'in') . 'valid');
        }
    }

    public function testSettingAndGettingLocale()
    {
        $validator = new Zend_Validate_Iban();
        try {
            $validator->setLocale('de_QA');
            $this->fail();
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains('IBAN validation', $e->getMessage());
        }

        $validator->setLocale('de_DE');
        $this->assertEquals('de_DE', $validator->getLocale());
    }

    public function testInstanceWithLocale()
    {
        $validator = new Zend_Validate_Iban('de_AT');
        $this->assertTrue($validator->isValid('AT611904300234573201'));
    }

    public function testIbanNotSupported()
    {
        $validator = new Zend_Validate_Iban('en_US');
        $this->assertFalse($validator->isValid('AT611904300234573201'));
    }
}
