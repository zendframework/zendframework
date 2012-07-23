<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OpenId
 */

namespace ZendTest\OpenId\Extension;

use Zend\OpenId\OpenId;
use Zend\OpenId\Extension;


/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @group      Zend_OpenId
 */
class ExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * testing forAll
     *
     */
    public function testForAll()
    {
        $params = array();
        $this->assertTrue( Extension\AbstractExtension::forAll(null, 'getTrustData', $params) );
        $this->assertSame( array(), $params );

        $params = array();
        $this->assertTrue( Extension\AbstractExtension::forAll(array(), 'getTrustData', $params) );
        $this->assertSame( array(), $params );

        $params = array();
        $this->assertFalse( Extension\AbstractExtension::forAll(array(1), 'getTrustData', $params) );

        $params = array();
        $this->assertFalse( Extension\AbstractExtension::forAll(new \stdClass(), 'getTrustData', $params) );

        $ext = new Extension\Sreg();
        $params = array();
        $this->assertTrue( Extension\AbstractExtension::forAll($ext, 'getTrustData', $params) );
        $this->assertSame( array('Zend\OpenId\Extension\Sreg'=>array()), $params );

        $ext = new Extension\Sreg();
        $params = array();
        $this->assertTrue( Extension\AbstractExtension::forAll(array($ext), 'getTrustData', $params) );
        $this->assertSame( array('Zend\OpenId\Extension\Sreg'=>array()), $params );

        $ext = new ExtensionHelper();
        $params = array();
        $this->assertTrue( Extension\AbstractExtension::forAll(array($ext), 'getTrustData', $params) );
        $this->assertSame( array(), $params );
        $this->assertFalse( Extension\AbstractExtension::forAll(array($ext), 'wrong', $params) );
        $this->assertSame( array(), $params );
    }

    /**
     * testing extension callbacks
     *
     */
    public function testCallbacks()
    {
        $ext = new ExtensionHelper();
        $a = array();
        $this->assertTrue( $ext->prepareRequest($a) );
        $this->assertSame( array(), $a );
        $this->assertTrue( $ext->parseRequest($a) );
        $this->assertSame( array(), $a );
        $this->assertTrue( $ext->prepareResponse($a) );
        $this->assertSame( array(), $a );
        $this->assertTrue( $ext->parseResponse($a) );
        $this->assertSame( array(), $a );
        $this->assertTrue( $ext->getTrustData($a) );
        $this->assertSame( array(), $a );
        $this->assertTrue( $ext->checkTrustData($a) );
        $this->assertSame( array(), $a );
        $this->assertFalse( $ext->wrong($a) );
        $this->assertSame( array(), $a );
    }
}

class ExtensionHelper extends Extension\AbstractExtension
{
    public function wrong($data)
    {
        return false;
    }
}
