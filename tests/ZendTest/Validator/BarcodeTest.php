<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator;

use Zend\Validator\Barcode;

/**
 * \Zend\Barcode
 *
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class BarcodeTest extends \PHPUnit_Framework_TestCase
{
    public function testNoneExisting()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'not found');
        $barcode = new Barcode('\Zend\Validate\BarcodeTest\NonExistentClassName');
    }

    public function testSetAdapter()
    {
        $barcode = new Barcode('upca');
        $this->assertTrue($barcode->isValid('065100004327'));

        $barcode->setAdapter('ean13');
        $this->assertTrue($barcode->isValid('0075678164125'));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $barcode = new Barcode('upca');
        $this->assertFalse($barcode->isValid(106510000.4327));
        $this->assertFalse($barcode->isValid(array('065100004327')));

        $barcode = new Barcode('ean13');
        $this->assertFalse($barcode->isValid(06510000.4327));
        $this->assertFalse($barcode->isValid(array('065100004327')));
    }

    public function testInvalidChecksumAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode1.php";
        $barcode = new Barcode('MyBarcode1');
        $this->assertFalse($barcode->isValid('0000000'));
        $this->assertTrue(array_key_exists('barcodeFailed', $barcode->getMessages()));
        $this->assertFalse($barcode->getAdapter()->hasValidChecksum('0000000'));
    }

    public function testInvalidCharAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode1.php";
        $barcode = new Barcode('MyBarcode1');
        $this->assertFalse($barcode->getAdapter()->hasValidCharacters(123));
    }

    public function testAscii128CharacterAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode2.php";
        $barcode = new Barcode('MyBarcode2');
        $this->assertTrue($barcode->getAdapter()->hasValidCharacters('1234QW!"'));
    }

    public function testInvalidLengthAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode2.php";
        $barcode = new Barcode('MyBarcode2');
        $this->assertFalse($barcode->getAdapter()->hasValidLength(123));
    }

    public function testArrayLengthAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode2.php";
        $barcode = new Barcode('MyBarcode2');
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('12'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('123'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('1234'));
    }

    public function testArrayLengthAdapter2()
    {
        require_once __DIR__ . "/_files/MyBarcode3.php";
        $barcode = new Barcode('MyBarcode3');
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('12'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('123'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1234'));
    }

    public function testOddLengthAdapter()
    {
        require_once __DIR__ . "/_files/MyBarcode4.php";
        $barcode = new Barcode('MyBarcode4');
        $this->assertTrue($barcode->getAdapter()->hasValidLength('1'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('12'));
        $this->assertTrue($barcode->getAdapter()->hasValidLength('123'));
        $this->assertFalse($barcode->getAdapter()->hasValidLength('1234'));
    }

    public function testInvalidAdapter()
    {
        $barcode = new Barcode('Ean13');

        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'does not implement');
        require_once __DIR__ . "/_files/MyBarcode5.php";
        $barcode->setAdapter('MyBarcode5');
    }

    public function testArrayConstructAdapter()
    {
        $barcode = new Barcode(array('adapter' => 'Ean13', 'options' => 'unknown', 'useChecksum' => false));
        $this->assertTrue($barcode->getAdapter() instanceof Barcode\Ean13);
        $this->assertFalse($barcode->useChecksum());
    }

    public function testDefaultArrayConstructWithMissingAdapter()
    {
        $barcode = new Barcode(array('options' => 'unknown', 'checksum' => false));
        $this->assertTrue($barcode->isValid('0075678164125'));
    }

    public function testConfigConstructAdapter()
    {
        $array = array('adapter' => 'Ean13', 'options' => 'unknown', 'useChecksum' => false);
        $config = new \Zend\Config\Config($array);

        $barcode = new Barcode($config);
        $this->assertTrue($barcode->isValid('0075678164125'));
    }

    public function testCODE25()
    {
        $barcode = new Barcode('code25');
        $this->assertTrue($barcode->isValid('0123456789101213'));
        $this->assertTrue($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123a'));

        $barcode->useChecksum(true);
        $this->assertTrue($barcode->isValid('0123456789101214'));
        $this->assertFalse($barcode->isValid('0123456789101213'));
    }

    public function testCODE25INTERLEAVED()
    {
        $barcode = new Barcode('code25interleaved');
        $this->assertTrue($barcode->isValid('0123456789101213'));
        $this->assertFalse($barcode->isValid('123'));

        $barcode->useChecksum(true);
        $this->assertTrue($barcode->isValid('0123456789101214'));
        $this->assertFalse($barcode->isValid('0123456789101213'));
    }

    public function testCODE39()
    {
        $barcode = new Barcode('code39');
        $this->assertTrue($barcode->isValid('TEST93TEST93TEST93TEST93Y+'));
        $this->assertTrue($barcode->isValid('00075678164124'));
        $this->assertFalse($barcode->isValid('Test93Test93Test'));

        $barcode->useChecksum(true);
        $this->assertTrue($barcode->isValid('159AZH'));
        $this->assertFalse($barcode->isValid('159AZG'));
    }

    public function testCODE39EXT()
    {
        $barcode = new Barcode('code39ext');
        $this->assertTrue($barcode->isValid('TEST93TEST93TEST93TEST93Y+'));
        $this->assertTrue($barcode->isValid('00075678164124'));
        $this->assertTrue($barcode->isValid('Test93Test93Test'));

// @TODO: CODE39 EXTENDED CHECKSUM VALIDATION MISSING
//        $barcode->useChecksum(true);
//        $this->assertTrue($barcode->isValid('159AZH'));
//        $this->assertFalse($barcode->isValid('159AZG'));
    }

    public function testCODE93()
    {
        $barcode = new Barcode('code93');
        $this->assertTrue($barcode->isValid('TEST93+'));
        $this->assertFalse($barcode->isValid('Test93+'));

        $barcode->useChecksum(true);
        $this->assertTrue($barcode->isValid('CODE 93E0'));
        $this->assertFalse($barcode->isValid('CODE 93E1'));
    }

    public function testCODE93EXT()
    {
        $barcode = new Barcode('code93ext');
        $this->assertTrue($barcode->isValid('TEST93+'));
        $this->assertTrue($barcode->isValid('Test93+'));

// @TODO: CODE93 EXTENDED CHECKSUM VALIDATION MISSING
//        $barcode->useChecksum(true);
//        $this->assertTrue($barcode->isValid('CODE 93E0'));
//        $this->assertFalse($barcode->isValid('CODE 93E1'));
    }

    public function testEAN2()
    {
        $barcode = new Barcode('ean2');
        $this->assertTrue($barcode->isValid('12'));
        $this->assertFalse($barcode->isValid('1'));
        $this->assertFalse($barcode->isValid('123'));
    }

    public function testEAN5()
    {
        $barcode = new Barcode('ean5');
        $this->assertTrue($barcode->isValid('12345'));
        $this->assertFalse($barcode->isValid('1234'));
        $this->assertFalse($barcode->isValid('123456'));
    }

    public function testEAN8()
    {
        $barcode = new Barcode('ean8');
        $this->assertTrue($barcode->isValid('12345670'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('12345671'));
        $this->assertTrue($barcode->isValid('1234567'));
    }

    public function testEAN12()
    {
        $barcode = new Barcode('ean12');
        $this->assertTrue($barcode->isValid('123456789012'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789013'));
    }

    public function testEAN13()
    {
        $barcode = new Barcode('ean13');
        $this->assertTrue($barcode->isValid('1234567890128'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('1234567890127'));
    }

    public function testEAN14()
    {
        $barcode = new Barcode('ean14');
        $this->assertTrue($barcode->isValid('12345678901231'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('12345678901232'));
    }

    public function testEAN18()
    {
        $barcode = new Barcode('ean18');
        $this->assertTrue($barcode->isValid('123456789012345675'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789012345676'));
    }

    public function testGTIN12()
    {
        $barcode = new Barcode('gtin12');
        $this->assertTrue($barcode->isValid('123456789012'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789013'));
    }

    public function testGTIN13()
    {
        $barcode = new Barcode('gtin13');
        $this->assertTrue($barcode->isValid('1234567890128'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('1234567890127'));
    }

    public function testGTIN14()
    {
        $barcode = new Barcode('gtin14');
        $this->assertTrue($barcode->isValid('12345678901231'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('12345678901232'));
    }

    public function testIDENTCODE()
    {
        $barcode = new Barcode('identcode');
        $this->assertTrue($barcode->isValid('564000000050'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('0563102430313'));
        $this->assertFalse($barcode->isValid('564000000051'));
    }

    public function testINTELLIGENTMAIL()
    {
        $barcode = new Barcode('intelligentmail');
        $this->assertTrue($barcode->isValid('01234567094987654321'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('5555512371'));
    }

    public function testISSN()
    {
        $barcode = new Barcode('issn');
        $this->assertTrue($barcode->isValid('1144875X'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('1144874X'));

        $this->assertTrue($barcode->isValid('9771144875007'));
    }

    public function testITF14()
    {
        $barcode = new Barcode('itf14');
        $this->assertTrue($barcode->isValid('00075678164125'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('00075678164124'));
    }

    public function testLEITCODE()
    {
        $barcode = new Barcode('leitcode');
        $this->assertTrue($barcode->isValid('21348075016401'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('021348075016401'));
        $this->assertFalse($barcode->isValid('21348075016402'));
    }

    public function testPLANET()
    {
        $barcode = new Barcode('planet');
        $this->assertTrue($barcode->isValid('401234567891'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('401234567892'));
    }

    public function testPOSTNET()
    {
        $barcode = new Barcode('postnet');
        $this->assertTrue($barcode->isValid('5555512372'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('5555512371'));
    }

    public function testROYALMAIL()
    {
        $barcode = new Barcode('royalmail');
        $this->assertTrue($barcode->isValid('SN34RD1AK'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('SN34RD1AW'));

        $this->assertTrue($barcode->isValid('012345W'));
        $this->assertTrue($barcode->isValid('06CIOUH'));
    }

    public function testSSCC()
    {
        $barcode = new Barcode('sscc');
        $this->assertTrue($barcode->isValid('123456789012345675'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789012345676'));
    }

    public function testUPCA()
    {
        $barcode = new Barcode('upca');
        $this->assertTrue($barcode->isValid('123456789012'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertFalse($barcode->isValid('123456789013'));
    }

    public function testUPCE()
    {
        $barcode = new Barcode('upce');
        $this->assertTrue($barcode->isValid('02345673'));
        $this->assertFalse($barcode->isValid('02345672'));
        $this->assertFalse($barcode->isValid('123'));
        $this->assertTrue($barcode->isValid('123456'));
        $this->assertTrue($barcode->isValid('0234567'));
    }

    /**
     * @group ZF-10116
     */
    public function testArrayLengthMessage()
    {
        $barcode = new Barcode('ean8');
        $this->assertFalse($barcode->isValid('123'));
        $message = $barcode->getMessages();
        $this->assertTrue(array_key_exists('barcodeInvalidLength', $message));
        $this->assertContains("length of 7/8 characters", $message['barcodeInvalidLength']);
    }

    /**
     * @group ZF-8673
     */
    public function testCODABAR()
    {
        $barcode = new Barcode('codabar');
        $this->assertTrue($barcode->isValid('123456789'));
        $this->assertTrue($barcode->isValid('A123A'));
        $this->assertTrue($barcode->isValid('A123C'));
        $this->assertFalse($barcode->isValid('A123E'));
        $this->assertFalse($barcode->isValid('A1A23C'));
        $this->assertTrue($barcode->isValid('T123*'));
        $this->assertFalse($barcode->isValid('*123A'));
    }

    /**
     * @group ZF-11532
     */
    public function testIssnWithMod0()
    {
        $barcode = new Barcode('issn');
        $this->assertTrue($barcode->isValid('18710360'));
    }

    /**
     * @group ZF-8674
     */
    public function testCODE128()
    {
        $barcode = new Barcode('code128');
        $this->assertTrue($barcode->isValid('ˆCODE128:Š'));
        $this->assertTrue($barcode->isValid('‡01231[Š'));

        $barcode->useChecksum(false);
        $this->assertTrue($barcode->isValid('012345'));
        $this->assertTrue($barcode->isValid('ABCDEF'));
        $this->assertFalse($barcode->isValid('01234Ê'));

    }

    /**
     * Test if EAN-13 contains only numeric characters
     *
     * @group ZF-3297
     */
    public function testEan13ContainsOnlyNumeric()
    {
        $barcode = new Barcode('ean13');
        $this->assertFalse($barcode->isValid('3RH1131-1BB40'));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new Barcode('code25');
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = new Barcode('code25');
        $this->assertAttributeEquals($validator->getOption('messageVariables'),
                                     'messageVariables', $validator);
    }
}
