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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** @see Zend_Service_ReCaptcha_MailHide */
require_once 'Zend/Service/ReCaptcha/MailHide.php';

/**
 * @category   Zend
 * @package    Zend_Service_ReCaptcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_ReCaptcha_MailHideTest extends PHPUnit_Framework_TestCase
{
    protected $_publicKey = TESTS_ZEND_SERVICE_RECAPTCHA_MAILHIDE_PUBLIC_KEY;
    protected $_privateKey = TESTS_ZEND_SERVICE_RECAPTCHA_MAILHIDE_PRIVATE_KEY;
    protected $_mailHide = null;

    public function setUp() {
        $this->_mailHide = new Zend_Service_ReCaptcha_MailHide();
    }

    public function testSetGetPrivateKey() {
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

        $config = new Zend_Config($options);

        $mailHide = new Zend_Service_ReCaptcha_MailHide($this->_publicKey, $this->_privateKey, $mail, $config);
        $_options = $mailHide->getOptions();

        $this->assertSame($this->_publicKey, $mailHide->getPublicKey());
        $this->assertSame($this->_privateKey, $mailHide->getPrivateKey());
        $this->assertSame($mail, $mailHide->getEmail());
        $this->assertSame($options['theme'], $_options['theme']);
        $this->assertSame($options['lang'], $_options['lang']);
    }

    protected function _checkHtml($html) {
        $server = Zend_Service_ReCaptcha_MailHide::MAILHIDE_SERVER;
        $pubKey = $this->_publicKey;

        $this->assertNotSame(false, strstr($html, 'm<a href="' . $server . '?k=' . $pubKey . '&amp;c=mX82V4Dce5Y9dNGvrJszhI2ld_eMWxpNsMNovQGRrXs=" onclick="window.open(\'' . $server . '?k=' . $pubKey . '&amp;c=mX82V4Dce5Y9dNGvrJszhI2ld_eMWxpNsMNovQGRrXs=\', \'\', \'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300\'); return false;" title="Reveal this e-mail address">...</a>@example.com'));
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
        $this->setExpectedException('Zend_Service_ReCaptcha_MailHide_Exception');

        $html = $this->_mailHide->getHtml();
    }

    public function testGetHtmlWithMissingPublicKey() {
        $this->setExpectedException('Zend_Service_ReCaptcha_MailHide_Exception');

        $mail = 'mail@example.com';

        $this->_mailHide->setEmail($mail);
        $this->_mailHide->setPrivateKey($this->_privateKey);

        $html = $this->_mailHide->getHtml();
    }

    public function testGetHtmlWithMissingPrivateKey() {
        $this->setExpectedException('Zend_Service_ReCaptcha_MailHide_Exception');

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