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

namespace ZendTest\OAuth;

use Zend\OAuth,
    Zend\Config\Config,
    Zend\Http\Client;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @group      Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class OAuthTest extends \PHPUnit_Framework_TestCase
{

    public function teardown()
    {
        OAuth\OAuth::clearHttpClient();
    }

    public function testCanSetCustomHttpClient()
    {
        OAuth\OAuth::setHttpClient(new HTTPClient19485876());
        $this->assertInstanceOf('ZendTest\\OAuth\\HttpClient19485876', OAuth\OAuth::getHttpClient());
    }

    public function testGetHttpClientResetsParameters()
    {
        $client = new HTTPClient19485876();
        $client->setParameterGet(array('key'=>'value'));
        OAuth\OAuth::setHttpClient($client);
        $resetClient = OAuth\OAuth::getHttpClient();
        $resetClient->setUri('http://www.example.com');
        $this->assertEquals('http://www.example.com', (string) $resetClient->getUri(true));
    }

    public function testGetHttpClientResetsAuthorizationHeader()
    {
        $client = new HTTPClient19485876();
        $client->setHeaders(array('Authorization' => 'realm="http://www.example.com",oauth_version="1.0"'));
        OAuth\OAuth::setHttpClient($client);
        $resetClient = OAuth\OAuth::getHttpClient();
        $this->assertEquals(null, $resetClient->getHeader('Authorization'));
    }

    /**
     * @group ZF-10182
     */
    public function testOauthClientPassingObjectConfigInConstructor()
    {
        $options = array(
            'requestMethod' => 'GET',
            'siteUrl'       => 'http://www.example.com'
        );

        $config = new Config($options);
        $client = new OAuth\Client($config);
        $this->assertEquals('GET', $client->getRequestMethod());
        $this->assertEquals('http://www.example.com', $client->getSiteUrl());
    }

    /**
     * @group ZF-10182
     */
    public function testOauthClientPassingArrayInConstructor()
    {
        $options = array(
            'requestMethod' => 'GET',
            'siteUrl'       => 'http://www.example.com'
        );

        $client = new OAuth\Client($options);
        $this->assertEquals('GET', $client->getRequestMethod());
        $this->assertEquals('http://www.example.com', $client->getSiteUrl());
    }
}

class HTTPClient19485876 extends \Zend\Http\Client {}
