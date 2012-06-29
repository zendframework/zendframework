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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Technorati;

use DateTime;
use Zend\Service\Technorati;

/**
 * Test helper
 */

/**
 * @see Technorati\Weblog
 */


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class WeblogTest extends TestCase
{
    public function setUp()
    {
        $this->domElement = self::getTestFileElementAsDom('TestWeblog.xml', '//weblog');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\Weblog', array($this->domElement));
    }

    public function testWeblog()
    {
        $weblog = new Technorati\Weblog($this->domElement);

        // check name
        $this->assertEquals('Roby Web World Italia', $weblog->getName());
        // check URL
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://robyww.blogspot.com'), $weblog->getUrl());
        // check Atom Url
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://robyww.blogspot.com/feeds/posts/atom'), $weblog->getAtomUrl());
        // check RSS Url
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://robyww.blogspot.com/feeds/posts/rss'), $weblog->getRssUrl());
        // check inbound blogs
        $this->assertEquals(71, $weblog->getInboundBlogs());
        // check inbound links
        $this->assertEquals(103, $weblog->getInboundLinks());
        // check last update
        $this->assertEquals(new DateTime('2007-11-11 08:47:26 GMT'), $weblog->getLastUpdate());
        // check rank
        $this->assertEquals(93473, $weblog->getRank());
        // check authors
        $var = $weblog->getAuthors();
        $this->assertInternalType('array', $var);
        $this->assertEquals(1, sizeof($var));
        // check photo
        $this->assertEquals(false, $weblog->hasPhoto());
        // check lat and lon
        $this->assertNull($weblog->getLat());
        $this->assertNull($weblog->getLon());
    }

    public function testWeblogWithTwoAuthors()
    {
        $domElement = self::getTestFileElementAsDom('TestWeblogTwoAuthors.xml', '//weblog');
        $weblog = new Technorati\Weblog($domElement);

        $authors = $weblog->getAuthors();

        // check whether $authors is an array with valid length
        $this->assertInternalType('array', $authors);
        $this->assertEquals(2, sizeof($authors));

        // check first author
        $author = $authors[0];
        $this->assertInstanceOf('Zend\Service\Technorati\Author', $author);
        $this->assertEquals('rfilippini', $author->getUsername());

        // check second author, be sure it's not the first one
        $author = $authors[1];
        $this->assertInstanceOf('Zend\Service\Technorati\Author', $author);
        $this->assertEquals('Rinzi', $author->getUsername());
    }

    public function testSetGet()
    {
        $weblog = new Technorati\Weblog($this->domElement);

        // check name
        $set = 'foo';
        $get = $weblog->setName($set)->getName();
        $this->assertInternalType('string', $get);
        $this->assertEquals($set, $get);

        // check URL

        $set = \Zend\Uri\UriFactory::factory('http://www.simonecarletti.com/');
        $get = $weblog->setUrl($set)->getUrl();
        $this->assertInstanceOf('Zend\Uri\Http', $get);
        $this->assertEquals($set, $get);

        $set = 'http://www.simonecarletti.com/';
        $get = $weblog->setUrl($set)->getUrl();
        $this->assertInstanceOf('Zend\Uri\Http', $get);
        $this->assertEquals(\Zend\Uri\UriFactory::factory($set), $get);

        // check Atom URL

        $set = \Zend\Uri\UriFactory::factory('http://www.simonecarletti.com/');
        $get = $weblog->setAtomUrl($set)->getAtomUrl();
        $this->assertInstanceOf('Zend\Uri\Http', $get);
        $this->assertEquals($set, $get);

        $set = 'http://www.simonecarletti.com/';
        $get = $weblog->setAtomUrl($set)->getAtomUrl();
        $this->assertInstanceOf('Zend\Uri\Http', $get);
        $this->assertEquals(\Zend\Uri\UriFactory::factory($set), $get);

        // check RSS Url

        $set = \Zend\Uri\UriFactory::factory('http://www.simonecarletti.com/');
        $get = $weblog->setRssUrl($set)->getRssUrl();
        $this->assertInstanceOf('Zend\Uri\Http', $get);
        $this->assertEquals($set, $get);

        $set = 'http://www.simonecarletti.com/';
        $get = $weblog->setRssUrl($set)->getRssUrl();
        $this->assertInstanceOf('Zend\Uri\Http', $get);
        $this->assertEquals(\Zend\Uri\UriFactory::factory($set), $get);

        // check inbound blogs

        $set = rand();
        $get = $weblog->setInboundBlogs($set)->getInboundBlogs();
        $this->assertInternalType('integer', $get);
        $this->assertEquals($set, $get);

        $set = (string) rand();
        $get = $weblog->setInboundBlogs($set)->getInboundBlogs();
        $this->assertInternalType('integer', $get);
        $this->assertEquals((int) $set, $get);

        // check inbound links

        $set = rand();
        $get = $weblog->setInboundLinks($set)->getInboundLinks();
        $this->assertInternalType('integer', $get);
        $this->assertEquals((int) $set, $get);

        $set = (string) rand();
        $get = $weblog->setInboundLinks($set)->getInboundLinks();
        $this->assertInternalType('integer', $get);
        $this->assertEquals((int) $set, $get);

        // last update

        $set = '2007-11-11 08:47:26 GMT';
        $get = $weblog->setLastUpdate($set)->getLastUpdate();
        $this->assertInstanceOf('DateTime', $get);
        $this->assertEquals(new DateTime($set), $get);

        /* not supported
        $set = time();
        $get = $weblog->setLastUpdate($set)->getLastUpdate();
        $this->assertInternalType('integer', $get);
        $this->assertEquals($set, $get); */

        $set = '200ty';
        try {
            $weblog->setLastUpdate($set);
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch(\Exception $e) {
            $this->assertContains('DateTime', $e->getMessage());
        }

        // check rank

        $set = rand();
        $get = $weblog->setRank($set)->getRank();
        $this->assertInternalType('integer', $get);
        $this->assertEquals((int) $set, $get);

        $set = (string) rand();
        $get = $weblog->setRank($set)->getRank();
        $this->assertInternalType('integer', $get);
        $this->assertEquals((int) $set, $get);

        // check hasPhoto

        $set = false;
        $get = $weblog->setHasPhoto($set)->hasPhoto();
        $this->assertInternalType('boolean', $get);
        $this->assertEquals($set, $get);

        $set = 0;
        $get = $weblog->setHasPhoto($set)->hasPhoto();
        $this->assertInternalType('boolean', $get);
        $this->assertEquals((bool) $set, $get);

        // check lat

        $set = 1.3;
        $get = $weblog->setLat($set)->getLat();
        $this->assertInternalType('float', $get);
        $this->assertEquals($set, $get);

        $set = '1.3';
        $get = $weblog->setLat($set)->getLat();
        $this->assertInternalType('float', $get);
        $this->assertEquals((float) $set, $get);

        // check lon

        $set = 1.3;
        $get = $weblog->setLon($set)->getLon();
        $this->assertInternalType('float', $get);
        $this->assertEquals($set, $get);

        $set = '1.3';
        $get = $weblog->setLon($set)->getLon();
        $this->assertInternalType('float', $get);
        $this->assertEquals((float) $set, $get);
    }

    public function testSettingInvalidUrlShouldRaiseException()
    {
        $this->markTestIncomplete('Uri::isValid() does not do complete URI validation yet');

        $weblog = new Technorati\Weblog($this->domElement);

        $set = 'http:::/foo';
        $this->setExpectedException('Zend\Service\Technorati\Exception\RuntimeException', 'invalid URI');
        $weblog->setUrl($set);
    }

    public function testSettingInvalidAtomUrlShouldRaiseException()
    {
        $this->markTestIncomplete('Uri::isValid() does not do complete URI validation yet');

        $weblog = new Technorati\Weblog($this->domElement);

        $set = 'http:::/foo';
        $this->setExpectedException('Zend\Service\Technorati\Exception\RuntimeException', 'invalid URI');
        $weblog->setAtomUrl($set);
    }

    public function testSettingInvalidRssUrlShouldRaiseException()
    {
        $this->markTestIncomplete('Uri::isValid() does not do complete URI validation yet');

        $weblog = new Technorati\Weblog($this->domElement);

        $set = 'http:::/foo';
        $this->setExpectedException('Zend\Service\Technorati\Exception\RuntimeException', 'invalid URI');
        $weblog->setRssUrl($set);
    }
}
