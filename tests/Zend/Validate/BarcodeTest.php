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

/** Zend_Validate_Barcode */
require_once 'Zend/Validate/Barcode.php';

/**
 * Zend_Validate_Barcode
 *
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @uses       Zend_Validate_Barcode
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_BarcodeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test if EAN-13 contains only numeric characters
     *
     * @group ZF-3297
     */
    public function testEan13ContainsOnlyNumeric()
    {
        $barcode = new Zend_Validate_Barcode('ean13');
        $this->assertFalse($barcode->isValid('3RH1131-1BB40'));
    }

    public function testNoneExisting()
    {
        try {
            $barcode = new Zend_Validate_Barcode('Zend');
            $this->fail("'Zend' is not a valid barcode type'");
        } catch (Exception $e) {
            $this->assertContains("No such file", $e->getMessage());
        }
    }

    public function testSetAdapter()
    {
        $barcode = new Zend_Validate_Barcode('upca');
        $this->assertTrue($barcode->isValid('065100004327'));

        $barcode->setAdapter('ean13');
        $this->assertTrue($barcode->isValid('0075678164125'));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $barcode = new Zend_Validate_Barcode('upca');
        $this->assertFalse($barcode->isValid(106510000.4327));
        $this->assertFalse($barcode->isValid(array('065100004327')));

        $barcode = new Zend_Validate_Barcode('ean13');
        $this->assertFalse($barcode->isValid(06510000.4327));
        $this->assertFalse($barcode->isValid(array('065100004327')));
    }

    public function testInvalidChecksumAdapter()
    {
        require_once dirname(__FILE__) . "/_files/MyBarcode1.php";
        $barcode = new Zend_Validate_Barcode('MyBarcode1');
        $this->assertFalse($barcode->isValid('0000000'));
        $this->assertTrue(array_key_exists('barcodeFailed', $barcode->getMessages()));
        $this->assertFalse($barcode->getAdapter()->checksum('0000000'));
    }

    public function testInvalidCharAdapter()
    {
        require_once dirname(__FILE__) . "/_files/MyBarcode1.php";
        $barcode = new Zend_Validate_Barcode('MyBarcode1');
        $this->assertFalse($barcode->getAdapter()->checkChars(123));
    }

    public function testAscii128CharacterAdapter()
    {
        require_once dirname(__FILE__) . "/_files/MyBarcode2.php";
        $barcode = new Zend_Validate_Barcode('MyBarcode2');
        $this->assertTrue($barcode->getAdapter()->checkChars('1234QW!"'));
    }

    public function testInvalidLengthAdapter()
    {
        require_once dirname(__FILE__) . "/_files/MyBarcode2.php";
        $barcode = new Zend_Validate_Barcode('MyBarcode2');
        $this->assertFalse($barcode->getAdapter()->checkLength(123));
    }

    public function testArrayLengthAdapter()
    {
        require_once dirname(__FILE__) . "/_files/MyBarcode2.php";
        $barcode = new Zend_Validate_Barcode('MyBarcode2');
        $this->assertTrue($barcode->getAdapter()->checkLength('1'));
        $this->assertFalse($barcode->getAdapter()->checkLength('12'));
        $this->assertTrue($barcode->getAdapter()->checkLength('123'));
        $this->assertFalse($barcode->getAdapter()->checkLength('1234'));
    }

    public function testArrayLengthAdapter2()
    {
        require_once dirname(__FILE__) . "/_files/MyBarcode3.php";
        $barcode = new Zend_Validate_Barcode('MyBarcode3');
        $this->assertTrue($barcode->getAdapter()->checkLength('1'));
        $this->assertTrue($barcode->getAdapter()->checkLength('12'));
        $this->assertTrue($barcode->getAdapter()->checkLength('123'));
        $this->assertTrue($barcode->getAdapter()->checkLength('1234'));
    }

    public function testOddLengthAdapter()
    {
        require_once dirname(__FILE__) . "/_files/MyBarcode4.php";
        $barcode = new Zend_Validate_Barcode('MyBarcode4');
        $this->assertTrue($barcode->getAdapter()->checkLength('1'));
        $this->assertFalse($barcode->getAdapter()->checkLength('12'));
        $this->assertTrue($barcode->getAdapter()->checkLength('123'));
        $this->assertFalse($barcode->getAdapter()->checkLength('1234'));
    }

    public function testInvalidAdapter()
    {
        $barcode = new Zend_Validate_Barcode('Ean13');
        try {
            require_once dirname(__FILE__) . "/_files/MyBarcode5.php";
            $barcode->setAdapter('MyBarcode5');
            $this->fails('Exception expected');
        } catch (Exception $e) {
            $this->assertContains('does not implement', $e->getMessage());
        }
    }

    public function testArrayConstructAdapter()
    {
        $barcode = new Zend_Validate_Barcode(array('adapter' => 'Ean13', 'options' => 'unknown', 'checksum' => false));
        $this->assertTrue($barcode->getAdapter() instanceof Zend_Validate_Barcode_Ean13);
        $this->assertFalse($barcode->getChecksum());
    }

    public function testInvalidArrayConstructAdapter()
    {
        try {
            $barcode = new Zend_Validate_Barcode(array('options' => 'unknown', 'checksum' => false));
            $this->fails('Exception expected');
        } catch (Exception $e) {
            $this->assertContains('Missing option', $e->getMessage());
        }
    }

    public function testConfigConstructAdapter()
    {
        $array = array('adapter' => 'Ean13', 'options' => 'unknown', 'checksum' => false);
        require_once 'Zend/Config.php';
        $config = new Zend_Config($array);

        $barcode = new Zend_Validate_Barcode($config);
        $this->assertTrue($barcode->isValid('0075678164125'));
    }

    public function testxxxxxxxxxxxxCODE25()
    {
        $barcode = new Zend_Validate_Barcode('code25');
        $this->assertTrue($barcode->isValid('00075678164125'));
        $this->assertFalse($barcode->isValid('123'));
        $barcode->setChecksum(true);
        $this->assertFalse($barcode->isValid('00075678164124'));
    }

    public function testCODE39()
    {
        $barcode = new Zend_Validate_Barcode('code39');
        $this->assertTrue($barcode->isValid('TEST93TEST93TEST93TEST93Y+'));
        $this->assertTrue($barcode->isValid('00075678164124'));
        $this->assertFalse($barcode->isValid('Test93Test93Test'));

        $barcode->setChecksum(true);
        $this->assertTrue($barcode->isValid('159AZH'));
        $this->assertFalse($barcode->isValid('159AZG'));
    }

    public function testCODE39EXT()
    {
        $barcode = new Zend_Validate_Barcode('code39ext');
        $this->assertTrue($barcode->isValid('TEST93TEST93TEST93TEST93Y+'));
        $this->assertTrue($barcode->isValid('00075678164124'));
        $this->assertTrue($barcode->isValid('Test93Test93Test'));

// @TODO: CODE39 EXTENDED CHECKSUM VALIDATION MISSING
//        $barcode->setChecksum(true);
//        $this->assertTrue($barcode->isValid('159AZH'));
//        $this->assertFalse($barcode->isValid('159AZG'));
    }

    public function testxxxxxxxxxxxxCODE93()
    {
        $barcode = new Zend_Validate_Barcode('code93');
        $this->assertTrue($barcode->isValid('TEST93TEST93TEST93TEST93Y+'));
        $this->assertFalse($barcode->isValid('00075678164124'));
    }

    public function testEAN8()
    {
        $barcode = new Zend_Validate_Barcode('ean8');
        $this->assertTrue($barcode->isValid('12345670'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('12345671'));
    }

    public function testEAN12()
    {
        $barcode = new Zend_Validate_Barcode('ean12');
        $this->assertTrue($barcode->isValid('123456789012'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789013'));
    }

    public function testEAN13()
    {
        $barcode = new Zend_Validate_Barcode('ean13');
        $this->assertTrue($barcode->isValid('1234567890128'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('1234567890127'));
    }

    public function testEAN14()
    {
        $barcode = new Zend_Validate_Barcode('ean14');
        $this->assertTrue($barcode->isValid('12345678901231'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('12345678901232'));
    }

    public function testEAN18()
    {
        $barcode = new Zend_Validate_Barcode('ean18');
        $this->assertTrue($barcode->isValid('123456789012345675'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789012345676'));
    }

    public function testGTIN12()
    {
        $barcode = new Zend_Validate_Barcode('gtin12');
        $this->assertTrue($barcode->isValid('123456789012'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789013'));
    }

    public function testGTIN13()
    {
        $barcode = new Zend_Validate_Barcode('gtin13');
        $this->assertTrue($barcode->isValid('1234567890128'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('1234567890127'));
    }

    public function testGTIN14()
    {
        $barcode = new Zend_Validate_Barcode('gtin14');
        $this->assertTrue($barcode->isValid('12345678901231'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('12345678901232'));
    }

    public function testxxxxxxxxxxxxIDENTCODE()
    {
        $barcode = new Zend_Validate_Barcode('identcode');
        $this->assertTrue($barcode->isValid('564000000050'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('0563102430313'));
        $this->assertFalse($barcode->isValid('563102430312'));
    }

    public function testxxxxxxxxxxxxITF14()
    {
        $barcode = new Zend_Validate_Barcode('itf14');
        $this->assertTrue($barcode->isValid('00075678164125'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('00075678164124'));
    }

    public function testxxxxxxxxxxxxLEITCODE()
    {
        $barcode = new Zend_Validate_Barcode('leitcode');
        $this->assertTrue($barcode->isValid('21348075016401'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('021348075016401'));
        $this->assertFalse($barcode->isValid('21348075016402'));
    }

    public function testSSCC()
    {
        $barcode = new Zend_Validate_Barcode('sscc');
        $this->assertTrue($barcode->isValid('123456789012345675'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789012345676'));
    }

    public function testUPCA()
    {
        $barcode = new Zend_Validate_Barcode('upca');
        $this->assertTrue($barcode->isValid('123456789012'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789013'));
    }

    public function testUPCE()
    {
        $barcode = new Zend_Validate_Barcode('upce');
        $this->assertTrue($barcode->isValid('02345673'));
        $this->assertFalse($barcode->isValid('02345672'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertTrue($barcode->isValid('123456'));
        $this->assertTrue($barcode->isValid('0234567'));
    }
}
