<?php

namespace ZendTest\Session;

use Zend\Session\Configuration\SessionConfiguration;

class SessionConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->config = new SessionConfiguration;
    }

    // session.save_path

    public function testSetSavePathErrorsOnInvalidPath()
    {
        $this->setExpectedException('Zend\\Session\\Exception');
        $this->config->setSavePath(__DIR__ . '/foobarboguspath');
    }

    public function testSavePathDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.save_path'), $this->config->getSavePath());
    }

    public function testSavePathIsMutable()
    {
        $this->config->setSavePath(__DIR__);
        $this->assertEquals(__DIR__, $this->config->getSavePath());
    }

    public function testSavePathAltersIniSetting()
    {
        $this->config->setSavePath(__DIR__);
        $this->assertEquals(__DIR__, ini_get('session.save_path'));
    }

    // session.name

    public function testNameDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.name'), $this->config->getName());
    }

    public function testNameIsMutable()
    {
        $this->config->setName('FOOBAR');
        $this->assertEquals('FOOBAR', $this->config->getName());
    }

    public function testNameAltersIniSetting()
    {
        $this->config->setName('FOOBAR');
        $this->assertEquals('FOOBAR', ini_get('session.name'));
    }

    // session.save_handler

    public function testSaveHandlerDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.save_handler'), $this->config->getSaveHandler(), var_export($this->config->toArray(), 1));
    }

    public function testSaveHandlerIsMutable()
    {
        $this->config->setSaveHandler('user');
        $this->assertEquals('user', $this->config->getSaveHandler());
    }

    public function testSaveHandlerAltersIniSetting()
    {
        $this->config->setSaveHandler('user');
        $this->assertEquals('user', ini_get('session.save_handler'));
    }

    public function testSettingInvalidSaveHandlerRaisesException()
    {
        $this->setExpectedException('Zend\\Session\\Exception', 'Invalid save handler');
        $this->config->setSaveHandler('foobar_bogus');
    }

    // session.gc_probability

    public function testGcProbabilityDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.gc_probability'), $this->config->getGcProbability());
    }

    public function testGcProbabilityIsMutable()
    {
        $this->config->setGcProbability(20);
        $this->assertEquals(20, $this->config->getGcProbability());
    }

    public function testGcProbabilityAltersIniSetting()
    {
        $this->config->setGcProbability(24);
        $this->assertEquals(24, ini_get('session.gc_probability'));
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

    public function testGcDivisorDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.gc_divisor'), $this->config->getGcDivisor());
    }

    public function testGcDivisorIsMutable()
    {
        $this->config->setGcDivisor(20);
        $this->assertEquals(20, $this->config->getGcDivisor());
    }

    public function testGcDivisorAltersIniSetting()
    {
        $this->config->setGcDivisor(24);
        $this->assertEquals(24, ini_get('session.gc_divisor'));
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

    public function testGcMaxlifetimeDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.gc_maxlifetime'), $this->config->getGcMaxlifetime());
    }

    public function testGcMaxlifetimeIsMutable()
    {
        $this->config->setGcMaxlifetime(20);
        $this->assertEquals(20, $this->config->getGcMaxlifetime());
    }

    public function testGcMaxlifetimeAltersIniSetting()
    {
        $this->config->setGcMaxlifetime(24);
        $this->assertEquals(24, ini_get('session.gc_maxlifetime'));
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

    public function testSerializeHandlerDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.serialize_handler'), $this->config->getSerializeHandler());
    }

    public function testSerializeHandlerIsMutable()
    {
        $value = extension_loaded('wddx') ? 'wddx' : 'php_binary';
        $this->config->setSerializeHandler($value);
        $this->assertEquals($value, $this->config->getSerializeHandler());
    }

    public function testSerializeHandlerAltersIniSetting()
    {
        $value = extension_loaded('wddx') ? 'wddx' : 'php_binary';
        $this->config->setSerializeHandler($value);
        $this->assertEquals($value, ini_get('session.serialize_handler'));
    }

    public function testSettingInvalidSerializeHandlerRaisesException()
    {
        $this->setExpectedException('Zend\\Session\\Exception', 'Invalid');
        $this->config->setSerializeHandler('foobar_bogus');
    }

    // session.cookie_lifetime

    public function testCookieLifetimeDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.cookie_lifetime'), $this->config->getCookieLifetime());
    }

    public function testCookieLifetimeIsMutable()
    {
        $this->config->setCookieLifetime(20);
        $this->assertEquals(20, $this->config->getCookieLifetime());
    }

    public function testCookieLifetimeAltersIniSetting()
    {
        $this->config->setCookieLifetime(24);
        $this->assertEquals(24, ini_get('session.cookie_lifetime'));
    }

    public function testCookieLifetimeCanBeZero()
    {
        $this->config->setCookieLifetime(0);
        $this->assertEquals(0, ini_get('session.cookie_lifetime'));
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

    public function testCookiePathDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.cookie_path'), $this->config->getCookiePath());
    }

    public function testCookiePathIsMutable()
    {
        $this->config->setCookiePath('/foo');
        $this->assertEquals('/foo', $this->config->getCookiePath());
    }

    public function testCookiePathAltersIniSetting()
    {
        $this->config->setCookiePath('/bar');
        $this->assertEquals('/bar', ini_get('session.cookie_path'));
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

    public function testCookieDomainDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.cookie_domain'), $this->config->getCookieDomain());
    }

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

    public function testCookieDomainAltersIniSetting()
    {
        $this->config->setCookieDomain('localhost');
        $this->assertEquals('localhost', ini_get('session.cookie_domain'));
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

    public function testCookieSecureDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.cookie_secure'), $this->config->getCookieSecure());
    }

    public function testCookieSecureIsMutable()
    {
        $value = ini_get('session.cookie_secure') ? false : true;
        $this->config->setCookieSecure($value);
        $this->assertEquals($value, $this->config->getCookieSecure());
    }

    public function testCookieSecureAltersIniSetting()
    {
        $value = ini_get('session.cookie_secure') ? false : true;
        $this->config->setCookieSecure($value);
        $this->assertEquals($value, ini_get('session.cookie_secure'));
    }

    // session.cookie_httponly

    public function testCookieHttpOnlyDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.cookie_httponly'), $this->config->getCookieHttpOnly());
    }

    public function testCookieHttpOnlyIsMutable()
    {
        $value = ini_get('session.cookie_httponly') ? false : true;
        $this->config->setCookieHttpOnly($value);
        $this->assertEquals($value, $this->config->getCookieHttpOnly());
    }

    public function testCookieHttpOnlyAltersIniSetting()
    {
        $value = ini_get('session.cookie_httponly') ? false : true;
        $this->config->setCookieHttpOnly($value);
        $this->assertEquals($value, ini_get('session.cookie_httponly'));
    }

    // session.use_cookies

    public function testUseCookiesDefaultsToIniSettings()
    {
        $this->assertSame((bool) ini_get('session.use_cookies'), $this->config->getUseCookies());
    }

    public function testUseCookiesIsMutable()
    {
        $value = ini_get('session.use_cookies') ? false : true;
        $this->config->setUseCookies($value);
        $this->assertEquals($value, (bool) $this->config->getUseCookies());
    }

    public function testUseCookiesAltersIniSetting()
    {
        $value = ini_get('session.use_cookies') ? false : true;
        $this->config->setUseCookies($value);
        $this->assertEquals($value, (bool) ini_get('session.use_cookies'));
    }

    // session.use_only_cookies

    public function testUseOnlyCookiesDefaultsToIniSettings()
    {
        $this->assertSame((bool) ini_get('session.use_only_cookies'), $this->config->getUseOnlyCookies());
    }

    public function testUseOnlyCookiesIsMutable()
    {
        $value = ini_get('session.use_only_cookies') ? false : true;
        $this->config->setOption('use_only_cookies', $value);
        $this->assertEquals($value, (bool) $this->config->getOption('use_only_cookies'));
    }

    public function testUseOnlyCookiesAltersIniSetting()
    {
        $value = ini_get('session.use_only_cookies') ? false : true;
        $this->config->setOption('use_only_cookies', $value);
        $this->assertEquals($value, (bool) ini_get('session.use_only_cookies'));
    }

    // session.referer_check

    public function testRefererCheckDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.referer_check'), $this->config->getRefererCheck());
    }

    public function testRefererCheckIsMutable()
    {
        $this->config->setOption('referer_check', 'FOOBAR');
        $this->assertEquals('FOOBAR', $this->config->getOption('referer_check'));
    }

    public function testRefererCheckMayBeEmpty()
    {
        $this->config->setOption('referer_check', '');
        $this->assertEquals('', $this->config->getOption('referer_check'));
    }

    public function testRefererCheckAltersIniSetting()
    {
        $this->config->setOption('referer_check', 'BARBAZ');
        $this->assertEquals('BARBAZ', ini_get('session.referer_check'));
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

    public function testEntropyFileDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.entropy_file'), $this->config->getEntropyFile());
    }

    public function testEntropyFileIsMutable()
    {
        $this->config->setEntropyFile(__FILE__);
        $this->assertEquals(__FILE__, $this->config->getEntropyFile());
    }

    public function testEntropyFileAltersIniSetting()
    {
        $this->config->setEntropyFile(__FILE__);
        $this->assertEquals(__FILE__, ini_get('session.entropy_file'));
    }

    // session.entropy_length

    public function testEntropyLengthDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.entropy_length'), $this->config->getEntropyLength());
    }

    public function testEntropyLengthIsMutable()
    {
        $this->config->setEntropyLength(20);
        $this->assertEquals(20, $this->config->getEntropyLength());
    }

    public function testEntropyLengthAltersIniSetting()
    {
        $this->config->setEntropyLength(24);
        $this->assertEquals(24, ini_get('session.entropy_length'));
    }

    public function testEntropyLengthCanBeZero()
    {
        $this->config->setEntropyLength(0);
        $this->assertEquals(0, ini_get('session.entropy_length'));
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

    public function testCacheLimiterDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.cache_limiter'), $this->config->getCacheLimiter());
    }

    /**
     * @dataProvider cacheLimiters
     */
    public function testCacheLimiterIsMutable($cacheLimiter)
    {
        $this->config->setCacheLimiter($cacheLimiter);
        $this->assertEquals($cacheLimiter, $this->config->getCacheLimiter());
    }

    /**
     * @dataProvider cacheLimiters
     */
    public function testCacheLimiterAltersIniSetting($cacheLimiter)
    {
        $this->config->setCacheLimiter($cacheLimiter);
        $this->assertEquals($cacheLimiter, ini_get('session.cache_limiter'));
    }

    public function testSettingInvalidCacheLimiterRaisesException()
    {
        $this->setExpectedException('Zend\\Session\\Exception', 'Invalid');
        $this->config->setCacheLimiter('foobar_bogus');
    }

    // session.cache_expire

    public function testCacheExpireDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.cache_expire'), $this->config->getCacheExpire());
    }

    public function testCacheExpireIsMutable()
    {
        $this->config->setCacheExpire(20);
        $this->assertEquals(20, $this->config->getCacheExpire());
    }

    public function testCacheExpireAltersIniSetting()
    {
        $this->config->setCacheExpire(24);
        $this->assertEquals(24, ini_get('session.cache_expire'));
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

    public function testUseTransSidDefaultsToIniSettings()
    {
        $this->assertSame((bool) ini_get('session.use_trans_sid'), $this->config->getUseTransSid());
    }

    public function testUseTransSidIsMutable()
    {
        $value = ini_get('session.use_trans_sid') ? false : true;
        $this->config->setOption('use_trans_sid', $value);
        $this->assertEquals($value, (bool) $this->config->getOption('use_trans_sid'));
    }

    public function testUseTransSidAltersIniSetting()
    {
        $value = ini_get('session.use_trans_sid') ? false : true;
        $this->config->setOption('use_trans_sid', $value);
        $this->assertEquals($value, (bool) ini_get('session.use_trans_sid'));
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

    public function testHashFunctionDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.hash_function'), $this->config->getHashFunction());
    }

    /**
     * @dataProvider hashFunctions
     */
    public function testHashFunctionIsMutable($hashFunction)
    {
        $this->config->setHashFunction($hashFunction);
        $this->assertEquals($hashFunction, $this->config->getHashFunction());
    }

    /**
     * @dataProvider hashFunctions
     */
    public function testHashFunctionAltersIniSetting($hashFunction)
    {
        $this->config->setHashFunction($hashFunction);
        $this->assertEquals($hashFunction, ini_get('session.hash_function'));
    }

    public function testSettingInvalidHashFunctionRaisesException()
    {
        $this->setExpectedException('Zend\\Session\\Exception', 'Invalid');
        $this->config->setHashFunction('foobar_bogus');
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

    public function testHashBitsPerCharacterDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('session.hash_bits_per_character'), $this->config->getHashBitsPerCharacter());
    }

    /**
     * @dataProvider hashBitsPerCharacters
     */
    public function testHashBitsPerCharacterIsMutable($hashBitsPerCharacter)
    {
        $this->config->setHashBitsPerCharacter($hashBitsPerCharacter);
        $this->assertEquals($hashBitsPerCharacter, $this->config->getHashBitsPerCharacter());
    }

    /**
     * @dataProvider hashBitsPerCharacters
     */
    public function testHashBitsPerCharacterAltersIniSetting($hashBitsPerCharacter)
    {
        $this->config->setHashBitsPerCharacter($hashBitsPerCharacter);
        $this->assertEquals($hashBitsPerCharacter, ini_get('session.hash_bits_per_character'));
    }

    public function testSettingInvalidHashBitsPerCharacterRaisesException()
    {
        $this->setExpectedException('Zend\\Session\\Exception', 'Invalid');
        $this->config->setHashBitsPerCharacter('foobar_bogus');
    }

    // url_rewriter.tags

    public function testUrlRewriterTagsDefaultsToIniSettings()
    {
        $this->assertSame(ini_get('url_rewriter.tags'), $this->config->getUrlRewriterTags());
    }

    public function testUrlRewriterTagsIsMutable()
    {
        $this->config->setUrlRewriterTags('a=href,form=action');
        $this->assertEquals('a=href,form=action', $this->config->getUrlRewriterTags());
    }

    public function testUrlRewriterTagsAltersIniSetting()
    {
        $this->config->setUrlRewriterTags('a=href,fieldset=');
        $this->assertEquals('a=href,fieldset=', ini_get('url_rewriter.tags'));
    }

    // remember_me_seconds

    public function testRememberMeSecondsDefaultsToTwoWeeks()
    {
        $this->assertEquals(1209600, $this->config->getRememberMeSeconds());
    }

    public function testRememberMeSecondsIsMutable()
    {
        $this->config->setRememberMeSeconds(604800);
        $this->assertEquals(604800, $this->config->getRememberMeSeconds());
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
                'getOption',
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
                'getOption',
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
        if ('getOption' == $getter) {
            $this->assertSame($value, $this->config->getOption($option));
        } else {
            $this->assertSame($value, $this->config->$getter());
        }
    }
}
