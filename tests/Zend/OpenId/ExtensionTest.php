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
namespace ZendTest\OpenId\Extension;

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
    function wrong($data)
    {
        return false;
    }
}
