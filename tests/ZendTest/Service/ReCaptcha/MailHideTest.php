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
 * @package    Zend_Service_ReCaptcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Service\ReCaptcha;

use Zend\Service\ReCaptcha;

/**
 * @category   Zend
 * @package    Zend_Service_ReCaptcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_ReCaptcha
 */
class MailHideTest extends \PHPUnit_Framework_TestCase
{
    protected $_publicKey  = TESTS_ZEND_SERVICE_RECAPTCHA_MAILHIDE_PUBLIC_KEY;
    protected $_privateKey = TESTS_ZEND_SERVICE_RECAPTCHA_MAILHIDE_PRIVATE_KEY;
    protected $_mailHide   = null;

    public function setUp() 
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('Zend_Service_ReCaptcha tests skipped due to missing mcrypt extension');
        }
        $this->_mailHide = new ReCaptcha\MailHide();
    }

    public function testSetGetPrivateKey() 
    {
        $this->_mailHide->setPrivateKey($this->_privateKey);
        $this->assertSame($this->_privateKey, $this->_mailHide->getPrivateKey());
    }

    public function testSetGetEmail() {
        $mail = 'mail@example.com';

        $this->_mailHide->setEmail($mail);
        $this->assertSame($mail, $this->_mailHide->getEmail());
        $this->assertSame('example.com', $this->_mailHide->getEmailDomainPart());
    }

    public function testEmailLocalPart() {
        $this->_mailHide->setEmail('abcd@example.com');
        $this->assertSame('a', $this->_mailHide->getEmailLocalPart());

        $this->_mailHide->setEmail('abcdef@example.com');
        $this->assertSame('abc', $this->_mailHide->getEmailLocalPart());

        $this->_mailHide->setEmail('abcdefg@example.com');
        $this->assertSame('abcd', $this->_mailHide->getEmailLocalPart());
    }

    public function testConstructor() {
        $mail = 'mail@example.com';

        $options = array(
            'theme' => 'black',
            'lang' => 'no',
        );

        $config = new \Zend\Config\Config($options);

        $mailHide = new ReCaptcha\MailHide($this->_publicKey, $this->_privateKey, $mail, $config);
        $_options = $mailHide->getOptions();

        $this->assertSame($this->_publicKey, $mailHide->getPublicKey());
        $this->assertSame($this->_privateKey, $mailHide->getPrivateKey());
        $this->assertSame($mail, $mailHide->getEmail());
        $this->assertSame($options['theme'], $_options['theme']);
        $this->assertSame($options['lang'], $_options['lang']);
    }

    protected function _checkHtml($html) {
        $server = ReCaptcha\MailHide::MAILHIDE_SERVER;
        $pubKey = $this->_publicKey;

        // Static value of the encrypter version of mail@example.com
        $encryptedEmail = 'XydrEdd6Eo90PE-LpxkmTEsq2G6SCeDzWkEQpF6f7v8=';

        $this->assertNotSame(false, strstr($html, 'm<a href="' . $server . '?k=' . $pubKey . '&amp;c=' . $encryptedEmail . '" onclick="window.open(\'' . $server . '?k=' . $pubKey . '&amp;c=' . $encryptedEmail . '\', \'\', \'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300\'); return false;" title="Reveal this e-mail address">...</a>@example.com'));
    }

    public function testGetHtml() {
        $mail = 'mail@example.com';

        $this->_mailHide->setEmail($mail);
        $this->_mailHide->setPublicKey($this->_publicKey);
        $this->_mailHide->setPrivateKey($this->_privateKey);

        $html = $this->_mailHide->getHtml();

        $this->_checkHtml($html);
    }

    public function testGetHtmlWithNoEmail() {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\MailHideException');

        $html = $this->_mailHide->getHtml();
    }

    public function testGetHtmlWithMissingPublicKey() {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\MailHideException');

        $mail = 'mail@example.com';

        $this->_mailHide->setEmail($mail);
        $this->_mailHide->setPrivateKey($this->_privateKey);

        $html = $this->_mailHide->getHtml();
    }

    public function testGetHtmlWithMissingPrivateKey() {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\MailHideException');

        $mail = 'mail@example.com';

        $this->_mailHide->setEmail($mail);
        $this->_mailHide->setPublicKey($this->_publicKey);

        $html = $this->_mailHide->getHtml();
    }

    public function testGetHtmlWithParamter() {
        $mail = 'mail@example.com';

        $this->_mailHide->setPublicKey($this->_publicKey);
        $this->_mailHide->setPrivateKey($this->_privateKey);

        $html = $this->_mailHide->getHtml($mail);

        $this->_checkHtml($html);
    }
}
