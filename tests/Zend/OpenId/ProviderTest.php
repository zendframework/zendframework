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
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\OpenId;

use Zend\OpenId\OpenId, 
    Zend\OpenId\Provider,
    Zend\OpenId\Extension;

/**
 * @outputBuffering enabled
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_OpenId
 */
class ProviderTest extends \PHPUnit_Framework_TestCase
{
    const USER     = "http://test_user.myopenid.com/";
    const PASSWORD = "01234567890abcdef";

    const HANDLE   = "01234567890abcdef";

    private $_user;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_user = new Provider\User\Session();
    }

    /**
     * testing register
     *
     */
    public function testRegister()
    {
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $storage->delUser(self::USER);
        $provider = new Provider\GenericProvider(null, null, $this->_user, $storage);
        $this->assertFalse( $storage->checkUser(self::USER, self::PASSWORD) );

        // wrong ID
        $this->assertFalse( $provider->register("", self::PASSWORD) );
        // registration of new user
        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        // registration of existent user
        $this->assertFalse( $provider->register(self::USER, self::PASSWORD) );

        $this->assertTrue( $storage->checkUser(self::USER, md5(self::USER . self::PASSWORD)) );
        $storage->delUser(self::USER);
    }

    /**
     * testing hasUser
     *
     */
    public function testHasUser()
    {
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $storage->delUser(self::USER);
        $provider = new Provider\GenericProvider(null, null, $this->_user, $storage);

        // wrong ID
        $this->assertFalse( $provider->hasUser("") );
        $this->assertFalse( $provider->hasUser("http://:80/test") );

        // check for non existent
        $this->assertFalse( $provider->hasUser(self::USER) );

        // check for existent user
        $this->assertTrue( $storage->addUser(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->hasUser(self::USER) );

        $storage->delUser(self::USER);
    }

    /**
     * testing login
     *
     */
    public function testLogin()
    {
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Provider\GenericProvider(null, null, $this->_user, $storage);

        // wrong ID
        $this->assertFalse( $provider->login("", self::PASSWORD) );
        $this->assertFalse( $this->_user->getLoggedInUser() );
        $this->assertFalse( $provider->login("http://:80/test", self::PASSWORD) );
        $this->assertFalse( $this->_user->getLoggedInUser() );

        // login as non existent user
        $this->assertFalse( $provider->login(self::USER, self::PASSWORD) );
        $this->assertFalse( $this->_user->getLoggedInUser() );
        // login as existent user with wrong password
        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertFalse( $provider->login(self::USER, self::PASSWORD . "x") );
        $this->assertFalse( $this->_user->getLoggedInUser() );
        // login as existent user with proper password
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertSame( self::USER, $this->_user->getLoggedInUser() );

        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
    }

    /**
     * testing logout
     *
     */
    public function testLogout()
    {
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Provider\GenericProvider(null, null, $this->_user, $storage);

        $this->assertFalse( $this->_user->getLoggedInUser() );
        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertFalse( $this->_user->getLoggedInUser() );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertSame( self::USER, $this->_user->getLoggedInUser() );
        $this->assertTrue( $provider->logout() );
        $this->assertFalse( $this->_user->getLoggedInUser() );

        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
    }

    /**
     * testing logout
     *
     */
    public function testLoggedInUser()
    {
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Provider\GenericProvider(null, null, $this->_user, $storage);

        $this->assertFalse( $provider->getLoggedInUser() );
        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertFalse( $provider->getLoggedInUser() );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertSame( self::USER, $this->_user->getLoggedInUser() );
        $this->assertTrue( $provider->logout() );
        $this->assertFalse( $provider->getLoggedInUser() );

        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
    }

    /**
     * testing getSiteRoot
     *
     */
    public function testGetSiteRoot()
    {
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $provider = new Provider\GenericProvider(null, null, $this->_user, $storage);

        $params = array(
            'openid_realm'      => "http://wrong/",
            'openid_trust_root' => "http://root/",
            'openid_return_to'  => "http://wrong/",
        );
        $this->assertSame( "http://root/", $provider->getSiteRoot($params) );

        $params = array(
            'openid_realm'      => "http://wrong/",
            'openid_return_to'  => "http://root/",
        );
        $this->assertSame( "http://root/", $provider->getSiteRoot($params) );

        $params = array(
            'openid_realm'      => "http://wrong/",
        );
        $this->assertFalse( $provider->getSiteRoot($params) );

        $params = array(
            'openid_ns'         => OpenId::NS_2_0,
            'openid_realm'      => "http://root/",
            'openid_trust_root' => "http://wrong/",
            'openid_return_to'  => "http://wrong/",
        );
        $this->assertSame( "http://root/", $provider->getSiteRoot($params) );

        $params = array(
            'openid_ns'         => OpenId::NS_2_0,
            'openid_trust_root' => "http://wrong/",
            'openid_return_to'  => "http://root/",
        );
        $this->assertSame( "http://root/", $provider->getSiteRoot($params) );

        $params = array(
            'openid_ns'         => OpenId::NS_2_0,
            'openid_return_to'  => "http://root/",
        );
        $this->assertSame( "http://root/", $provider->getSiteRoot($params) );

        $params = array(
            'openid_ns'         => OpenId::NS_2_0,
        );
        $this->assertFalse( $provider->getSiteRoot($params) );

        $params = array(
            'openid_trust_root' => "",
        );
        $this->assertFalse( $provider->getSiteRoot($params) );
    }

    /**
     * testing allowSite
     *
     */
    public function testAllowSite()
    {
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Provider\GenericProvider(null, null, $this->_user, $storage);

        // not logged in
        $this->assertFalse( $provider->allowSite("http://www.test.com/") );
        // logged in
        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->allowSite("http://www.test.com/") );

        $trusted = $storage->getTrustedSites(self::USER);
        $this->assertTrue( is_array($trusted) );
        $this->assertSame( 1, count($trusted) );
        reset($trusted);
        $this->assertSame( "http://www.test.com/", key($trusted) );
        $this->assertSame( true, current($trusted) );

        // duplicate
        $this->assertTrue( $provider->allowSite("http://www.test.com/") );

        $trusted = $storage->getTrustedSites(self::USER);
        $this->assertTrue( is_array($trusted) );
        $this->assertSame( 1, count($trusted) );
        reset($trusted);
        $this->assertSame( "http://www.test.com/", key($trusted) );
        $this->assertSame( true, current($trusted) );

        // extensions
        $sreg = new Extension\Sreg(array("nickname"=>"test_id"));
        $this->assertTrue( $provider->allowSite("http://www.test.com/", $sreg) );

        $trusted = $storage->getTrustedSites(self::USER);
        $this->assertTrue( is_array($trusted) );
        $this->assertSame( 1, count($trusted) );
        reset($trusted);
        $this->assertSame( "http://www.test.com/", key($trusted) );
        $this->assertSame( array('Zend\OpenId\Extension\Sreg'=>array('nickname'=>'test_id')), current($trusted) );

        $this->_user->delLoggedInUser();
        $storage->delUser(self::USER);
    }

    /**
     * testing denySite
     *
     */
    public function testDenySite()
    {
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Provider\GenericProvider(null, null, $this->_user, $storage);
        $sreg = new Extension\Sreg(array("nickname"=>"test_id"));

        // not logged in
        $this->assertFalse( $provider->denySite("http://www.test.com/") );

        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->allowSite("http://www.test1.com/") );
        $this->assertTrue( $provider->allowSite("http://www.test2.com/", $sreg) );
        $this->AssertSame( array(
                               'http://www.test1.com/' => true,
                               'http://www.test2.com/' => array(
                                   'Zend\OpenId\Extension\Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               )
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->assertTrue( $provider->denySite("http://www.test3.com/") );
        $this->AssertSame( array(
                               'http://www.test1.com/' => true,
                               'http://www.test2.com/' => array(
                                   'Zend\OpenId\Extension\Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               ),
                               'http://www.test3.com/' => false
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->assertTrue( $provider->denySite("http://www.test1.com/") );
        $this->AssertSame( array(
                               'http://www.test1.com/' => false,
                               'http://www.test2.com/' => array(
                                   'Zend\OpenId\Extension\Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               ),
                               'http://www.test3.com/' => false
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->assertTrue( $provider->denySite("http://www.test2.com/") );
        $this->AssertSame( array(
                               'http://www.test1.com/' => false,
                               'http://www.test2.com/' => false,
                               'http://www.test3.com/' => false
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->_user->delLoggedInUser();
        $storage->delUser(self::USER);
    }

    /**
     * testing delSite
     *
     */
    public function testDelSite()
    {
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Provider\GenericProvider(null, null, $this->_user, $storage);
        $sreg = new Extension\Sreg(array("nickname"=>"test_id"));

        // not logged in
        $this->assertFalse( $provider->delSite("http://www.test.com/") );

        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->allowSite("http://www.test1.com/") );
        $this->assertTrue( $provider->allowSite("http://www.test2.com/", $sreg) );
        $this->AssertSame( array(
                               'http://www.test1.com/' => true,
                               'http://www.test2.com/' => array(
                                   'Zend\OpenId\Extension\Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               )
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->assertTrue( $provider->delSite("http://www.test3.com/") );
        $this->AssertSame( array(
                               'http://www.test1.com/' => true,
                               'http://www.test2.com/' => array(
                                   'Zend\OpenId\Extension\Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               )
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->assertTrue( $provider->delSite("http://www.test1.com/") );
        $this->AssertSame( array(
                               'http://www.test2.com/' => array(
                                   'Zend\OpenId\Extension\Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               )
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->assertTrue( $provider->delSite("http://www.test2.com/") );
        $this->AssertSame( array(
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->_user->delLoggedInUser();
        $storage->delUser(self::USER);
    }

    /**
     * testing getTrustedSites
     *
     */
    public function testGetTrustedSites()
    {
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Provider\GenericProvider(null, null, $this->_user, $storage);
        $sreg = new Extension\Sreg(array("nickname"=>"test_id"));

        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->allowSite("http://www.test1.com/") );
        $this->assertTrue( $provider->allowSite("http://www.test2.com/", $sreg) );
        $this->AssertSame( array(
                               'http://www.test1.com/' => true,
                               'http://www.test2.com/' => array(
                                   'Zend\OpenId\Extension\Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               )
                           ),
                           $provider->getTrustedSites() );

        $this->_user->delLoggedInUser();
        $this->AssertFalse( $provider->getTrustedSites() );

        $storage->delUser(self::USER);
    }

    /**
     * testing genSecret
     *
     */
    public function testGenSecret()
    {
        $provider = new ProviderHelper(null, null, $this->_user, new Provider\Storage\File(__DIR__."/_files/provider"));

        // SHA1
        $x = $provider->genSecret("sha1");
        $this->assertTrue( is_string($x) );
        $this->assertSame( 20, strlen($x) );

        // SHA256
        $x = $provider->genSecret("sha256");
        $this->assertTrue( is_string($x) );
        $this->assertSame( 32, strlen($x) );

        // invalid function
        $this->assertFalse( $provider->genSecret("md5") );
    }

    /**
     * testing _associate
     *
     */
    public function testAssociate()
    {
        try {
            $storage = new Provider\Storage\File(__DIR__."/_files/provider");
            $provider = new ProviderHelper(null, null, $this->_user, $storage);

            // Wrong assoc_type
            $ret = $provider->handle(array('openid_mode'=>'associate'));
            $res = array();
            foreach (explode("\n", $ret) as $line) {
                if (!empty($line)) {
                    list($key, $val) = explode(":", $line, 2);
                    $res[$key] = $val;
                }
            }
            $this->assertSame( 'unsupported-type', $res['error-code'] );

            // Wrong assoc_type (OpenID 2.0)
            $ret = $provider->handle(array('openid_ns'=>OpenId::NS_2_0,
                                           'openid_mode'=>'associate'));
            $res = array();
            foreach (explode("\n", $ret) as $line) {
                if (!empty($line)) {
                    list($key, $val) = explode(":", $line, 2);
                    $res[$key] = $val;
                }
            }
            $this->assertSame( OpenId::NS_2_0, $res['ns'] );
            $this->assertSame( 'unsupported-type', $res['error-code'] );

            // Wrong session_type
            $ret = $provider->handle(array('openid_mode'=>'associate',
                                           'openid_assoc_type'=>'HMAC-SHA1',
                                           'openid_session_type'=>'DH-SHA257'));
            $res = array();
            foreach (explode("\n", $ret) as $line) {
                if (!empty($line)) {
                    list($key, $val) = explode(":", $line, 2);
                    $res[$key] = $val;
                }
            }
            $this->assertSame( 'unsupported-type', $res['error-code'] );

            // Associaation without encryption
            $ret = $provider->handle(array('openid_assoc_type'=>'HMAC-SHA1',
                                           'openid_mode'=>'associate'));
            $res = array();
            foreach (explode("\n", $ret) as $line) {
                if (!empty($line)) {
                    list($key, $val) = explode(":", $line, 2);
                    $res[$key] = $val;
                }
            }
            $this->assertSame( 'HMAC-SHA1', $res['assoc_type'] );
            $this->assertTrue( isset($res['mac_key']) );
            $this->assertSame( 20, strlen(base64_decode($res['mac_key'])) );
            $this->assertTrue( isset($res['assoc_handle']) );
            $this->assertSame( '3600', $res['expires_in'] );
            $this->assertFalse( isset($res['session_type']) );
            $this->assertTrue( $storage->getAssociation($res['assoc_handle'], $macFunc, $secret, $expires) );
            $this->assertSame( 'sha1', $macFunc );
            $this->assertSame( bin2hex(base64_decode($res['mac_key'])), bin2hex($secret) );

            // Associaation without encryption (OpenID 2.0)
            $ret = $provider->handle(array('openid_ns'=>OpenId::NS_2_0,
                                           'openid_assoc_type'=>'HMAC-SHA256',
                                           'openid_mode'=>'associate'));
            $res = array();
            foreach (explode("\n", $ret) as $line) {
                if (!empty($line)) {
                    list($key, $val) = explode(":", $line, 2);
                    $res[$key] = $val;
                }
            }
            $this->assertSame( OpenId::NS_2_0, $res['ns'] );
            $this->assertSame( 'HMAC-SHA256', $res['assoc_type'] );
            $this->assertTrue( isset($res['mac_key']) );
            $this->assertSame( 32, strlen(base64_decode($res['mac_key'])) );
            $this->assertTrue( isset($res['assoc_handle']) );
            $this->assertSame( '3600', $res['expires_in'] );
            $this->assertFalse( isset($res['session_type']) );
            $this->assertTrue( $storage->getAssociation($res['assoc_handle'], $macFunc, $secret, $expires) );
            $this->assertSame( 'sha256', $macFunc );
            $this->assertSame( bin2hex(base64_decode($res['mac_key'])), bin2hex($secret) );

            // Associaation without encryption (OpenID 2.0)
            $ret = $provider->handle(array('openid_ns'=>OpenId::NS_2_0,
                                           'openid_assoc_type'=>'HMAC-SHA256',
                                           'openid_mode'=>'associate',
                                           'openid_session_type'=>'no-encryption'));
            $res = array();
            foreach (explode("\n", $ret) as $line) {
                if (!empty($line)) {
                    list($key, $val) = explode(":", $line, 2);
                    $res[$key] = $val;
                }
            }
            $this->assertSame( OpenId::NS_2_0, $res['ns'] );
            $this->assertSame( 'HMAC-SHA256', $res['assoc_type'] );
            $this->assertTrue( isset($res['mac_key']) );
            $this->assertSame( 32, strlen(base64_decode($res['mac_key'])) );
            $this->assertTrue( isset($res['assoc_handle']) );
            $this->assertSame( '3600', $res['expires_in'] );
            $this->assertSame( 'no-encryption', $res['session_type'] );
            $this->assertTrue( $storage->getAssociation($res['assoc_handle'], $macFunc, $secret, $expires) );
            $this->assertSame( 'sha256', $macFunc );
            $this->assertSame( bin2hex(base64_decode($res['mac_key'])), bin2hex($secret) );

            // Associaation with DH-SHA1 encryption
            $ret = $provider->handle(array('openid_assoc_type'=>'HMAC-SHA1',
                                           'openid_mode'=>'associate',
                                           'openid_session_type'=>'DH-SHA1',
                                           'openid_dh_modulus'=>'ANz5OguIOXLsDhmYmsWizjEOHTdxfo2Vcbt2I3MYZuYe91ouJ4mLBX+YkcLiemOcPym2CBRYHNOyyjmG0mg3BVd9RcLn5S3IHHoXGHblzqdLFEi/368Ygo79JRnxTkXjgmY0rxlJ5bU1zIKaSDuKdiI+XUkKJX8Fvf8W8vsixYOr',
                                           'openid_dh_gen'=>'Ag==',
                                           'openid_dh_consumer_public'=>'RqexRm+Zn5s3sXxFBjI9WfCOBwBDDQBKPzX4fjMGl3YEJh5tx8SVo7awgwuqsliR+nvjmRh5kSFIGv8YSCsy88v1CcAfWUGfjehO9euxQcXOYJnNGbl6GQrE2FYe2RCvML4Yi8eYCYtCQi0wlDE7BJXGSVPXFzj/ru0lR/voPpk=',
                                           ));
            $res = array();
            foreach (explode("\n", $ret) as $line) {
                if (!empty($line)) {
                    list($key, $val) = explode(":", $line, 2);
                    $res[$key] = $val;
                }
            }
            $this->assertSame( 'HMAC-SHA1', $res['assoc_type'] );
            $this->assertSame( 'DH-SHA1', $res['session_type'] );
            $this->assertTrue( isset($res['dh_server_public']) );
            $this->assertTrue( isset($res['enc_mac_key']) );
            $this->assertSame( 20, strlen(base64_decode($res['enc_mac_key'])) );
            $this->assertTrue( isset($res['assoc_handle']) );
            $this->assertSame( '3600', $res['expires_in'] );
            $this->assertTrue( $storage->getAssociation($res['assoc_handle'], $macFunc, $secret, $expires) );
            $this->assertSame( 'sha1', $macFunc );

            // Associaation with DH-SHA256 encryption (OpenID 2.0)
            $ret = $provider->handle(array('openid_ns'=>OpenId::NS_2_0,
                                           'openid_assoc_type'=>'HMAC-SHA256',
                                           'openid_mode'=>'associate',
                                           'openid_session_type'=>'DH-SHA256',
                                           'openid_dh_modulus'=>'ANz5OguIOXLsDhmYmsWizjEOHTdxfo2Vcbt2I3MYZuYe91ouJ4mLBX+YkcLiemOcPym2CBRYHNOyyjmG0mg3BVd9RcLn5S3IHHoXGHblzqdLFEi/368Ygo79JRnxTkXjgmY0rxlJ5bU1zIKaSDuKdiI+XUkKJX8Fvf8W8vsixYOr',
                                           'openid_dh_gen'=>'Ag==',
                                           'openid_dh_consumer_public'=>'RqexRm+Zn5s3sXxFBjI9WfCOBwBDDQBKPzX4fjMGl3YEJh5tx8SVo7awgwuqsliR+nvjmRh5kSFIGv8YSCsy88v1CcAfWUGfjehO9euxQcXOYJnNGbl6GQrE2FYe2RCvML4Yi8eYCYtCQi0wlDE7BJXGSVPXFzj/ru0lR/voPpk=',
                                           ));
            $res = array();
            foreach (explode("\n", $ret) as $line) {
                if (!empty($line)) {
                    list($key, $val) = explode(":", $line, 2);
                    $res[$key] = $val;
                }
            }
            $this->assertSame( 'HMAC-SHA256', $res['assoc_type'] );
            $this->assertSame( 'DH-SHA256', $res['session_type'] );
            $this->assertTrue( isset($res['dh_server_public']) );
            $this->assertTrue( isset($res['enc_mac_key']) );
            $this->assertSame( 32, strlen(base64_decode($res['enc_mac_key'])) );
            $this->assertTrue( isset($res['assoc_handle']) );
            $this->assertSame( '3600', $res['expires_in'] );
            $this->assertTrue( $storage->getAssociation($res['assoc_handle'], $macFunc, $secret, $expires) );
            $this->assertSame( 'sha256', $macFunc );
        } catch (\Zend\OpenId\Exception\ExceptionInterface $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    /**
     * testing _checkAuthentication
     *
     */
    public function testCheckAuthentication()
    {
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $provider = new ProviderHelper(null, null, $this->_user, $storage);

        // Wrong arguments
        $ret = $provider->handle(array('openid_mode'=>'check_authentication'));
        $res = array();
        foreach (explode("\n", $ret) as $line) {
            if (!empty($line)) {
                list($key, $val) = explode(":", $line, 2);
                $res[$key] = $val;
            }
        }
        $this->assertSame( 'id_res', $res['openid.mode'] );
        $this->assertSame( 'false', $res['is_valid'] );

        // Wrong arguments (OpenID 2.0)
        $ret = $provider->handle(array('openid_ns'=>OpenId::NS_2_0,
                                       'openid_mode'=>'check_authentication'));
        $res = array();
        foreach (explode("\n", $ret) as $line) {
            if (!empty($line)) {
                list($key, $val) = explode(":", $line, 2);
                $res[$key] = $val;
            }
        }
        $this->assertSame( OpenId::NS_2_0, $res['ns'] );
        $this->assertSame( 'id_res', $res['openid.mode'] );
        $this->assertSame( 'false', $res['is_valid'] );

        // Wrong session id
        $storage->delAssociation(self::HANDLE);
        $ret = $provider->handle(array('openid_mode'=>'check_authentication',
                                       'openid_assoc_handle'=>self::HANDLE));
        $res = array();
        foreach (explode("\n", $ret) as $line) {
            if (!empty($line)) {
                list($key, $val) = explode(":", $line, 2);
                $res[$key] = $val;
            }
        }
        $this->assertSame( 'id_res', $res['openid.mode'] );
        $this->assertSame( 'false', $res['is_valid'] );

        // Proper session signed with HAMC-SHA256
        $storage->addAssociation(self::HANDLE, "sha1", pack("H*", '0102030405060708091011121314151617181920'), time() + 3660);
        $ret = $provider->handle(array('openid_mode'=>'check_authentication',
                                       'openid_assoc_handle'=>self::HANDLE,
                                       'openid_signed'=>'mode,assoc_handle,signed',
                                       'openid_sig'=>'IgLZCOXmEPowYl6yyFZjYL4ZTtQ='));
        $res = array();
        foreach (explode("\n", $ret) as $line) {
            if (!empty($line)) {
                list($key, $val) = explode(":", $line, 2);
                $res[$key] = $val;
            }
        }
        $this->assertSame( 'id_res', $res['openid.mode'] );
        $this->assertSame( 'true', $res['is_valid'] );

        // Proper session signed with HAMC-SHA256
        $storage->delAssociation(self::HANDLE);
        $storage->addAssociation(self::HANDLE, "sha256", pack("H*", '0102030405060708091011121314151617181920212223242526272829303132'), time() + 3660);
        $ret = $provider->handle(array('openid_mode'=>'check_authentication',
                                       'openid_assoc_handle'=>self::HANDLE,
                                       'openid_signed'=>'mode,assoc_handle,signed',
                                       'openid_sig'=>'xoJcXj30L1N7QRir7I2ovop1SaijXnAI97X/yH+kvck='));
        $res = array();
        foreach (explode("\n", $ret) as $line) {
            if (!empty($line)) {
                list($key, $val) = explode(":", $line, 2);
                $res[$key] = $val;
            }
        }
        $this->assertSame( 'id_res', $res['openid.mode'] );
        $this->assertSame( 'true', $res['is_valid'] );

        // Wrong signature
        $storage->delAssociation(self::HANDLE);
        $storage->addAssociation(self::HANDLE, "sha256", pack("H*", '0102030405060708091011121314151617181920212223242526272829303132'), time() + 3660);
        $ret = $provider->handle(array('openid_ns'=>OpenId::NS_2_0,
                                       'openid_mode'=>'check_authentication',
                                       'openid_assoc_handle'=>self::HANDLE,
                                       'openid_signed'=>'ns,mode,assoc_handle,signed',
                                       'openid_sig'=>'xoJcXj30L1N7QRir7I2ovop1SaijXnAI97X/yH+kvck='));
        $res = array();
        foreach (explode("\n", $ret) as $line) {
            if (!empty($line)) {
                list($key, $val) = explode(":", $line, 2);
                $res[$key] = $val;
            }
        }
        $this->assertSame( 'id_res', $res['openid.mode'] );
        $this->assertSame( 'false', $res['is_valid'] );

        $storage->delAssociation(self::HANDLE);
    }

    /**
     * testing respondToConsumer
     *
     */
    public function testRespondToConsumer()
    {
        $this->expectOutputRegex('/.*/'); // Hide stdout from the component when the test run
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $provider = new ProviderHelper(null, null, $this->_user, $storage);

        // dumb mode
        $response = new ResponseHelper(true);
        $storage->delAssociation(self::HANDLE);
        $this->assertTrue( $provider->respondToConsumer(array(
                'openid_assoc_handle' => self::HANDLE,
                'openid_return_to' => 'http://www.test.com/test.php'
            ), null, $response) );
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $ret = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $ret[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $ret['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $ret['openid.return_to'] );
        $this->assertTrue( isset($ret['openid.assoc_handle']) );
        $this->assertTrue( isset($ret['openid.response_nonce']) );
        $this->assertTrue( isset($ret['openid.signed']) );
        $this->assertTrue( isset($ret['openid.sig']) );
        $this->assertTrue( $storage->getAssociation($ret['openid.assoc_handle'], $macFunc, $secret, $expires) );
        $this->assertSame( 'sha1', $macFunc );

        // OpenID 2.0 with SHA256
        $_SERVER['SCRIPT_URI'] = "http://www.test.com/endpoint.php";
        $response = new ResponseHelper(true);
        $storage->addAssociation(self::HANDLE, "sha256", pack("H*", '0102030405060708091011121314151617181920212223242526272829303132'), time() + 3660);
        $this->assertTrue( $provider->respondToConsumer(array(
                'openid_ns' => OpenId::NS_2_0,
                'openid_assoc_handle' => self::HANDLE,
                'openid_return_to' => 'http://www.test.com/test.php'
            ), null, $response) );
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $ret = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $ret[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $ret['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $ret['openid.return_to'] );
        $this->assertSame( self::HANDLE, $ret['openid.assoc_handle'] );
        $this->assertTrue( isset($ret['openid.response_nonce']) );
        $this->assertTrue( isset($ret['openid.signed']) );
        $this->assertTrue( isset($ret['openid.sig']) );
        $this->assertSame( OpenId::NS_2_0, $ret['openid.ns'] );
        $this->assertSame( "http://www.test.com/endpoint.php", $ret['openid.op_endpoint'] );
        $this->assertTrue( $storage->getAssociation(self::HANDLE, $macFunc, $secret, $expires) );
        $this->assertSame( 'sha256', $macFunc );
        $storage->delAssociation(self::HANDLE);

        // OpenID 1.1 with SHA1
        $storage->addAssociation(self::HANDLE, "sha1", pack("H*", '0102030405060708091011121314151617181920'), time() + 3660);
        $response = new ResponseHelper(true);
        $ret = $provider->respondToConsumer(array(
                'openid_assoc_handle' => self::HANDLE,
                'openid_return_to' => 'http://www.test.com/test.php',
                'openid_claimed_id' => 'http://claimed_id/',
                'openid_identity' => 'http://identity/',
                'openid_unknown' => 'http://www.test.com/test.php',
            ), null, $response);
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $ret = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $ret[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $ret['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $ret['openid.return_to'] );
        $this->assertSame( self::HANDLE, $ret['openid.assoc_handle'] );
        $this->assertTrue( isset($ret['openid.response_nonce']) );
        $this->assertTrue( isset($ret['openid.signed']) );
        $this->assertTrue( isset($ret['openid.sig']) );
        $this->assertFalse( isset($ret['openid.ns']) );
        $this->assertFalse( isset($ret['openid.op_endpoint']) );
        $this->assertSame( 'http://claimed_id/', $ret['openid.claimed_id'] );
        $this->assertSame( 'http://identity/', $ret['openid.identity'] );
        $this->assertFalse( isset($ret['openid.unknown']) );
        $this->assertTrue( $storage->getAssociation(self::HANDLE, $macFunc, $secret, $expires) );
        $this->assertSame( 'sha1', $macFunc );
        $storage->delAssociation(self::HANDLE);

        // extensions
        $sreg = new Extension\Sreg(array("nickname"=>"test_id"));
        $response = new ResponseHelper(true);
        $this->assertTrue( $provider->respondToConsumer(array(
                'openid_return_to' => 'http://www.test.com/test.php',
            ), $sreg, $response) );
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $ret = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $ret[$key] = urldecode($val);
        }
        $this->assertSame( 'test_id', $ret['openid.sreg.nickname'] );
    }

    /**
     * testing _checkId
     *
     */
    public function testCheckIdImmediate()
    {
        $this->expectOutputRegex('/.*/'); // Hide stdout from the component when the test run
        $_SERVER['SCRIPT_URI'] = "http://www.test.com/server.php";
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $provider = new ProviderHelper(null, null, $this->_user, $storage);
        $provider->logout();

        // Wrong arguments (no openid.return_to and openid.trust_root)
        $response = new ResponseHelper(true);
        $this->assertFalse( $provider->handle(array(
            'openid_mode'=>'checkid_immediate'),
            null, $response) );

        // Unexistent user
        $storage->delUser(self::USER);
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $this->assertSame( 'http://www.test.com/test.php?openid.mode=cancel', $headers->get('Location')->getFieldValue() );

        // No openid_identity
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $url2 = parse_url($query['openid.user_setup_url']);
        $this->assertSame( 'www.test.com', $url2['host'] );
        $this->assertSame( '/server.php', $url2['path'] );
        $query2 = array();
        foreach (explode('&', $url2['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query2[$key] = urldecode($val);
        }
        $this->assertSame( 'login', $query2['openid.action'] );
        $this->assertSame( 'checkid_setup', $query2['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query2['openid.return_to'] );

        // Non logged in user
        $provider->register(self::USER, self::PASSWORD);
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $url2 = parse_url($query['openid.user_setup_url']);
        $this->assertSame( 'www.test.com', $url2['host'] );
        $this->assertSame( '/server.php', $url2['path'] );
        $query2 = array();
        foreach (explode('&', $url2['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query2[$key] = urldecode($val);
        }
        $this->assertSame( 'login', $query2['openid.action'] );
        $this->assertSame( 'checkid_setup', $query2['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query2['openid.return_to'] );
        $this->assertSame( self::USER, $query2['openid.identity'] );

        // Non logged in user with SREG
        $provider->register(self::USER, self::PASSWORD);
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php',
            'openid_ns_sreg'=>Extension\Sreg::NAMESPACE_1_1,
            'openid_sreg_required'=>'nickname'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $url2 = parse_url($query['openid.user_setup_url']);
        $this->assertSame( 'www.test.com', $url2['host'] );
        $this->assertSame( '/server.php', $url2['path'] );
        $query2 = array();
        foreach (explode('&', $url2['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query2[$key] = urldecode($val);
        }
        $this->assertSame( 'login', $query2['openid.action'] );
        $this->assertSame( 'checkid_setup', $query2['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query2['openid.return_to'] );
        $this->assertSame( self::USER, $query2['openid.identity'] );
        $this->assertSame( Extension\Sreg::NAMESPACE_1_1, $query2['openid.ns.sreg'] );
        $this->assertSame( "nickname", $query2['openid.sreg.required'] );

        // Logged in user (unknown site)
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $url2 = parse_url($query['openid.user_setup_url']);
        $this->assertSame( 'www.test.com', $url2['host'] );
        $this->assertSame( '/server.php', $url2['path'] );
        $query2 = array();
        foreach (explode('&', $url2['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query2[$key] = urldecode($val);
        }
        $this->assertSame( 'trust', $query2['openid.action'] );
        $this->assertSame( 'checkid_setup', $query2['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query2['openid.return_to'] );
        $this->assertSame( self::USER, $query2['openid.identity'] );

        // Logged in user (unknown site 2)
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->allowSite('http://www.test.com/test1.php') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $url2 = parse_url($query['openid.user_setup_url']);
        $this->assertSame( 'www.test.com', $url2['host'] );
        $this->assertSame( '/server.php', $url2['path'] );
        $query2 = array();
        foreach (explode('&', $url2['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query2[$key] = urldecode($val);
        }
        $this->assertSame( 'trust', $query2['openid.action'] );
        $this->assertSame( 'checkid_setup', $query2['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query2['openid.return_to'] );
        $this->assertSame( self::USER, $query2['openid.identity'] );

        // Logged in user (unknown site + SREG)
        $response = new ResponseHelper(true);
        $this->assertTrue( $provider->delSite('http://www.test.com/test1.php') );
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php',
            'openid_ns_sreg'=>Extension\Sreg::NAMESPACE_1_1,
            'openid_sreg_required'=>'nickname'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $url2 = parse_url($query['openid.user_setup_url']);
        $this->assertSame( 'www.test.com', $url2['host'] );
        $this->assertSame( '/server.php', $url2['path'] );
        $query2 = array();
        foreach (explode('&', $url2['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query2[$key] = urldecode($val);
        }
        $this->assertSame( 'trust', $query2['openid.action'] );
        $this->assertSame( 'checkid_setup', $query2['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query2['openid.return_to'] );
        $this->assertSame( self::USER, $query2['openid.identity'] );
        $this->assertSame( Extension\Sreg::NAMESPACE_1_1, $query2['openid.ns.sreg'] );
        $this->assertSame( "nickname", $query2['openid.sreg.required'] );

        // Logged in user (untrusted site)
        $this->assertTrue( $provider->denySite('http://www.test.com') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $this->assertSame( 'http://www.test.com/test.php?openid.mode=cancel', $headers->get('Location')->getFieldValue() );

        // Logged in user (untrusted site with wildcard)
        $this->assertTrue( $provider->delSite('http://www.test.com') );
        $this->assertTrue( $provider->denySite('http://*.test.com') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $this->assertSame( 'http://www.test.com/test.php?openid.mode=cancel', $headers->get('Location')->getFieldValue() );

        // Logged in user (trusted site)
        $this->assertTrue( $provider->delSite('http://*.test.com') );
        $this->assertTrue( $provider->allowSite('http://www.test.com/') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );
        $this->assertSame( self::USER, $query['openid.identity'] );
        $this->assertTrue( isset($query['openid.assoc_handle']) );
        $this->assertTrue( isset($query['openid.response_nonce']) );
        $this->assertTrue( isset($query['openid.signed']) );
        $this->assertTrue( isset($query['openid.sig']) );
        $this->assertSame( 20, strlen(base64_decode($query['openid.sig'])) );

        // Logged in user (trusted site without openid.return_to)
        $this->assertTrue( $provider->allowSite('http://www.test.com/') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_trust_root'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertEquals(0, count($headers));
        $this->assertSame( '', $response->getBody() );

        // Logged in user (trusted site) & OpenID 2.0 & established session
        $storage->delAssociation(self::HANDLE);
        $storage->addAssociation(self::HANDLE, "sha1", pack("H*", '0102030405060708091011121314151617181920'), time() + 3660);
        $this->assertTrue( $provider->allowSite('http://www.test.com/') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_ns'=>OpenId::NS_2_0,
            'openid_assoc_handle'=>self::HANDLE,
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( OpenId::NS_2_0, $query['openid.ns'] );
        $this->assertSame( "http://www.test.com/server.php", $query['openid.op_endpoint'] );
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );
        $this->assertSame( self::USER, $query['openid.identity'] );
        $this->assertSame( self::HANDLE, $query['openid.assoc_handle'] );
        $this->assertTrue( isset($query['openid.response_nonce']) );
        $this->assertTrue( isset($query['openid.signed']) );
        $this->assertTrue( isset($query['openid.sig']) );
        $this->assertSame( 20, strlen(base64_decode($query['openid.sig'])) );

        // Logged in user (trusted site) & invalid association handle
        $storage->delAssociation(self::HANDLE);
        $this->assertTrue( $provider->allowSite('http://www.test.com/') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_ns'=>OpenId::NS_2_0,
            'openid_assoc_handle'=>self::HANDLE,
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( OpenId::NS_2_0, $query['openid.ns'] );
        $this->assertSame( "http://www.test.com/server.php", $query['openid.op_endpoint'] );
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );
        $this->assertSame( self::USER, $query['openid.identity'] );
        $this->assertSame( self::HANDLE, $query['openid.invalidate_handle'] );
        $this->assertTrue( isset($query['openid.assoc_handle']) );
        $this->assertTrue( isset($query['openid.response_nonce']) );
        $this->assertTrue( isset($query['openid.signed']) );
        $this->assertTrue( isset($query['openid.sig']) );
        $this->assertSame( 32, strlen(base64_decode($query['openid.sig'])) );

        // SREG success
        $sreg = new Extension\Sreg(array('nickname'=>'test','email'=>'test@test.com'));
        $this->assertTrue( $provider->allowSite('http://www.test.com/', $sreg) );
        $sreg = new Extension\Sreg();
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_ns'=>OpenId::NS_2_0,
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php',
            'openid_ns_sreg'=>Extension\Sreg::NAMESPACE_1_1,
            'openid_sreg_required'=>'nickname',
            'openid_sreg_optional'=>'email',
            ),
            $sreg, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( OpenId::NS_2_0, $query['openid.ns'] );
        $this->assertSame( "http://www.test.com/server.php", $query['openid.op_endpoint'] );
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );
        $this->assertSame( self::USER, $query['openid.identity'] );
        $this->assertTrue( isset($query['openid.assoc_handle']) );
        $this->assertTrue( isset($query['openid.response_nonce']) );
        $this->assertTrue( isset($query['openid.signed']) );
        $this->assertTrue( isset($query['openid.sig']) );
        $this->assertSame( 32, strlen(base64_decode($query['openid.sig'])) );
        $this->assertSame( Extension\Sreg::NAMESPACE_1_1, $query['openid.ns.sreg'] );
        $this->assertSame( 'test', $query['openid.sreg.nickname'] );
        $this->assertSame( 'test@test.com', $query['openid.sreg.email'] );

        // SREG failed
        $sreg = new Extension\Sreg(array('nickname'=>'test'));
        $this->assertTrue( $provider->allowSite('http://www.test.com/', $sreg) );
        $sreg = new Extension\Sreg();
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_immediate',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php',
            'openid_sreg_required'=>'nickname,email',
            ),
            $sreg, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $url2 = parse_url($query['openid.user_setup_url']);
        $this->assertSame( 'www.test.com', $url2['host'] );
        $this->assertSame( '/server.php', $url2['path'] );
        $query2 = array();
        foreach (explode('&', $url2['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query2[$key] = urldecode($val);
        }
        $this->assertSame( 'trust', $query2['openid.action'] );
        $this->assertSame( 'checkid_setup', $query2['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query2['openid.return_to'] );
        $this->assertSame( self::USER, $query2['openid.identity'] );
        $this->assertSame( "nickname,email", $query2['openid.sreg.required'] );

        $provider->logout();
        $storage->delUser(self::USER);
    }

    /**
     * testing handle
     *
     */
    public function testCheckIdSetup()
    {
        $this->expectOutputRegex('/.*/'); // Hide stdout from the component when the test run
        $_SERVER['SCRIPT_URI'] = "http://www.test.com/server.php";
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $provider = new ProviderHelper(null, null, $this->_user, $storage);
        $provider->logout();

        // Wrong arguments (no openid.return_to and openid.trust_root)
        $response = new ResponseHelper(true);
        $this->assertFalse( $provider->handle(array(
            'openid_mode'=>'checkid_setup'),
            null, $response) );

        // Unexistent user
        $storage->delUser(self::USER);
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_setup',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $this->assertSame( 'http://www.test.com/test.php?openid.mode=cancel', $headers->get('Location')->getFieldValue() );

        // No openid_identity
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_setup',
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/server.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'login', $query['openid.action'] );
        $this->assertSame( 'checkid_setup', $query['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );

        // Non logged in user
        $provider->register(self::USER, self::PASSWORD);
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_setup',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/server.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'login', $query['openid.action'] );
        $this->assertSame( 'checkid_setup', $query['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );
        $this->assertSame( self::USER, $query['openid.identity'] );

        // Logged in user (unknown site)
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_setup',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/server.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'trust', $query['openid.action'] );
        $this->assertSame( 'checkid_setup', $query['openid.mode'] );
        $this->assertSame( self::USER, $query['openid.identity'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );

        // Logged in user (untrusted site)
        $this->assertTrue( $provider->denySite('http://www.test.com/') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_setup',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $this->assertSame( 'http://www.test.com/test.php?openid.mode=cancel', $headers->get('Location')->getFieldValue() );

        // Logged in user (trusted site)
        $this->assertTrue( $provider->allowSite('http://www.test.com/') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_setup',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );
        $this->assertSame( self::USER, $query['openid.identity'] );
        $this->assertTrue( isset($query['openid.assoc_handle']) );
        $this->assertTrue( isset($query['openid.response_nonce']) );
        $this->assertTrue( isset($query['openid.signed']) );
        $this->assertTrue( isset($query['openid.sig']) );
        $this->assertSame( 20, strlen(base64_decode($query['openid.sig'])) );

        // Logged in user (trusted site without openid.return_to)
        $this->assertTrue( $provider->allowSite('http://www.test.com/') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_setup',
            'openid_identity'=>self::USER,
            'openid_trust_root'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertEquals(0, count($headers));
        $this->assertSame( '', $response->getBody() );

        // Logged in user (trusted site) & OpenID 2.0 & established session
        $storage->delAssociation(self::HANDLE);
        $storage->addAssociation(self::HANDLE, "sha1", pack("H*", '0102030405060708091011121314151617181920'), time() + 3660);
        $this->assertTrue( $provider->allowSite('http://www.test.com/') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_ns'=>OpenId::NS_2_0,
            'openid_assoc_handle'=>self::HANDLE,
            'openid_mode'=>'checkid_setup',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( OpenId::NS_2_0, $query['openid.ns'] );
        $this->assertSame( "http://www.test.com/server.php", $query['openid.op_endpoint'] );
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );
        $this->assertSame( self::USER, $query['openid.identity'] );
        $this->assertSame( self::HANDLE, $query['openid.assoc_handle'] );
        $this->assertTrue( isset($query['openid.response_nonce']) );
        $this->assertTrue( isset($query['openid.signed']) );
        $this->assertTrue( isset($query['openid.sig']) );
        $this->assertSame( 20, strlen(base64_decode($query['openid.sig'])) );

        // Logged in user (trusted site) & invalid association handle
        $storage->delAssociation(self::HANDLE);
        $this->assertTrue( $provider->allowSite('http://www.test.com/') );
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_ns'=>OpenId::NS_2_0,
            'openid_assoc_handle'=>self::HANDLE,
            'openid_mode'=>'checkid_setup',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php'),
            null, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( OpenId::NS_2_0, $query['openid.ns'] );
        $this->assertSame( "http://www.test.com/server.php", $query['openid.op_endpoint'] );
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );
        $this->assertSame( self::USER, $query['openid.identity'] );
        $this->assertSame( self::HANDLE, $query['openid.invalidate_handle'] );
        $this->assertTrue( isset($query['openid.assoc_handle']) );
        $this->assertTrue( isset($query['openid.response_nonce']) );
        $this->assertTrue( isset($query['openid.signed']) );
        $this->assertTrue( isset($query['openid.sig']) );
        $this->assertSame( 32, strlen(base64_decode($query['openid.sig'])) );

        // SREG success
        $sreg = new Extension\Sreg(array('nickname'=>'test','email'=>'test@test.com'));
        $this->assertTrue( $provider->allowSite('http://www.test.com/', $sreg) );
        $sreg = new Extension\Sreg();
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_ns'=>OpenId::NS_2_0,
            'openid_mode'=>'checkid_setup',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php',
            'openid_ns_sreg'=>Extension\Sreg::NAMESPACE_1_1,
            'openid_sreg_required'=>'nickname',
            'openid_sreg_optional'=>'email',
            ),
            $sreg, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( OpenId::NS_2_0, $query['openid.ns'] );
        $this->assertSame( "http://www.test.com/server.php", $query['openid.op_endpoint'] );
        $this->assertSame( 'id_res', $query['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );
        $this->assertSame( self::USER, $query['openid.identity'] );
        $this->assertTrue( isset($query['openid.assoc_handle']) );
        $this->assertTrue( isset($query['openid.response_nonce']) );
        $this->assertTrue( isset($query['openid.signed']) );
        $this->assertTrue( isset($query['openid.sig']) );
        $this->assertSame( 32, strlen(base64_decode($query['openid.sig'])) );
        $this->assertSame( Extension\Sreg::NAMESPACE_1_1, $query['openid.ns.sreg'] );
        $this->assertSame( 'test', $query['openid.sreg.nickname'] );
        $this->assertSame( 'test@test.com', $query['openid.sreg.email'] );

        // SREG failed
        $sreg = new Extension\Sreg(array('nickname'=>'test'));
        $this->assertTrue( $provider->allowSite('http://www.test.com/', $sreg) );
        $sreg = new Extension\Sreg();
        $response = new ResponseHelper(true);
        $this->assertTrue($provider->handle(array(
            'openid_mode'=>'checkid_setup',
            'openid_identity'=>self::USER,
            'openid_return_to'=>'http://www.test.com/test.php',
            'openid_sreg_required'=>'nickname,email',
            ),
            $sreg, $response));
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/server.php', $url['path'] );
        $query = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $query[$key] = urldecode($val);
        }
        $this->assertSame( 'trust', $query['openid.action'] );
        $this->assertSame( 'checkid_setup', $query['openid.mode'] );
        $this->assertSame( self::USER, $query['openid.identity'] );
        $this->assertSame( 'http://www.test.com/test.php', $query['openid.return_to'] );
        $this->assertSame( 'nickname,email', $query['openid.sreg.required'] );

        $provider->logout();
        $storage->delUser(self::USER);
    }

    /**
     * testing handle
     *
     */
    public function testHandle()
    {
        $provider = new ProviderHelper(null, null, $this->_user, new Provider\Storage\File(__DIR__."/_files/provider"));

        // no openid_mode
        $this->assertFalse( $provider->handle(array()) );

        // wrong openid_mode
        $this->assertFalse( $provider->handle(array('openid_mode'=>'wrong')) );
    }

    /**
     * testing setOpEndpoint
     *
     */
    public function testSetOpEndpoint()
    {
        $this->expectOutputRegex('/.*/'); // Hide stdout from the component when the test run
        $storage = new Provider\Storage\File(__DIR__."/_files/provider");
        $provider = new ProviderHelper(null, null, $this->_user, $storage);
        $provider->setOpEndpoint("http://www.test.com/real_endpoint.php");

        // OpenID 2.0 with SHA256
        $_SERVER['SCRIPT_URI'] = "http://www.test.com/endpoint.php";
        $response = new ResponseHelper(true);
        $storage->addAssociation(self::HANDLE, "sha256", pack("H*", '0102030405060708091011121314151617181920212223242526272829303132'), time() + 3660);
        $this->assertTrue( $provider->respondToConsumer(array(
                'openid_ns' => OpenId::NS_2_0,
                'openid_assoc_handle' => self::HANDLE,
                'openid_return_to' => 'http://www.test.com/test.php'
            ), null, $response) );
        $headers = $response->getHeaders();
        $this->assertTrue($headers->has('Location'));
        $url = parse_url($headers->get('Location')->getFieldValue());
        $this->assertSame( 'www.test.com', $url['host'] );
        $this->assertSame( '/test.php', $url['path'] );
        $ret = array();
        foreach (explode('&', $url['query']) as $line) {
            list($key,$val) = explode('=', $line, 2);
            $ret[$key] = urldecode($val);
        }
        $this->assertSame( 'id_res', $ret['openid.mode'] );
        $this->assertSame( 'http://www.test.com/test.php', $ret['openid.return_to'] );
        $this->assertSame( self::HANDLE, $ret['openid.assoc_handle'] );
        $this->assertTrue( isset($ret['openid.response_nonce']) );
        $this->assertTrue( isset($ret['openid.signed']) );
        $this->assertTrue( isset($ret['openid.sig']) );
        $this->assertSame( OpenId::NS_2_0, $ret['openid.ns'] );
        $this->assertSame( "http://www.test.com/real_endpoint.php", $ret['openid.op_endpoint'] );
        $this->assertTrue( $storage->getAssociation(self::HANDLE, $macFunc, $secret, $expires) );
        $this->assertSame( 'sha256', $macFunc );
        $storage->delAssociation(self::HANDLE);
    }
}

