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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\ReCaptcha;

use Zend\Service\ReCaptcha;

/**
 * @category   Zend
 * @package    Zend_Service_ReCaptcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_ReCaptcha
 */
class MailHideTest extends \PHPUnit_Framework_TestCase
{
    protected $publicKey  = TESTS_ZEND_SERVICE_RECAPTCHA_MAILHIDE_PUBLIC_KEY;
    protected $privateKey = TESTS_ZEND_SERVICE_RECAPTCHA_MAILHIDE_PRIVATE_KEY;
    protected $mailHide   = null;

    public function setUp() 
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('Zend\Service\ReCaptcha tests skipped due to missing mcrypt extension');
        }
        if ($this->publicKey == 'public mailhide key' || $this->privateKey == 'private mailhide key') {
            $this->markTestSkipped('Zend\Service\ReCaptcha\MailHide tests skipped due to missing keys');
        }
        $this->mailHide = new ReCaptcha\MailHide();
    }

    public function testSetGetPrivateKey() 
    {
        $this->mailHide->setPrivateKey($this->privateKey);
        $this->assertSame($this->privateKey, $this->mailHide->getPrivateKey());
    }

    public function testSetGetEmail() {
        $mail = 'mail@example.com';

        $this->mailHide->setEmail($mail);
        $this->assertSame($mail, $this->mailHide->getEmail());
        $this->assertSame('example.com', $this->mailHide->getEmailDomainPart());
    }

    public function testEmailLocalPart() {
        $this->mailHide->setEmail('abcd@example.com');
        $this->assertSame('a', $this->mailHide->getEmailLocalPart());

        $this->mailHide->setEmail('abcdef@example.com');
        $this->assertSame('abc', $this->mailHide->getEmailLocalPart());

        $this->mailHide->setEmail('abcdefg@example.com');
        $this->assertSame('abcd', $this->mailHide->getEmailLocalPart());
    }

    public function testConstructor() {
        $mail = 'mail@example.com';

        $options = array(
            'theme' => 'black',
            'lang' => 'no',
        );

        $config = new \Zend\Config\Config($options);

        $mailHide = new ReCaptcha\MailHide($this->publicKey, $this->privateKey, $mail, $config);
        $_options = $mailHide->getOptions();

        $this->assertSame($this->publicKey, $mailHide->getPublicKey());
        $this->assertSame($this->privateKey, $mailHide->getPrivateKey());
        $this->assertSame($mail, $mailHide->getEmail());
        $this->assertSame($options['theme'], $_options['theme']);
        $this->assertSame($options['lang'], $_options['lang']);
    }

    protected function _checkHtml($html) {
        $server = ReCaptcha\MailHide::MAILHIDE_SERVER;
        $pubKey = $this->publicKey;

        $this->assertEquals(2, substr_count($html, 'k=' . $pubKey));
        $this->assertRegexp('/c\=[a-zA-Z0-9_=-]+"/', $html);
        $this->assertRegexp('/c\=[a-zA-Z0-9_=-]+\\\'/', $html);
    }

    public function testGetHtml() {
        $mail = 'mail@example.com';

        $this->mailHide->setEmail($mail);
        $this->mailHide->setPublicKey($this->publicKey);
        $this->mailHide->setPrivateKey($this->privateKey);

        $html = $this->mailHide->getHtml();

        $this->_checkHtml($html);
    }

    public function testGetHtmlWithNoEmail() {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\MailHideException');

        $html = $this->mailHide->getHtml();
    }

    public function testGetHtmlWithMissingPublicKey() {

        $mail = 'mail@example.com';

        $this->mailHide->setEmail($mail);
        $this->mailHide->setPrivateKey($this->privateKey);

        $this->setExpectedException('Zend\\Service\\ReCaptcha\\MailHideException');
        $html = $this->mailHide->getHtml();
    }

    public function testGetHtmlWithMissingPrivateKey() {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\MailHideException');

        $mail = 'mail@example.com';

        $this->mailHide->setEmail($mail);
        $this->mailHide->setPublicKey($this->publicKey);

        $html = $this->mailHide->getHtml();
    }

    public function testGetHtmlWithParamter() {
        $mail = 'mail@example.com';

        $this->mailHide->setPublicKey($this->publicKey);
        $this->mailHide->setPrivateKey($this->privateKey);

        $html = $this->mailHide->getHtml($mail);

        $this->_checkHtml($html);
    }
}
