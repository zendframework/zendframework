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
 * @see Zend_Service_DeveloperGarden_IpLocation
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
class Zend_Service_DeveloperGarden_OfflineClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_OfflineCredential_Mock
     */
    protected $_service = null;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN', 'Unknown');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD', 'Unknown');
        }
        $this->service = new Zend_Service_DeveloperGarden_OfflineClient_Mock();
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Exception
     */
    public function testConstructorCheckWsdl()
    {
        $client = new Zend_Service_DeveloperGarden_OfflineClientIncompleteWsdlFile_Mock();
        $this->assertNull($client);
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Exception
     */
    public function testConstructorCheckWsdlLocal()
    {
        $client = new Zend_Service_DeveloperGarden_OfflineClientIncompleteWsdlFileLocal_Mock();
        $this->assertNull($client);
    }

    public function testOptionsConstructor()
    {
        $options = array(
            'Username' => md5(microtime()),
            'Password' => md5(microtime()),
            'Realm' => md5(microtime()),
            'Environment' => Zend_Service_DeveloperGarden_OfflineClient_Mock::ENV_MOCK
        );

        $this->service = new Zend_Service_DeveloperGarden_OfflineClient_Mock($options);

        $creds = $this->service->getCredential();
        $this->assertEquals($options['Username'], $creds->getUsername());
        $this->assertEquals($options['Password'], $creds->getPassword());
        $this->assertEquals($options['Realm'], $creds->getRealm());
        $this->assertEquals($options['Environment'], $this->service->getEnvironment());
    }

    public function testSetOptions()
    {
        $options = array(
            'val1' => md5(microtime()),
            'val2' => md5(microtime()),
            'val3' => md5(microtime()),
            'val4' => md5(microtime())
        );

        $this->assertNull($this->service->getOption('not_existing'));
        foreach ($options as $key => $value) {
            $this->assertNull($this->service->getOption($key));
            $this->assertInstanceOf(
                'Zend_Service_DeveloperGarden_Client_AbstractClient',
                $this->service->setOption($key, $value)
            );
        }
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testSetOptionsException()
    {
        $this->assertNull($this->service->setOption(0x100, 'Foobar'));
        $this->assertNull($this->service->setOption(100  , 'Foobar'));
        $this->assertNull($this->service->setOption(0100 , 'Foobar'));
    }

    public function testGetSoapClient()
    {
        $options = array(
            'Username' => 'Zend',
            'Password' => 'Framework',
            'Realm' => 'zend.com',
            'Environment' => Zend_Service_DeveloperGarden_OfflineClient_Mock::ENV_MOCK
        );

        $this->service = new Zend_Service_DeveloperGarden_OfflineClient_Mock($options);
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_OfflineClient_Mock',
            $this->service
        );
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_Soap',
            $this->service->getSoapClient()
        );
    }

    public function testOnlineWsdl()
    {
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_AbstractClient',
            $this->service->setUseLocalWsdl(false)
        );
        $this->assertEquals(
            'http://framework.zend.com',
            $this->service->getWsdl()
        );
    }

    public function testSetLocalWsdl()
    {
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_AbstractClient',
            $this->service->setLocalWsdl('my.wsdl')
        );

        $this->assertEquals(
            'my.wsdl',
            $this->service->getWsdl()
        );
    }

    public function testSetWsdl()
    {
        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_AbstractClient',
            $this->service->setWsdl('http://my.wsdl')
        );

        $this->assertInstanceOf(
            'Zend_Service_DeveloperGarden_Client_AbstractClient',
            $this->service->setUseLocalWsdl(false)
        );

        $this->assertEquals(
            'http://my.wsdl',
            $this->service->getWsdl()
        );
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Exception
     */
    public function testSetLocalWsdlException()
    {
        $this->assertNull($this->service->setLocalWsdl(null));
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Exception
     */
    public function testSetWsdlException()
    {
        $this->assertNull($this->service->setWsdl(null));
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testCheckEnv()
    {
        $this->assertNull($this->service->checkEnvironment(null));
    }

    public function testParticipantsAction()
    {
        $actions = $this->service->getParticipantActions();
        $this->assertInternalType(
            'array',
            $actions
        );
        $this->assertEquals(3, count($actions));
    }

    public function testParticipantsActionValid()
    {
        $actions = $this->service->getParticipantActions();
        foreach ($actions as $k => $v) {
            $this->assertNull($this->service->checkParticipantAction($k));
        }
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Client_Exception
     */
    public function testParticipantsActionInValid()
    {
        $this->assertNull($this->service->checkParticipantAction('NotValid'));
    }

    public function testGetClientOptionsWithWsdlCache()
    {
        $this->assertNull(
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setWsdlCache(WSDL_CACHE_BOTH)
        );
        $options = $this->service->getClientOptions();
        $this->assertInternalType(
            'array',
            $options
        );
        $this->assertArrayHasKey('cache_wsdl', $options);
        $this->assertEquals(
            WSDL_CACHE_BOTH,
            $options['cache_wsdl']
        );
    }
}

class Zend_Service_DeveloperGarden_OfflineClient_Mock
    extends Zend_Service_DeveloperGarden_IpLocation
{
    protected $_wsdlFile = 'http://framework.zend.com';

    protected $_options = array(
        'val1' => null,
        'val2' => null,
        'val3' => null,
        'val4' => null
    );

    /**
     * returns the internal options array
     * @return array
     */
    public function getOptionsArrayRaw()
    {
        return $this->_options;
    }

}

class Zend_Service_DeveloperGarden_OfflineClientIncompleteWsdlFile_Mock
    extends Zend_Service_DeveloperGarden_IpLocation
{
    protected $_wsdlFile = null;
}

class Zend_Service_DeveloperGarden_OfflineClientIncompleteWsdlFileLocal_Mock
    extends Zend_Service_DeveloperGarden_IpLocation
{
    protected $_wsdlFileLocal = null;
}
