<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\ReCaptcha;

use Zend\Service\ReCaptcha\ReCaptcha;
use Zend\Service\ReCaptcha\Response as ReCaptchaResponse;
use Zend\Config;

/**
 * @category   Zend
 * @package    Zend_Service_ReCaptcha
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_ReCaptcha
 */
class ReCaptchaTest extends \PHPUnit_Framework_TestCase
{
    protected $publicKey = TESTS_ZEND_SERVICE_RECAPTCHA_PUBLIC_KEY;
    protected $privateKey = TESTS_ZEND_SERVICE_RECAPTCHA_PRIVATE_KEY;

    /**
     * @var ReCaptcha
     */
    protected $reCaptcha = null;

    public function setUp()
    {
        $this->reCaptcha = new ReCaptcha();
    }

    public function testSetAndGet()
    {
        /* Set and get IP address */
        $ip = '127.0.0.1';
        $this->reCaptcha->setIp($ip);
        $this->assertSame($ip, $this->reCaptcha->getIp());

        /* Set and get public key */
        $this->reCaptcha->setPublicKey($this->publicKey);
        $this->assertSame($this->publicKey, $this->reCaptcha->getPublicKey());

        /* Set and get private key */
        $this->reCaptcha->setPrivateKey($this->privateKey);
        $this->assertSame($this->privateKey, $this->reCaptcha->getPrivateKey());
    }

    public function testSingleParam()
    {
        $key = 'ssl';
        $value = true;

        $this->reCaptcha->setParam($key, $value);
        $this->assertSame($value, $this->reCaptcha->getParam($key));
    }

    public function tetsGetNonExistingParam()
    {
        $this->assertNull($this->reCaptcha->getParam('foobar'));
    }

    public function testMultipleParams()
    {
        $params = array(
            'ssl' => true,
            'error' => 'errorMsg',
            'xhtml' => true,
        );

        $this->reCaptcha->setParams($params);
        $_params = $this->reCaptcha->getParams();

        $this->assertSame($params['ssl'], $_params['ssl']);
        $this->assertSame($params['error'], $_params['error']);
        $this->assertSame($params['xhtml'], $_params['xhtml']);
    }

    public function testSingleOption()
    {
        $key = 'theme';
        $value = 'black';

        $this->reCaptcha->setOption($key, $value);
        $this->assertSame($value, $this->reCaptcha->getOption($key));
    }

    public function tetsGetNonExistingOption()
    {
        $this->assertNull($this->reCaptcha->getOption('foobar'));
    }

    public function testMultipleOptions()
    {
        $options = array(
            'theme' => 'black',
            'lang' => 'no',
        );

        $this->reCaptcha->setOptions($options);
        $_options = $this->reCaptcha->getOptions();

        $this->assertSame($options['theme'], $_options['theme']);
        $this->assertSame($options['lang'], $_options['lang']);
    }

    public function testSetMultipleParamsFromZendConfig()
    {
        $params = array(
            'ssl' => true,
            'error' => 'errorMsg',
            'xhtml' => true,
        );

        $config = new Config\Config($params);

        $this->reCaptcha->setParams($config);
        $_params = $this->reCaptcha->getParams();

        $this->assertSame($params['ssl'], $_params['ssl']);
        $this->assertSame($params['error'], $_params['error']);
        $this->assertSame($params['xhtml'], $_params['xhtml']);
    }

    public function testSetInvalidParams()
    {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\Exception');
        $var = 'string';
        $this->reCaptcha->setParams($var);
    }

    public function testSetMultipleOptionsFromZendConfig()
    {
        $options = array(
            'theme' => 'black',
            'lang' => 'no',
        );

        $config = new Config\Config($options);

        $this->reCaptcha->setOptions($config);
        $_options = $this->reCaptcha->getOptions();

        $this->assertSame($options['theme'], $_options['theme']);
        $this->assertSame($options['lang'], $_options['lang']);
    }

    public function testSetInvalidOptions()
    {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\Exception');
        $var = 'string';
        $this->reCaptcha->setOptions($var);
    }

    public function testConstructor()
    {
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

        $reCaptcha = new ReCaptcha($this->publicKey, $this->privateKey, $params, $options, $ip);

        $_params = $reCaptcha->getParams();
        $_options = $reCaptcha->getOptions();

        $this->assertSame($this->publicKey, $reCaptcha->getPublicKey());
        $this->assertSame($this->privateKey, $reCaptcha->getPrivateKey());
        $this->assertSame($params['ssl'], $_params['ssl']);
        $this->assertSame($params['error'], $_params['error']);
        $this->assertSame($params['xhtml'], $_params['xhtml']);
        $this->assertSame($options['theme'], $_options['theme']);
        $this->assertSame($options['lang'], $_options['lang']);
        $this->assertSame($ip, $reCaptcha->getIp());
    }

    public function testConstructorWithNoIp()
    {
        // Fake the _SERVER value
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $reCaptcha = new ReCaptcha(null, null, null, null, null);

        $this->assertSame($_SERVER['REMOTE_ADDR'], $reCaptcha->getIp());

        unset($_SERVER['REMOTE_ADDR']);
    }

    public function testGetHtmlWithNoPublicKey()
    {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\Exception');

        $html = $this->reCaptcha->getHtml();
    }

    public function testVerify()
    {
        $this->reCaptcha->setPublicKey($this->publicKey);
        $this->reCaptcha->setPrivateKey($this->privateKey);
        $this->reCaptcha->setIp('127.0.0.1');

        if (defined('TESTS_ZEND_SERVICE_RECAPTCHA_ONLINE_ENABLED') &&
            constant('TESTS_ZEND_SERVICE_RECAPTCHA_ONLINE_ENABLED')) {

            $this->_testVerifyOnline();
        } else {
            $this->_testVerifyOffline();
        }
    }

    protected function _testVerifyOnline()
    {
    }

    protected function _testVerifyOffline()
    {
        $adapter = new \Zend\Http\Client\Adapter\Test();
        $client = new \Zend\Http\Client(null, array(
            'adapter' => $adapter
        ));

        $this->reCaptcha->setHttpClient($client);

        $resp = $this->reCaptcha->verify('challengeField', 'responseField');

        // See if we have a valid object and that the status is false
        $this->assertTrue($resp instanceof ReCaptchaResponse);
        $this->assertFalse($resp->getStatus());
    }

    public function testGetHtml()
    {
        $this->reCaptcha->setPublicKey($this->publicKey);
        $errorMsg = 'errorMsg';
        $this->reCaptcha->setParam('ssl', true);
        $this->reCaptcha->setParam('xhtml', true);
        $this->reCaptcha->setParam('error', $errorMsg);

        $html = $this->reCaptcha->getHtml();

        // See if the options for the captcha exist in the string
        $this->assertNotSame(false, strstr($html, 'var RecaptchaOptions = {"theme":"red","lang":"en"};'));

        // See if the js/iframe src is correct
        $this->assertNotSame(false, strstr($html, 'src="' . ReCaptcha::API_SECURE_SERVER . '/challenge?k=' . $this->publicKey . '&error=' . $errorMsg . '"'));
    }

    /** @group ZF-10991 */
    public function testHtmlGenerationWillUseSuppliedNameForNoScriptElements()
    {
        $this->reCaptcha->setPublicKey($this->publicKey);
        $html = $this->reCaptcha->getHtml('contact');
        $this->assertContains('contact[recaptcha_challenge_field]', $html);
        $this->assertContains('contact[recaptcha_response_field]', $html);
    }

    public function testVerifyWithMissingPrivateKey()
    {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\Exception');

        $this->reCaptcha->verify('challenge', 'response');
    }

    public function testVerifyWithMissingIp()
    {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\Exception');

        $this->reCaptcha->setPrivateKey($this->privateKey);
        $this->reCaptcha->verify('challenge', 'response');
    }

    public function testVerifyWithMissingChallengeField()
    {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\Exception');

        $this->reCaptcha->setPrivateKey($this->privateKey);
        $this->reCaptcha->setIp('127.0.0.1');
        $this->reCaptcha->verify('', 'response');
    }

    public function testVerifyWithMissingResponseField()
    {
        $this->setExpectedException('Zend\\Service\\ReCaptcha\\Exception');

        $this->reCaptcha->setPrivateKey($this->privateKey);
        $this->reCaptcha->setIp('127.0.0.1');
        $this->reCaptcha->verify('challenge', '');
    }
}
