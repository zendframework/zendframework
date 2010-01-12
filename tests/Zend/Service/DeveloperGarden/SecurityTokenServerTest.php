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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_DeveloperGarden_SecurityTokenServerTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Service_DeveloperGarden_SecurityTokenServer
 */
require_once 'Zend/Service/DeveloperGarden/SecurityTokenServer.php';

/**
 * @see Zend_Service_DeveloperGarden_SecurityTokenServer_Cache
 */
require_once 'Zend/Service/DeveloperGarden/SecurityTokenServer/Cache.php';

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Service_DeveloperGarden_SecurityTokenServerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_SecurityTokenServer_Mock
     */
    protected $_service = null;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        PHPUnit_TextUI_TestRunner::run($suite);
    }

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
        $this->assertType(
            'Zend_Service_DeveloperGarden_Client_Soap',
            $soap
        );

        $tokens = $this->service->getTokens();
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse',
            $tokens
        );

        $this->assertType(
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

        require_once 'Zend/Cache.php';
        $cache = Zend_Cache::factory(
            'Core',
            'File',
            array('automatic_serialization' => true),
            array()
        );
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setCache($cache)
        );
        $this->assertType(
            'Zend_Cache_Core',
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getCache()
        );

        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::resetTokenCache();
        Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::clearCache();

        $tokens = $this->service->getTokens();
        $this->assertType(
            'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse',
            $tokens
        );

        $this->assertType(
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

if (PHPUnit_MAIN_METHOD == 'Zend_Service_DeveloperGarden_SecurityTokenServerTest::main') {
    Zend_Service_DeveloperGarden_SecurityTokenServerTest::main();
}
