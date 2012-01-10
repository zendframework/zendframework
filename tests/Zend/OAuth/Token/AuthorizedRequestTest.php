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

namespace ZendTest\OAuth\Token;

use Zend\OAuth\Token\AuthorizedRequest as AuthorizedRequestToken;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @group      Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AuthorizedRequestTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructorSetsInputData()
    {
        $data = array('foo'=>'bar');
        $token = new AuthorizedRequestToken($data);
        $this->assertEquals($data, $token->getData());
    }

    public function testConstructorParsesAccessTokenFromInputData()
    {
        $data = array(
            'oauth_token'=>'jZaee4GF52O3lUb9'
        );
        $token = new AuthorizedRequestToken($data);
        $this->assertEquals('jZaee4GF52O3lUb9', $token->getToken());
    }

    public function testPropertyAccessWorks()
    {
        $data = array(
            'oauth_token'=>'jZaee4GF52O3lUb9'
        );
        $token = new AuthorizedRequestToken($data);
        $this->assertEquals('jZaee4GF52O3lUb9', $token->oauth_token);
    }

    public function testTokenCastsToEncodedQueryString()
    {
        $queryString = 'oauth_token=jZaee4GF52O3lUb9&foo%20=bar~';
        $token = new AuthorizedRequestToken();
        $token->setToken('jZaee4GF52O3lUb9');
        $token->setParam('foo ', 'bar~');
        $this->assertEquals($queryString, (string) $token);
    }

    public function testToStringReturnsEncodedQueryString()
    {
        $queryString = 'oauth_token=jZaee4GF52O3lUb9';
        $token = new AuthorizedRequestToken();
        $token->setToken('jZaee4GF52O3lUb9');
        $this->assertEquals($queryString, $token->toString());
    }

    public function testIsValidDetectsBadResponse()
    {
        $data = array(
            'missing_oauth_token'=>'jZaee4GF52O3lUb9'
        );
        $token = new AuthorizedRequestToken($data);
        $this->assertFalse($token->isValid());
    }

    public function testIsValidDetectsGoodResponse()
    {
        $data = array(
            'oauth_token'=>'jZaee4GF52O3lUb9',
            'foo'=>'bar'
        );
        $token = new AuthorizedRequestToken($data);
        $this->assertTrue($token->isValid());
    }
}
