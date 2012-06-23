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
 * @package    Zend_InfoCard
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\InfoCard;
use Zend\InfoCard\XML\EncryptedData,
    Zend\InfoCard\XML\KeyInfo,
    Zend\InfoCard\XML,
    Zend\InfoCard\XML\Exception;


/**
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_InfoCard
 */
class XmlParsingTest extends \PHPUnit_Framework_TestCase
{
    protected $_xmlDocument;

    public function setUp()
    {
        $this->tokenDocument  = __DIR__ . '/_files/encryptedtoken.xml';
        $this->tokenDocument2 = __DIR__ . '/_files/encryptedtoken2.xml';
        $this->loadXmlDocument();
    }

    public function loadXmlDocument()
    {
        $this->_xmlDocument  = file_get_contents($this->tokenDocument);
        $this->_xmlDocument2 = file_get_contents($this->tokenDocument2);
    }

    public function testEncryptedDataTypeThrowsExceptionOnInvalidInput()
    {
        $doc = file_get_contents(__DIR__ . '/_files/encryptedtoken_bad_type.xml');

        $this->setExpectedException('Zend\InfoCard\XML\Exception\InvalidArgumentException', 'Unknown EncryptedData type found');
        $encryptedData = EncryptedData\Factory::getInstance($doc);
    }
    
    public function testEncryptedDataTypeThrowsExceptionOnInvalidInput2()
    {
        $this->setExpectedException('Zend\InfoCard\XML\Exception\InvalidArgumentException', 'Invalid Data provided to create instance');
        $encryptedData = EncryptedData\Factory::getInstance(10);
    }
    
    public function testEncryptedData()
    {
        $encryptedData = EncryptedData\Factory::getInstance($this->_xmlDocument);

        $this->assertTrue($encryptedData instanceof EncryptedData\XMLEnc);
        $this->assertSame($encryptedData->getCipherValue(), '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');

        $this->assertSame($encryptedData->getEncryptionMethod(), 'http://www.w3.org/2001/04/xmlenc#aes256-cbc');
        $this->assertTrue($encryptedData->getKeyInfo() instanceof KeyInfo\XMLDSig);
    }

    public function testEncryptedData2()
    {
        $encryptedData = EncryptedData\Factory::getInstance($this->_xmlDocument2);

        $this->assertTrue($encryptedData instanceof EncryptedData\XMLEnc);
        $this->assertSame($encryptedData->getCipherValue(), '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');

        $this->assertSame($encryptedData->getEncryptionMethod(), 'http://www.w3.org/2001/04/xmlenc#aes256-cbc');
        $this->assertTrue($encryptedData->getKeyInfo() instanceof KeyInfo\XMLDSig);
    }

    public function testTostring()
    {
        $encryptedData = EncryptedData\Factory::getInstance($this->_xmlDocument);
        $key = $encryptedData->getKeyInfo();

        $this->assertTrue(is_string($key->__toString()));
    }

    public function testConversion()
    {
        $encryptedData = EncryptedData\Factory::getInstance($this->_xmlDocument);

        $keyInfo = $encryptedData->getKeyInfo();

        $this->assertTrue($keyInfo instanceof \SimpleXMLElement);

        $dom = KeyInfo\XMLDSig::convertToDOM($keyInfo);
        $this->assertTrue($dom instanceof \DOMNode);
        $sxe = KeyInfo\XMLDSig::convertToObject($dom, 'Zend\InfoCard\XML\KeyInfo\XMLDSig');

        $this->assertTrue($sxe instanceof KeyInfo\XMLDSig);
    }

    public function testConvertObjectClassLoad()
    {
        $encryptedData = EncryptedData\Factory::getInstance($this->_xmlDocument);
        $keyInfo = $encryptedData->getKeyInfo();
        $dom = KeyInfo\XMLDSig::convertToDOM($keyInfo);

        try {
            $sxe = KeyInfo\XMLDSig::convertToObject($dom, '\ZendTest\InfoCard\XML\KeyInfo\InvalidClassName');
        } catch (Exception\InvalidArgumentException $e) {
        }
    }

    public function testEncryptedDataKeyInfo()
    {
        $keyinfo = EncryptedData\Factory::getInstance($this->_xmlDocument)->getKeyInfo();

        $this->assertTrue($keyinfo instanceof KeyInfo\XMLDSig);
        $this->assertTrue($keyinfo->getEncryptedKey() instanceof XML\EncryptedKey);
    }

    public function testEncryptedKey()
    {
        $enckey = EncryptedData\Factory::getInstance($this->_xmlDocument)->getKeyInfo()->getEncryptedKey();

        $this->assertTrue($enckey instanceof XML\EncryptedKey);

        $this->assertSame($enckey->getEncryptionMethod(), 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p');
        $this->assertSame($enckey->getDigestMethod(), 'http://www.w3.org/2000/09/xmldsig#sha1');
        $this->assertSame($enckey->getCipherValue(), 'AIgtBEv9lGMikyHjV/b5mQ5LbLyupNtRH8hl5I6tJsZI5CYP32BLo9FgxAY5ZReEv+XZbqcs5KORBvTbMkP6l7MY32WJGPBDDMSB7k6DshryoZqlmGMbjt2g1nM7xOuwwfru1jC7t+qCBXL4PPBpHDhHzAW7u8tB8LQCU6GklIFa1+GoZbQ00BY/OoPbE3rxhxgGPAHXfYPLjGALIkYo9czeTO/zfcydHl5Xcyp/PsskSOUhNFcftxG+fQELb/oqc50ldBWlxBM/qU7fLI4KRfUag3J5sanCUsgiYdF0iQNfiYnUKLa9ThDHjHUQnB5EEt77cM2/DKQkyExMBBgYcRo9GzqyLXiDCYWVatCQU6rAD8NkBBpFs8W/0QXIV1J/S3DuZS3Eo4x27gRlT5YfUeO7jAZvwqy51WHNXwq13QTV2AOGfvpK3054sZm+10jdfAq6tgYdShQgO2kHRGP1q9vAC3SfD49mP9q+AemJrAkiR2HZTxkEQ+AttdfPhc2dzdLXp+ukQdqpL/xlywIp+KIim+YVjhO+Bi92rRn5Kl0h7q6MkpoTGI1F+akmNhD6VmB1Nd0G6e4AGTisuyd+vygEH7fsZhZuiMSknajfgPgazKiLUihwRvfk4FJm18Ju97tXcl6LhIJpkOcq7sI25GhWz0mHX1ErOf/949pcozo=');

        $this->assertTrue($enckey->getKeyInfo() instanceof KeyInfo\DefaultKeyInfo);
    }

    public function testEncryptedKey2()
    {
        $enckey = EncryptedData\Factory::getInstance($this->_xmlDocument2)->getKeyInfo()->getEncryptedKey();

        $this->assertTrue($enckey instanceof XML\EncryptedKey);

        $this->assertSame($enckey->getEncryptionMethod(), 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p');
        $this->assertSame($enckey->getDigestMethod(), 'http://www.w3.org/2000/09/xmldsig#sha1');
        $this->assertSame($enckey->getCipherValue(), 'AIgtBEv9lGMikyHjV/b5mQ5LbLyupNtRH8hl5I6tJsZI5CYP32BLo9FgxAY5ZReEv+XZbqcs5KORBvTbMkP6l7MY32WJGPBDDMSB7k6DshryoZqlmGMbjt2g1nM7xOuwwfru1jC7t+qCBXL4PPBpHDhHzAW7u8tB8LQCU6GklIFa1+GoZbQ00BY/OoPbE3rxhxgGPAHXfYPLjGALIkYo9czeTO/zfcydHl5Xcyp/PsskSOUhNFcftxG+fQELb/oqc50ldBWlxBM/qU7fLI4KRfUag3J5sanCUsgiYdF0iQNfiYnUKLa9ThDHjHUQnB5EEt77cM2/DKQkyExMBBgYcRo9GzqyLXiDCYWVatCQU6rAD8NkBBpFs8W/0QXIV1J/S3DuZS3Eo4x27gRlT5YfUeO7jAZvwqy51WHNXwq13QTV2AOGfvpK3054sZm+10jdfAq6tgYdShQgO2kHRGP1q9vAC3SfD49mP9q+AemJrAkiR2HZTxkEQ+AttdfPhc2dzdLXp+ukQdqpL/xlywIp+KIim+YVjhO+Bi92rRn5Kl0h7q6MkpoTGI1F+akmNhD6VmB1Nd0G6e4AGTisuyd+vygEH7fsZhZuiMSknajfgPgazKiLUihwRvfk4FJm18Ju97tXcl6LhIJpkOcq7sI25GhWz0mHX1ErOf/949pcozo=');

        $this->assertTrue($enckey->getKeyInfo() instanceof KeyInfo\DefaultKeyInfo);
    }

    public function testEncryptedKeyKeyInfo()
    {
        $keyinfo = EncryptedData\Factory::getInstance($this->_xmlDocument)->getKeyInfo()->getEncryptedKey()->getKeyInfo();

        $this->assertTrue($keyinfo instanceof KeyInfo\DefaultKeyInfo);
        $this->assertTrue($keyinfo->getSecurityTokenReference() instanceof XML\SecurityTokenReference);
    }

    public function testSecurityTokenReference()
    {
        $sectoken = EncryptedData\Factory::getInstance($this->_xmlDocument)->getKeyInfo()
                                                                                      ->getEncryptedKey()
                                                                                      ->getKeyInfo()
                                                                                      ->getSecurityTokenReference();
        $this->assertTrue($sectoken instanceof XML\SecurityTokenReference);

        $this->assertSame($sectoken->getKeyThumbprintType(), 'http://docs.oasis-open.org/wss/oasis-wss-soap-message-security-1.1#ThumbprintSHA1');
        $this->assertSame($sectoken->getKeyThumbprintEncodingType(), 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary');
        $this->assertSame($sectoken->getKeyReference(false), '/OCqQ7Np25sOiA+4OsFh1R6qIeY=');
    }

    public function testEncryptedKeyFactoryThrowsExceptionOnInvalidInput1()
    {
        $this->setExpectedException('Zend\InfoCard\XML\Exception\InvalidArgumentException', 'Invalid Data provided to create instance');
        XML\EncryptedKey::getInstance(10);
    }
    
    public function testEncryptedKeyFactoryThrowsExceptionOnInvalidInput2()
    {
        $doc = file_get_contents(__DIR__ . "/_files/encryptedkey_bad_block.xml");

        $this->setExpectedException('Zend\InfoCard\XML\Exception\InvalidArgumentException', 'Invalid XML Block provided for EncryptedKey');
        XML\EncryptedKey::getInstance($doc);
    }
    
    public function testEncryptedKeyThrowsExceptionOnGetEncryptionMethodWithBadXml()
    {
        $doc = file_get_contents(__DIR__ . "/_files/encryptedkey_missing_enc_algo.xml");
        $ek = XML\EncryptedKey::getInstance($doc);

        $this->setExpectedException('Zend\InfoCard\XML\Exception\RuntimeException', 'Unable to determine the encryption algorithm in the');
        $ek->getEncryptionMethod();
    }

    public function testKeyInfoThrowsExceptionOnInvalidInput()
    {
        $this->setExpectedException('Zend\InfoCard\XML\Exception\RuntimeException', 'Unknown KeyInfo Namespace provided');
        KeyInfo\Factory::getInstance("<foo xmlns:enc=\"http://notxmlencns.org#foo\"/>");
    }
    
    public function testKeyInfoThrowsExceptionOnInvalidInput2()
    {
        $this->setExpectedException('Zend\InfoCard\XML\Exception\InvalidArgumentException', 'Invalid Data provided to create instance');
        KeyInfo\Factory::getInstance(10);
    }

    public function testSecurityTokenReferenceThrowsExceptionsOnInvalidInput1()
    {
        $this->setExpectedException('Zend\InfoCard\XML\Exception\InvalidArgumentException', 'Invalid XML Block provided for SecurityTokenReference');
        XML\SecurityTokenReference::getInstance("<foo/>");
    }

    public function testSecurityTokenReferenceThrowsExceptionsOnInvalidInput2()
    {
        $this->setExpectedException('Zend\InfoCard\XML\Exception\InvalidArgumentException', 'Invalid Data provided to create instance');
        XML\SecurityTokenReference::getInstance(10);
    }
    
    public function testSecurityTokenReferenceThrowsExceptionsOnKeyReferenceRetrievalWithInvalidXml()
    {
        $doc = file_get_contents(__DIR__ . "/_files/security_token_bad_keyref.xml");

        $this->setExpectedException('Zend\InfoCard\XML\Exception\RuntimeException', 'Unknown Key Reference Encoding Type:');
        $si = XML\SecurityTokenReference::getInstance($doc);
        $si->getKeyReference();
    }

}
