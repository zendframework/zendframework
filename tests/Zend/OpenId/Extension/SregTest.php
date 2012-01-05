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

/**
 * @namespace
 */
namespace ZendTest\OpenId;

use Zend\OpenId\OpenId,
    Zend\OpenId\Extension;


/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_OpenId
 */
class SregTest extends \PHPUnit_Framework_TestCase
{
    const USER = "test_user";
    const EMAIL = "user@test.com";
    const POLICY = "http://www.somewhere.com/policy.html";

    /**
     * testing getProperties
     *
     */
    public function testGetProperties()
    {
        $ext = new Extension\Sreg();
        $this->assertSame( array(), $ext->getProperties() );
        $ext = new Extension\Sreg(array('nickname'=>true,'email'=>false));
        $this->assertSame( array('nickname'=>true,'email'=>false), $ext->getProperties() );
    }

    /**
     * testing getPolicyUrl
     *
     */
    public function testGetPolicyUrl()
    {
        $ext = new Extension\Sreg();
        $this->assertSame( null, $ext->getPolicyUrl() );
        $ext = new Extension\Sreg(null, self::POLICY);
        $this->assertSame( self::POLICY, $ext->getPolicyUrl() );
    }

    /**
     * testing getVersion
     *
     */
    public function testGetVersion()
    {
        $ext = new Extension\Sreg();
        $this->assertSame( 1.0, $ext->getVersion() );
        $ext = new Extension\Sreg(null, null, 1.1);
        $this->assertSame( 1.1, $ext->getVersion() );
    }

    /**
     * testing getSregProperties
     *
     */
    public function testGetSregProperties()
    {
        $this->assertSame(
            array(
                "nickname",
                "email",
                "fullname",
                "dob",
                "gender",
                "postcode",
                "country",
                "language",
                "timezone"
            ),
            Extension\Sreg::getSregProperties() );
    }

    /**
     * testing prepareRequest
     *
     */
    public function testPrepareRequest()
    {
        $ext = new Extension\Sreg();
        $params = array();
        $this->assertTrue( $ext->prepareRequest($params) );
        $this->assertSame( array(), $params );
        $ext = new Extension\Sreg(array("nickname"=>true,"email"=>false));
        $params = array();
        $this->assertTrue( $ext->prepareRequest($params) );
        $this->assertSame( array('openid.sreg.required'=>"nickname", 'openid.sreg.optional'=>"email"), $params );
        $ext = new Extension\Sreg(array("nickname"=>true,"email"=>true), self::POLICY);
        $params = array();
        $this->assertTrue( $ext->prepareRequest($params) );
        $this->assertSame( array('openid.sreg.required'=>"nickname,email", 'openid.sreg.policy_url' => self::POLICY), $params );
        $ext = new Extension\Sreg(array("nickname"=>false,"email"=>false), self::POLICY, 1.1);
        $params = array();
        $this->assertTrue( $ext->prepareRequest($params) );
        $this->assertSame( array('openid.ns.sreg'=>"http://openid.net/extensions/sreg/1.1",'openid.sreg.optional'=>"nickname,email", 'openid.sreg.policy_url' => self::POLICY), $params );
    }

    /**
     * testing parseRequest
     *
     */
    public function testParseRequest()
    {
        $ext = new Extension\Sreg();

        $this->assertTrue( $ext->parseRequest(array()) );
        $this->assertSame( array(), $ext->getProperties() );
        $this->assertSame( null, $ext->getPolicyUrl() );
        $this->assertSame( 1.0, $ext->getVersion() );

        $this->assertTrue( $ext->parseRequest(array('openid_sreg_required'=>"nickname", 'openid_sreg_optional'=>"email")) );
        $this->assertSame( array('nickname'=>true,'email'=>false), $ext->getProperties() );
        $this->assertSame( null, $ext->getPolicyUrl() );
        $this->assertSame( 1.0, $ext->getVersion() );

        $this->assertTrue( $ext->parseRequest(array('openid_sreg_required'=>"nickname,email", 'openid_sreg_policy_url' => self::POLICY)) );
        $this->assertSame( array('nickname'=>true,'email'=>true), $ext->getProperties() );
        $this->assertSame( self::POLICY, $ext->getPolicyUrl() );
        $this->assertSame( 1.0, $ext->getVersion() );

        $this->assertTrue( $ext->parseRequest(array('openid_ns_sreg'=>"http://openid.net/extensions/sreg/1.1", 'openid_sreg_optional'=>"nickname,email", 'openid_sreg_policy_url' => self::POLICY)) );
        $this->assertSame( array('nickname'=>false,'email'=>false), $ext->getProperties() );
        $this->assertSame( self::POLICY, $ext->getPolicyUrl() );
        $this->assertSame( 1.1, $ext->getVersion() );
    }

    /**
     * testing getTrustData
     *
     */
    public function testGetTrustData()
    {
        $ext = new Extension\Sreg();
        $data = array();
        $this->assertTrue( $ext->getTrustData($data) );
        $this->assertSame( 1, count($data) );
        $this->assertSame( array(), $data["Zend\OpenId\Extension\Sreg"] );
        $ext = new Extension\Sreg(array('nickname'=>true,'email'=>false));
        $data = array();
        $this->assertTrue( $ext->getTrustData($data) );
        $this->assertSame( 1, count($data) );
        $this->assertSame( array('nickname'=>true,'email'=>false), $data["Zend\OpenId\Extension\Sreg"] );
    }

    /**
     * testing checkTrustData
     *
     */
    public function testCheckTrustData()
    {
        $ext = new Extension\Sreg();
        $this->assertTrue( $ext->checkTrustData(array()) );
        $this->assertSame( array(), $ext->getProperties() );

        $ext = new Extension\Sreg();
        $this->assertTrue( $ext->checkTrustData(array("Zend\OpenId\Extension\Sreg"=>array())) );
        $this->assertSame( array(), $ext->getProperties() );

        $ext = new Extension\Sreg(array());
        $this->assertTrue( $ext->checkTrustData(array("Zend\OpenId\Extension\Sreg"=>array("nickname"=>self::USER, "email"=>self::EMAIL))) );
        $this->assertSame( array(), $ext->getProperties() );

        $ext = new Extension\Sreg(array("nickname"=>true,"email"=>true));
        $this->assertTrue( $ext->checkTrustData(array("Zend\OpenId\Extension\Sreg"=>array("nickname"=>self::USER, "email"=>self::EMAIL))) );
        $this->assertSame( array('nickname'=>self::USER, "email"=>self::EMAIL), $ext->getProperties() );

        $ext = new Extension\Sreg(array("nickname"=>true,"email"=>true));
        $this->assertFalse( $ext->checkTrustData(array("Zend\OpenId\Extension\Sreg"=>array("nickname"=>self::USER))) );

        $ext = new Extension\Sreg(array("nickname"=>true,"email"=>false));
        $this->assertTrue( $ext->checkTrustData(array("Zend\OpenId\Extension\Sreg"=>array("nickname"=>self::USER))) );
        $this->assertSame( array('nickname'=>self::USER), $ext->getProperties() );

        $ext = new Extension\Sreg(array("nickname"=>false,"email"=>true));
        $this->assertTrue( $ext->checkTrustData(array("Zend\OpenId\Extension\Sreg"=>array("nickname"=>self::USER, "email"=>self::EMAIL))) );
        $this->assertSame( array('nickname'=>self::USER, "email"=>self::EMAIL), $ext->getProperties() );

        $ext = new Extension\Sreg(array("nickname"=>false,"email"=>true));
        $this->assertFalse( $ext->checkTrustData(array("Zend\OpenId\Extension\SregX"=>array("nickname"=>self::USER, "email"=>self::EMAIL))) );
    }

    /**
     * testing prepareResponse
     *
     */
    public function testPrepareResponse()
    {
        $ext = new Extension\Sreg();
        $params = array();
        $this->assertTrue( $ext->prepareResponse($params) );
        $this->assertSame( array(), $params );

        $ext = new Extension\Sreg(array('nickname'=>self::USER, "email"=>self::EMAIL), self::POLICY);
        $params = array();
        $this->assertTrue( $ext->prepareResponse($params) );
        $this->assertSame( array('openid.sreg.nickname'=>self::USER, 'openid.sreg.email'=>self::EMAIL), $params );

        $ext = new Extension\Sreg(array('nickname'=>self::USER, "email"=>self::EMAIL), self::POLICY, 1.1);
        $params = array();
        $this->assertTrue( $ext->prepareResponse($params) );
        $this->assertSame( array('openid.ns.sreg'=>"http://openid.net/extensions/sreg/1.1", 'openid.sreg.nickname'=>self::USER, 'openid.sreg.email'=>self::EMAIL), $params );
    }

    /**
     * testing parseResponse
     *
     */
    public function testParseResponse()
    {
        $ext = new Extension\Sreg();

        $this->assertTrue( $ext->parseResponse(array()) );
        $this->assertSame( array(), $ext->getProperties() );
        $this->assertSame( null, $ext->getPolicyUrl() );
        $this->assertSame( 1.0, $ext->getVersion() );

        $this->assertTrue( $ext->parseResponse(array('openid_sreg_nickname'=>self::USER, 'openid_sreg_email'=>self::EMAIL)) );
        $this->assertSame( array('nickname'=>self::USER,'email'=>self::EMAIL), $ext->getProperties() );
        $this->assertSame( null, $ext->getPolicyUrl() );
        $this->assertSame( 1.0, $ext->getVersion() );

        $this->assertTrue( $ext->parseResponse(array('openid_sreg_nickname'=>self::USER, 'openid_sreg_email'=>self::EMAIL, 'openid_sreg_policy_url' => self::POLICY)) );
        $this->assertSame( array('nickname'=>self::USER,'email'=>self::EMAIL), $ext->getProperties() );
        $this->assertSame( null, $ext->getPolicyUrl() );
        $this->assertSame( 1.0, $ext->getVersion() );

        $this->assertTrue( $ext->parseResponse(array('openid_ns_sreg'=>"http://openid.net/extensions/sreg/1.1",'openid_sreg_nickname'=>self::USER, 'openid_sreg_email'=>self::EMAIL)) );
        $this->assertSame( array('nickname'=>self::USER,'email'=>self::EMAIL), $ext->getProperties() );
        $this->assertSame( null, $ext->getPolicyUrl() );
        $this->assertSame( 1.1, $ext->getVersion() );
    }
}
