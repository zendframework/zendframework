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
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Service_DeveloperGarden_SecurityTokenServer
 */

/**
 * @see Zend_Service_DeveloperGarden_SecurityTokenServer_Cache
 */

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_DeveloperGarden
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_DeveloperGarden_OfflineSecurityTokenServerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_OfflineSecurityTokenServer_Mock
     */
    protected $_service = null;

    public function setUp()
    {
        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::removeCache();
        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::clearCache();

        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN', 'Unknown');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD', 'Unknown');
        }
        $config = array(
            'username' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN,
            'password' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD,
        );
        $this->service = new Zend_Service_DeveloperGarden_OfflineSecurityTokenServer_Mock($config);
    }

    public function tearDown()
    {
        // clear test case
        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::removeCache();
        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::clearCache();
    }

    public function testWsdlCache()
    {
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getWsdlCache()
        );
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setWsdlCache(WSDL_CACHE_NONE)
        );
        $this->assertEquals(
            WSDL_CACHE_NONE,
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getWsdlCache()
        );
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setWsdlCache(WSDL_CACHE_MEMORY)
        );
        $this->assertEquals(
            WSDL_CACHE_MEMORY,
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getWsdlCache()
        );
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setWsdlCache(WSDL_CACHE_DISK)
        );
        $this->assertEquals(
            WSDL_CACHE_DISK,
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getWsdlCache()
        );
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setWsdlCache(WSDL_CACHE_BOTH)
        );
        $this->assertEquals(
            WSDL_CACHE_BOTH,
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getWsdlCache()
        );
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setWsdlCache(WSDL_CACHE_NONE)
        );
        $this->assertEquals(
            WSDL_CACHE_NONE,
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getWsdlCache()
        );
    }

    public function testDisableWsdlCache()
    {
        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setWsdlCache(WSDL_CACHE_BOTH);
        $this->assertEquals(
            WSDL_CACHE_BOTH,
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getWsdlCache()
        );

        // clear cache property
        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setWsdlCache(null);
        $this->assertNull(Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getWsdlCache());
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Exception
     */
    public function testGetTokenFromCacheException()
    {
        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache('NotExisting');
    }

    public function testGetTokenFromCache()
    {
        $value = Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache('securityToken');
        $this->assertNull($value);

        $value = Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache('getTokens');
        $this->assertNull($value);
    }

    public function testSetTokenToCache1stParamException()
    {
        try {
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setTokenToCache(
                'NotExisting',
                'Zend-Framework'
            );
            $this->fail('An expected Error has not been raised.');
        } catch (Exception $e) {
        }
    }

    public function testSetTokenToCache2ndParamException()
    {
        try {
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setTokenToCache(
                'securityToken',
                'Zend-Framework'
            );
            $this->fail('An expected Error has not been raised.');
        } catch (Exception $e) {
        }
    }

    public function testSetTokenToCacheSecurityTokenResponse()
    {
        $token = new Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse();
        $token->tokenFormat = 'saml20';
        $token->tokenEncoding = 'text/xml';
        $token->tokenData = '<xml><some><nice><token /></nice></some></xml>';

        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setTokenToCache(
            'securityToken',
            $token
        );
        $value = Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache('securityToken');
        $this->assertEquals($token->tokenFormat, $value->tokenFormat);
        $this->assertEquals($token->tokenEncoding, $value->tokenEncoding);
        $this->assertEquals($token->tokenData, $value->tokenData);
        $this->assertTrue($value instanceof Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse);
    }

    public function testSetTokenToCacheGetTokensResponse()
    {
        $token = new Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse();
        $token->securityToken = '<xml><security><token /></security></xml>';

        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setTokenToCache(
            'getTokens',
            $token
        );
        $value = Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache('getTokens');
        $this->assertEquals($token->securityToken, $value->securityToken);
        $this->assertTrue($value instanceof Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_SecurityTokenServer_Exception
     */
    public function testGetTokenException()
    {
        $resp = new Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse();
        $this->assertNotNull($resp->getSecurityToken());
    }

    public function testTokenCacheGetCache()
    {
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getCache()
        );
    }

    public function testTokenCacheSetCache()
    {
        $cache = Zend\Cache\Cache::factory('Core', 'File', array(), array());
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setCache($cache)
        );
        $this->assertInstanceOf(
            'Zend\Cache\Frontend\Core',
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getCache()
        );
    }

    public function testTokenCacheRemoveCache()
    {
        $cache = Zend\Cache\Cache::factory('Core', 'File', array(), array());
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setCache($cache)
        );
        $this->assertInstanceOf(
            'Zend\Cache\Frontend\Core',
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getCache()
        );

        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::removeCache();
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getCache()
        );
    }

    public function testTokenCacheClearCache()
    {
        $cache = Zend\Cache\Cache::factory('Core', 'File', array(), array());
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setCache($cache)
        );
        $this->assertInstanceOf(
            'Zend\Cache\Frontend\Core',
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getCache()
        );

        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::clearCache();
    }

    public function testTokenCacheGetTokenFromCacheWithZendCache()
    {
        $tokens = array(
            'securityToken',
            'getTokens'
        );

        $cache = Zend\Cache\Cache::factory('Core', 'File', array(), array());
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setCache($cache)
        );
        $this->assertInstanceOf(
            'Zend\Cache\Frontend\Core',
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getCache()
        );

        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::resetTokenCache();
        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::clearCache();
        foreach ($tokens as $v) {
            $this->assertNull(
                Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache($v)
            );
        }
    }
}

class Zend_Service_DeveloperGarden_OfflineSecurityTokenServer_Mock
    extends Zend_Service_DeveloperGarden_SecurityTokenServer
{

}
