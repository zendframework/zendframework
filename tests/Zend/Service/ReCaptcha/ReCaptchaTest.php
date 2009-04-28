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

/** @see Zend_Service_ReCaptcha */
require_once 'Zend/Service/ReCaptcha.php';

/** @see Zend_Http_Client_Adapter_Test */
require_once 'Zend/Http/Client/Adapter/Test.php';

/** @see Zend_Config */
require_once 'Zend/Config.php';

/**
 * @category   Zend
 * @package    Zend_Service_ReCaptcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_ReCaptcha_ReCaptchaTest extends PHPUnit_Framework_TestCase
{
    protected $_publicKey = TESTS_ZEND_SERVICE_RECAPTCHA_PUBLIC_KEY;
    protected $_privateKey = TESTS_ZEND_SERVICE_RECAPTCHA_PRIVATE_KEY;
    protected $_reCaptcha = null;

    public function setUp()  {
        $this->_reCaptcha = new Zend_Service_ReCaptcha();
    }

    public function testSetAndGet() {
        /* Set and get IP address */
        $ip = '127.0.0.1';
        $this->_reCaptcha->setIp($ip);
        $this->assertSame($ip, $this->_reCaptcha->getIp());

        /* Set and get public key */
        $this->_reCaptcha->setPublicKey($this->_publicKey);
        $this->assertSame($this->_publicKey, $this->_reCaptcha->getPublicKey());

        /* Set and get private key */
        $this->_reCaptcha->setPrivateKey($this->_privateKey);
        $this->assertSame($this->_privateKey, $this->_reCaptcha->getPrivateKey());
    }

    public function testSingleParam() {
        $key = 'ssl';
        $value = true;

        $this->_reCaptcha->setParam($key, $value);
        $this->assertSame($value, $this->_reCaptcha->getParam($key));
    }

    public function tetsGetNonExistingParam() {
        $this->assertNull($this->_reCaptcha->getParam('foobar'));
    }

    public function testMultipleParams() {
        $params = array(
            'ssl' => true,
            'error' => 'errorMsg',
            'xhtml' => true,
        );

        $this->_reCaptcha->setParams($params);
        $_params = $this->_reCaptcha->getParams();

        $this->assertSame($params['ssl'], $_params['ssl']);
        $this->assertSame($params['error'], $_params['error']);
        $this->assertSame($params['xhtml'], $_params['xhtml']);
    }

    public function testSingleOption() {
        $key = 'theme';
        $value = 'black';

        $this->_reCaptcha->setOption($key, $value);
        $this->assertSame($value, $this->_reCaptcha->getOption($key));
    }

    public function tetsGetNonExistingOption() {
        $this->assertNull($this->_reCaptcha->getOption('foobar'));
    }

    public function testMultipleOptions() {
        $options = array(
            'theme' => 'black',
            'lang' => 'no',
        );

        $this->_reCaptcha->setOptions($options);
        $_options = $this->_reCaptcha->getOptions();

        $this->assertSame($options['theme'], $_options['theme']);
        $this->assertSame($options['lang'], $_options['lang']);
    }

    public function testSetMultipleParamsFromZendConfig() {
        $params = array(
            'ssl' => true,
            'error' => 'errorMsg',
            'xhtml' => true,
        );

        $config = new Zend_Config($params);

        $this->_reCaptcha->setParams($config);
        $_params = $this->_reCaptcha->getParams();

        $this->assertSame($params['ssl'], $_params['ssl']);
        $this->assertSame($params['error'], $_params['error']);
        $this->assertSame($params['xhtml'], $_params['xhtml']);
    }

    public function testSetInvalidParams() {
        $this->setExpectedException('Zend_Service_ReCaptcha_Exception');
        $var = 'string';
        $this->_reCaptcha->setParams($var);
    }

    public function testSetMultipleOptionsFromZendConfig() {
        $options = array(
            'theme' => 'black',
            'lang' => 'no',
        );

        $config = new Zend_Config($options);

        $this->_reCaptcha->setOptions($config);
        $_options = $this->_reCaptcha->getOptions();

        $this->assertSame($options['theme'], $_options['theme']);
        $this->assertSame($options['lang'], $_options['lang']);
    }

    public function testSetInvalidOptions() {
        $this->setExpectedException('Zend_Service_ReCaptcha_Exception');
        $var = 'string';
        $this->_reCaptcha->setOptions($var);
    }

    public function testConstructor() {
        $params = array(
            'ssl' => true,
            'error' => 'errorMsg',
            'xhtml' => true,
        );

        $options = array(
            'theme' => 'black',
            'lang' => 'no',
        );

        $ip = '127.0.0.1';

        $reCaptcha = new Zend_Service_ReCaptcha($this->_publicKey, $this->_privateKey, $params, $options, $ip);

        $_params = $reCaptcha->getParams();
        $_options = $reCaptcha->getOptions();

        $this->assertSame($this->_publicKey, $reCaptcha->getPublicKey());
        $this->assertSame($this->_privateKey, $reCaptcha->getPrivateKey());
        $this->assertSame($params['ssl'], $_params['ssl']);
        $this->assertSame($params['error'], $_params['error']);
        $this->assertSame($params['xhtml'], $_params['xhtml']);
        $this->assertSame($options['theme'], $_options['theme']);
        $this->assertSame($options['lang'], $_options['lang']);
        $this->assertSame($ip, $reCaptcha->getIp());
    }

    public function testConstructorWithNoIp() {
        // Fake the _SERVER value
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $reCaptcha = new Zend_Service_ReCaptcha(null, null, null, null, null);

        $this->assertSame($_SERVER['REMOTE_ADDR'], $reCaptcha->getIp());

        unset($_SERVER['REMOTE_ADDR']);
    }

    public function testGetHtmlWithNoPublicKey() {
        $this->setExpectedException('Zend_Service_ReCaptcha_Exception');

        $html = $this->_reCaptcha->getHtml();
    }

    public function testVerify() {
        $this->_reCaptcha->setPublicKey($this->_publicKey);
        $this->_reCaptcha->setPrivateKey($this->_privateKey);
        $this->_reCaptcha->setIp('127.0.0.1');

        if (defined('TESTS_ZEND_SERVICE_RECAPTCHA_ONLINE_ENABLED') &&
            constant('TESTS_ZEND_SERVICE_RECAPTCHA_ONLINE_ENABLED')) {

            $this->_testVerifyOnline();
        } else {
            $this->_testVerifyOffline();
        }
    }

    protected function _testVerifyOnline() {

    }

    protected function _testVerifyOffline() {
        $adapter = new Zend_Http_Client_Adapter_Test();
        $client = new Zend_Http_Client(null, array(
            'adapter' => $adapter
        ));

        Zend_Service_ReCaptcha::setHttpClient($client);

        $resp = $this->_reCaptcha->verify('challengeField', 'responseField');

        // See if we have a valid object and that the status is false
        $this->assertTrue($resp instanceof Zend_Service_ReCaptcha_Response);
        $this->assertFalse($resp->getStatus());
    }

    public function testGetHtml() {
        $this->_reCaptcha->setPublicKey($this->_publicKey);
        $errorMsg = 'errorMsg';
        $this->_reCaptcha->setParam('ssl', true);
        $this->_reCaptcha->setParam('xhtml', true);
        $this->_reCaptcha->setParam('error', $errorMsg);

        $html = $this->_reCaptcha->getHtml();

        // See if the options for the captcha exist in the string
        $this->assertNotSame(false, strstr($html, 'var RecaptchaOptions = {"theme":"red","lang":"en"};'));

        // See if the js/iframe src is correct
        $this->assertNotSame(false, strstr($html, 'src="' . Zend_Service_ReCaptcha::API_SECURE_SERVER . '/challenge?k=' . $this->_publicKey . '&error=' . $errorMsg . '"'));
    }

    public function testVerifyWithMissingPrivateKey() {
        $this->setExpectedException('Zend_Service_ReCaptcha_Exception');

        $this->_reCaptcha->verify('challenge', 'response');
    }

    public function testVerifyWithMissingIp() {
        $this->setExpectedException('Zend_Service_ReCaptcha_Exception');

        $this->_reCaptcha->setPrivateKey($this->_privateKey);
        $this->_reCaptcha->verify('challenge', 'response');
    }

    public function testVerifyWithMissingChallengeField() {
        $this->setExpectedException('Zend_Service_ReCaptcha_Exception');

        $this->_reCaptcha->setPrivateKey($this->_privateKey);
        $this->_reCaptcha->setIp('127.0.0.1');
        $this->_reCaptcha->verify('', 'response');
    }

    public function testVerifyWithMissingResponseField() {
        $this->setExpectedException('Zend_Service_ReCaptcha_Exception');

        $this->_reCaptcha->setPrivateKey($this->_privateKey);
        $this->_reCaptcha->setIp('127.0.0.1');
        $this->_reCaptcha->verify('challenge', '');
    }
}