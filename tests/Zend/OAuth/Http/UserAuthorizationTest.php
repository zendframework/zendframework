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
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

namespace ZendTest\OAuth\Http;

use Zend\OAuth\Http,
    Zend\OAuth;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @group      Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class UserAuthorizationTest extends \PHPUnit_Framework_TestCase
{

    protected $stubConsumer = null;

    public function setup()
    {
        $this->stubConsumer = new Consumer34879;
    }

    public function testConstructorSetsConsumerInstance()
    {
        $redirect = new Http\UserAuthorization($this->stubConsumer);
        $this->assertInstanceOf('ZendTest\\OAuth\\Http\\Consumer34879', $redirect->getConsumer());
    }

    public function testConstructorSetsCustomServiceParameters()
    {
        $redirect = new Http\UserAuthorization($this->stubConsumer, array(1,2,3));
        $this->assertEquals(array(1,2,3), $redirect->getParameters());
    }

    public function testAssembleParametersReturnsUserAuthorizationParamArray()
    {
        $redirect = new Http\UserAuthorization($this->stubConsumer, array('foo '=>'bar~'));
        $expected = array(
            'oauth_token'=>'1234567890',
            'oauth_callback'=>'http://www.example.com/local',
            'foo '=>'bar~'
        );
        $this->assertEquals($expected, $redirect->assembleParams());
    }

    public function testGetUrlReturnsEncodedQueryStringParamsAppendedToLocalUrl()
    {
        $redirect = new Http\UserAuthorization($this->stubConsumer, array('foo '=>'bar~'));
        $expected =
            'http://www.example.com/authorize?oauth_token=1234567890&oauth_callback=http%3A%2F%2Fwww.example.com%2Flocal&foo%20=bar~';
        $this->assertEquals($expected, $redirect->getUrl());
    }

}

class Consumer34879 extends OAuth\Consumer
{
    public function getUserAuthorizationUrl(){return 'http://www.example.com/authorize';}
    public function getCallbackUrl(){return 'http://www.example.com/local';}
    public function getLastRequestToken(){$r=new Token34879;return $r;}
}
class Token34879
{
    public function getToken(){return '1234567890';}
}
