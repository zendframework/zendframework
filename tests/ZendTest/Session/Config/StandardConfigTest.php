<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace ZendTest\Session;

use Zend\Session\Config\StandardConfig;

/**
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @group      Zend_Session
 */
class StandardConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StandardConfig
     */
    protected $config;

    public function setUp()
    {
        $this->config = new StandardConfig;
    }

    // session.save_path

    public function testSetSavePathErrorsOnInvalidPath()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid save_path provided');
        $this->config->setSavePath(__DIR__ . '/foobarboguspath');
    }

    public function testSavePathIsMutable()
    {
        $this->config->setSavePath(__DIR__);
        $this->assertEquals(__DIR__, $this->config->getSavePath());
    }
    // session.name

    public function testNameIsMutable()
    {
        $this->config->setName('FOOBAR');
        $this->assertEquals('FOOBAR', $this->config->getName());
    }

    // session.save_handler

    public function testSaveHandlerIsMutable()
    {
        $this->config->setSaveHandler('user');
        $this->assertEquals('user', $this->config->getSaveHandler());
    }

    // session.gc_probability

    public function testGcProbabilityIsMutable()
    {
        $this->config->setGcProbability(20);
        $this->assertEquals(20, $this->config->getGcProbability());
    }

    public function testSettingInvalidGcProbabilityRaisesException()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid gc_probability; must be numeric');
        $this->config->setGcProbability('foobar_bogus');
    }

    public function testSettingInvalidGcProbabilityRaisesException2()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid gc_probability; must be a percentage');
        $this->config->setGcProbability(-1);
    }

    public function testSettingInvalidGcProbabilityRaisesException3()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid gc_probability; must be a percentage');
        $this->config->setGcProbability(101);
    }

    // session.gc_divisor

    public function testGcDivisorIsMutable()
    {
        $this->config->setGcDivisor(20);
        $this->assertEquals(20, $this->config->getGcDivisor());
    }

    public function testSettingInvalidGcDivisorRaisesException()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid gc_divisor; must be numeric');
        $this->config->setGcDivisor('foobar_bogus');
    }

    public function testSettingInvalidGcDivisorRaisesException2()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid gc_divisor; must be a positive integer');
        $this->config->setGcDivisor(-1);
    }

    // session.gc_maxlifetime

    public function testGcMaxlifetimeIsMutable()
    {
        $this->config->setGcMaxlifetime(20);
        $this->assertEquals(20, $this->config->getGcMaxlifetime());
    }

    public function testSettingInvalidGcMaxlifetimeRaisesException()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid gc_maxlifetime; must be numeric');
        $this->config->setGcMaxlifetime('foobar_bogus');
    }

    public function testSettingInvalidGcMaxlifetimeRaisesException2()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid gc_maxlifetime; must be a positive integer');
        $this->config->setGcMaxlifetime(-1);
    }

    // session.serialize_handler

    public function testSerializeHandlerIsMutable()
    {
        $value = extension_loaded('wddx') ? 'wddx' : 'php_binary';
        $this->config->setSerializeHandler($value);
        $this->assertEquals($value, $this->config->getSerializeHandler());
    }

    // session.cookie_lifetime

    public function testCookieLifetimeIsMutable()
    {
        $this->config->setCookieLifetime(20);
        $this->assertEquals(20, $this->config->getCookieLifetime());
    }

    public function testCookieLifetimeCanBeZero()
    {
        $this->config->setCookieLifetime(0);
        $this->assertEquals(0, $this->config->getCookieLifetime());
    }

    public function testSettingInvalidCookieLifetimeRaisesException()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid cookie_lifetime; must be numeric');
        $this->config->setCookieLifetime('foobar_bogus');
    }

    public function testSettingInvalidCookieLifetimeRaisesException2()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid cookie_lifetime; must be a positive integer or zero');
        $this->config->setCookieLifetime(-1);
    }

    // session.cookie_path

    public function testCookiePathIsMutable()
    {
        $this->config->setCookiePath('/foo');
        $this->assertEquals('/foo', $this->config->getCookiePath());
    }

    public function testSettingInvalidCookiePathRaisesException()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid cookie path');
        $this->config->setCookiePath(24);
    }

    public function testSettingInvalidCookiePathRaisesException2()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid cookie path');
        $this->config->setCookiePath('foo');
    }

    public function testSettingInvalidCookiePathRaisesException3()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid cookie path');
        $this->config->setCookiePath('D:\\WINDOWS\\System32\\drivers\\etc\\hosts');
    }

    // session.cookie_domain

    public function testCookieDomainIsMutable()
    {
        $this->config->setCookieDomain('example.com');
        $this->assertEquals('example.com', $this->config->getCookieDomain());
    }

    public function testCookieDomainCanBeEmpty()
    {
        $this->config->setCookieDomain('');
        $this->assertEquals('', $this->config->getCookieDomain());
    }

    public function testSettingInvalidCookieDomainRaisesException()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid cookie domain: must be a string');
        $this->config->setCookieDomain(24);
    }

    public function testSettingInvalidCookieDomainRaisesException2()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'does not match the expected structure for a DNS hostname');
        $this->config->setCookieDomain('D:\\WINDOWS\\System32\\drivers\\etc\\hosts');
    }

    // session.cookie_secure

    public function testCookieSecureIsMutable()
    {
        $this->config->setCookieSecure(true);
        $this->assertEquals(true, $this->config->getCookieSecure());
    }

    // session.cookie_httponly

    public function testCookieHttpOnlyIsMutable()
    {
        $this->config->setCookieHttpOnly(true);
        $this->assertEquals(true, $this->config->getCookieHttpOnly());
    }

    // session.use_cookies

    public function testUseCookiesIsMutable()
    {
        $this->config->setUseCookies(true);
        $this->assertEquals(true, (bool) $this->config->getUseCookies());
    }

    // session.use_only_cookies

    public function testUseOnlyCookiesIsMutable()
    {
        $this->config->setUseOnlyCookies(true);
        $this->assertEquals(true, (bool) $this->config->getUseOnlyCookies());
    }

    // session.referer_check

    public function testRefererCheckIsMutable()
    {
        $this->config->setRefererCheck('FOOBAR');
        $this->assertEquals('FOOBAR', $this->config->getRefererCheck());
    }

    public function testRefererCheckMayBeEmpty()
    {
        $this->config->setRefererCheck('');
        $this->assertEquals('', $this->config->getRefererCheck());
    }

    // session.entropy_file

    public function testSetEntropyFileErrorsOnInvalidPath()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid entropy_file provided');
        $this->config->setEntropyFile(__DIR__ . '/foobarboguspath');
    }

    public function testEntropyFileIsMutable()
    {
        $this->config->setEntropyFile(__FILE__);
        $this->assertEquals(__FILE__, $this->config->getEntropyFile());
    }

    // session.entropy_length

    public function testEntropyLengthIsMutable()
    {
        $this->config->setEntropyLength(20);
        $this->assertEquals(20, $this->config->getEntropyLength());
    }

    public function testEntropyLengthCanBeZero()
    {
        $this->config->setEntropyLength(0);
        $this->assertEquals(0, $this->config->getEntropyLength());
    }

    public function testSettingInvalidEntropyLengthRaisesException()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid entropy_length; must be numeric');
        $this->config->setEntropyLength('foobar_bogus');
    }

    public function testSettingInvalidEntropyLengthRaisesException2()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException', 'Invalid entropy_length; must be a positive integer or zero');
        $this->config->setEntropyLength(-1);
    }

    // session.cache_limiter

    public function cacheLimiters()
    {
        return array(
            array('nocache'),
            array('public'),
            array('private'),
            array('private_no_expire'),
        );
    }

    /**
     * @dataProvider cacheLimiters
     */
    public function testCacheLimiterIsMutable($cacheLimiter)
    {
        $this->config->setCacheLimiter($cacheLimiter);
        $this->assertEquals($cacheLimiter, $this->config->getCacheLimiter());
    }

    // session.cache_expire

    public function testCacheExpireIsMutable()
    {
        $this->config->setCacheExpire(20);
        $this->assertEquals(20, $this->config->getCacheExpire());
    }

    public function testSettingInvalidCacheExpireRaisesException()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException',
                                    'Invalid cache_expire; must be numeric');
        $this->config->setCacheExpire('foobar_bogus');
    }

    public function testSettingInvalidCacheExpireRaisesException2()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException',
                                    'Invalid cache_expire; must be a positive integer');
        $this->config->setCacheExpire(-1);
    }

    // session.use_trans_sid

    public function testUseTransSidIsMutable()
    {
        $this->config->setUseTransSid(true);
        $this->assertEquals(true, (bool) $this->config->getUseTransSid());
    }

    // session.hash_function

    public function hashFunctions()
    {
        $hashFunctions = array(0, 1) + hash_algos();
        $provider      = array();
        foreach ($hashFunctions as $function) {
            $provider[] = array($function);
        }
        return $provider;
    }

    /**
     * @dataProvider hashFunctions
     */
    public function testHashFunctionIsMutable($hashFunction)
    {
        $this->config->setHashFunction($hashFunction);
        $this->assertEquals($hashFunction, $this->config->getHashFunction());
    }

    // session.hash_bits_per_character

    public function hashBitsPerCharacters()
    {
        return array(
            array(4),
            array(5),
            array(6),
        );
    }

    /**
     * @dataProvider hashBitsPerCharacters
     */
    public function testHashBitsPerCharacterIsMutable($hashBitsPerCharacter)
    {
        $this->config->setHashBitsPerCharacter($hashBitsPerCharacter);
        $this->assertEquals($hashBitsPerCharacter, $this->config->getHashBitsPerCharacter());
    }

    public function testSettingInvalidHashBitsPerCharacterRaisesException()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException',
                                    'Invalid hash bits per character provided');
        $this->config->setHashBitsPerCharacter('foobar_bogus');
    }

    // url_rewriter.tags

    public function testUrlRewriterTagsIsMutable()
    {
        $this->config->setUrlRewriterTags('a=href,form=action');
        $this->assertEquals('a=href,form=action', $this->config->getUrlRewriterTags());
    }

    // remember_me_seconds

    public function testRememberMeSecondsIsMutable()
    {
        $this->config->setRememberMeSeconds(20);
        $this->assertEquals(20, $this->config->getRememberMeSeconds());
    }

    public function testSettingInvalidRememberMeSecondsRaisesException()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException',
                                    'Invalid remember_me_seconds; must be numeric');
        $this->config->setRememberMeSeconds('foobar_bogus');
    }

    public function testSettingInvalidRememberMeSecondsRaisesException2()
    {
        $this->setExpectedException('Zend\Session\Exception\InvalidArgumentException',
                                    'Invalid remember_me_seconds; must be a positive integer');
        $this->config->setRememberMeSeconds(-1);
    }

    // setOptions

    /**
     * @dataProvider optionsProvider
     */
    public function testSetOptionsTranslatesUnderscoreSeparatedKeys($option, $getter, $value)
    {
        $options = array($option => $value);
        $this->config->setOptions($options);
        $this->assertSame($value, $this->config->$getter());
    }

    public function optionsProvider()
    {
        return array(
            array(
                'save_path',
                'getSavePath',
                __DIR__,
            ),
            array(
                'name',
                'getName',
                'FOOBAR',
            ),
            array(
                'save_handler',
                'getSaveHandler',
                'user',
            ),
            array(
                'gc_probability',
                'getGcProbability',
                42,
            ),
            array(
                'gc_divisor',
                'getGcDivisor',
                3,
            ),
            array(
                'gc_maxlifetime',
                'getGcMaxlifetime',
                180,
            ),
            array(
                'serialize_handler',
                'getSerializeHandler',
                'php_binary',
            ),
            array(
                'cookie_lifetime',
                'getCookieLifetime',
                180,
            ),
            array(
                'cookie_path',
                'getCookiePath',
                '/foo/bar',
            ),
            array(
                'cookie_domain',
                'getCookieDomain',
                'framework.zend.com',
            ),
            array(
                'cookie_secure',
                'getCookieSecure',
                true,
            ),
            array(
                'cookie_httponly',
                'getCookieHttpOnly',
                true,
            ),
            array(
                'use_cookies',
                'getUseCookies',
                false,
            ),
            array(
                'use_only_cookies',
                'getUseOnlyCookies',
                true,
            ),
            array(
                'referer_check',
                'getRefererCheck',
                'foobar',
            ),
            array(
                'entropy_file',
                'getEntropyFile',
                __FILE__,
            ),
            array(
                'entropy_length',
                'getEntropyLength',
                42,
            ),
            array(
                'cache_limiter',
                'getCacheLimiter',
                'private',
            ),
            array(
                'cache_expire',
                'getCacheExpire',
                42,
            ),
            array(
                'use_trans_sid',
                'getUseTransSid',
                true,
            ),
            array(
                'hash_function',
                'getHashFunction',
                'md5',
            ),
            array(
                'hash_bits_per_character',
                'getHashBitsPerCharacter',
                5,
            ),
            array(
                'url_rewriter_tags',
                'getUrlRewriterTags',
                'a=href',
            ),
        );
    }

    /**
     * Set entropy file /dev/urandom, see issue #3046
     *
     * @link https://github.com/zendframework/zf2/issues/3046
     */
    public function testSetEntropyDevUrandom()
    {
        if (!file_exists('/dev/urandom')) {
            $this->markTestSkipped(
                "This test doesn't work because /dev/urandom file doesn't exist."
            );
        }
        $result = $this->config->setEntropyFile('/dev/urandom');
        $this->assertInstanceOf('Zend\Session\Config\StandardConfig', $result);
    }
}
