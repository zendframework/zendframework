<?php

namespace ZendTest\Session;

use Zend\Session\Configuration\StandardConfiguration;

class StandardConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->config = new StandardConfiguration;
    }

    // session.save_path

    public function testSetSavePathErrorsOnInvalidPath()
    {
        $this->setExpectedException('Zend\\Session\\Exception');
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
        try {
            $this->config->setGcProbability('foobar_bogus');
            $this->fail('Expected exception from string GC probability');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->config->setGcProbability(-1);
            $this->fail('Expected exception from negative GC probability');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->config->setGcProbability(101);
            $this->fail('Expected exception from out-of-range GC probability');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
    }

    // session.gc_divisor

    public function testGcDivisorIsMutable()
    {
        $this->config->setGcDivisor(20);
        $this->assertEquals(20, $this->config->getGcDivisor());
    }

    public function testSettingInvalidGcDivisorRaisesException()
    {
        try {
            $this->config->setGcDivisor('foobar_bogus');
            $this->fail('Expected exception from string GC divisor');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->config->setGcDivisor(-1);
            $this->fail('Expected exception from negative GC divisor');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
    }

    // session.gc_maxlifetime

    public function testGcMaxlifetimeIsMutable()
    {
        $this->config->setGcMaxlifetime(20);
        $this->assertEquals(20, $this->config->getGcMaxlifetime());
    }

    public function testSettingInvalidGcMaxlifetimeRaisesException()
    {
        try {
            $this->config->setGcMaxlifetime('foobar_bogus');
            $this->fail('Expected exception from string GC maxlifetime');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->config->setGcMaxlifetime(-1);
            $this->fail('Expected exception from negative GC maxlifetime');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
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
        try {
            $this->config->setCookieLifetime('foobar_bogus');
            $this->fail('Expected exception from string cookie lifetime');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->config->setCookieLifetime(-1);
            $this->fail('Expected exception from negative cookie lifetime');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
    }

    // session.cookie_path

    public function testCookiePathIsMutable()
    {
        $this->config->setCookiePath('/foo');
        $this->assertEquals('/foo', $this->config->getCookiePath());
    }

    public function testSettingInvalidCookiePathRaisesException()
    {
        try {
            $this->config->setCookiePath(24);
            $this->fail('Expected exception from string cookie path');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->config->setCookiePath('foo');
            $this->fail('Expected exception from non-path cookie path');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->config->setCookiePath('D:\\WINDOWS\\System32\\drivers\\etc\\hosts');
            $this->fail('Expected exception from malformed cookie path');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
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
        try {
            $this->config->setCookieDomain(24);
            $this->fail('Expected exception from string cookie domain');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->config->setCookieDomain('D:\\WINDOWS\\System32\\drivers\\etc\\hosts');
            $this->fail('Expected exception from malformed cookie domain');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
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
        $this->setExpectedException('Zend\\Session\\Exception');
        $this->config->setEntropyFile(__DIR__ . '/foobarboguspath');
    }

    public function testSetEntropyFileErrorsOnDirectory()
    {
        $this->setExpectedException('Zend\\Session\\Exception');
        $this->config->setEntropyFile(__DIR__);
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
        try {
            $this->config->setEntropyLength('foobar_bogus');
            $this->fail('Expected exception from string entropy length');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->config->setEntropyLength(-1);
            $this->fail('Expected exception from negative entropy length');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
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
        try {
            $this->config->setCacheExpire('foobar_bogus');
            $this->fail('Expected exception from string cache expiration');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->config->setCacheExpire(-1);
            $this->fail('Expected exception from negative cache expiration');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
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
        $this->setExpectedException('Zend\\Session\\Exception', 'Invalid');
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
        try {
            $this->config->setRememberMeSeconds('foobar_bogus');
            $this->fail('Expected exception from string remember_me_seconds');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }

        try {
            $this->config->setRememberMeSeconds(-1);
            $this->fail('Expected exception from negative remember_me_seconds');
        } catch (\Zend\Session\Exception $e) {
            $this->assertContains('Invalid', $e->getMessage());
        }
    }

    // setOptions

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
     * @dataProvider optionsProvider
     */
    public function testSetOptionsTranslatesUnderscoreSeparatedKeys($option, $getter, $value)
    {
        $options = array($option => $value);
        $this->config->setOptions($options);
        $this->assertSame($value, $this->config->$getter());
    }
}
