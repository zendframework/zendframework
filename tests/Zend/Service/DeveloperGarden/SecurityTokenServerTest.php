<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_DeveloperGarden
 */
class Zend_Service_DeveloperGarden_SecurityTokenServerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_SecurityTokenServer_Mock
     */
    protected $_service = null;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED') ||
            TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED !== true) {
            $this->markTestSkipped('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED is not enabled');
        }

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
        $this->service = new Zend_Service_DeveloperGarden_SecurityTokenServer_Mock($config);
    }

    public function testGetTokens()
    {
        $soap = $this->service->getSoapClient();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_Soap',
            $soap
        );

        $tokens = $this->service->getTokens();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse',
            $tokens
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse',
            $tokens->securityToken
        );
        $this->assertNotNull(
            'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse',
            $tokens->getSecurityToken()
        );
    }

    public function testTokenCacheGetTokenFromCacheWithZendCacheAndCacheHit()
    {
        $tokensAr = array(
            'securityToken',
            'getTokens'
        );

        $cache = Zend_Cache::factory(
            'Core',
            'File',
            array('automatic_serialization' => true),
            array()
        );
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setCache($cache)
        );
        $this->assertInstanceOf(
            'Zend_Cache_Core',
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getCache()
        );

        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::resetTokenCache();
        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::clearCache();

        $tokens = $this->service->getTokens();
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse',
            $tokens
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse',
            $tokens->securityToken
        );
        $this->assertNotNull(
            'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse',
            $tokens->getSecurityToken()
        );

        foreach ($tokensAr as $v) {
            $this->assertNotNull(
                Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache($v)
            );
            $this->assertNotNull(
                Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache($v)
            );
        }
    }
}

class Zend_Service_DeveloperGarden_SecurityTokenServer_Mock
    extends Zend_Service_DeveloperGarden_SecurityTokenServer
{

}
