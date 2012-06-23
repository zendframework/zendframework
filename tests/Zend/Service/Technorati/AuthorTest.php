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
use Zend\Service\Technorati;

/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class AuthorTest extends TestCase
{
    public function setUp()
    {
        $this->domElement = self::getTestFileElementAsDom('TestAuthor.xml', '//author');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\Author', array($this->domElement));
    }

    public function testAuthor()
    {
        $author = new Technorati\Author($this->domElement);

        $this->assertInternalType('string', $author->getFirstName());
        $this->assertEquals('Cesare', $author->getFirstName());

        $this->assertInternalType('string', $author->getLastName());
        $this->assertEquals('Lamanna', $author->getLastName());

        $this->assertInternalType('string', $author->getUsername());
        $this->assertEquals('cesarehtml', $author->getUsername());

        $this->assertInternalType('string', $author->getDescription());
        $this->assertEquals('This is a description.', $author->getDescription());

        $this->assertInternalType('string', $author->getFirstName());
        $this->assertEquals('This is a bio.', $author->getBio());

        $this->assertInstanceOf('Zend\Uri\Http', $author->getThumbnailPicture());
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://static.technorati.com/progimages/photo.jpg?uid=117217'), $author->getThumbnailPicture());
    }

    public function testSetGet()
    {
        $author = new Technorati\Author($this->domElement);

        // check first name
        $set = 'first';
        $get = $author->setFirstName($set)->getFirstName();
        $this->assertInternalType('string', $get);
        $this->assertEquals($set, $get);

        // check last name
        $set = 'last';
        $get = $author->setLastName($set)->getLastName();
        $this->assertInternalType('string', $get);
        $this->assertEquals($set, $get);

        // check username
        $set = 'user';
        $get = $author->setUsername($set)->getUsername();
        $this->assertInternalType('string', $get);
        $this->assertEquals($set, $get);

        // check description
        $set = 'desc';
        $get = $author->setUsername($set)->getUsername();
        $this->assertInternalType('string', $get);
        $this->assertEquals($set, $get);

        // check bio
        $set = 'biography';
        $get = $author->setBio($set)->getBio();
        $this->assertInternalType('string', $get);
        $this->assertEquals($set, $get);

        // check thubmnail picture

        $set = \Zend\Uri\UriFactory::factory('http://www.simonecarletti.com/');
        $get = $author->setThumbnailPicture($set)->getThumbnailPicture();
        $this->assertInstanceOf('Zend\Uri\Http', $get);
        $this->assertEquals($set, $get);

        $set = 'http://www.simonecarletti.com/';
        $get = $author->setThumbnailPicture($set)->getThumbnailPicture();
        $this->assertInstanceOf('Zend\Uri\Http', $get);
        $this->assertEquals(\Zend\Uri\UriFactory::factory($set), $get);

    }

    public function testShouldRaiseExceptionIfUrlIsInvalid()
    {
        $this->markTestIncomplete('Uri::isValid() does not do complete URI validation yet');

        $author = new Technorati\Author($this->domElement);

        $set = 'http:::/foo';
        $this->setExpectedException('Zend\Service\Technorati\Exception\RuntimeException', 'invalid URI');
        $author->setThumbnailPicture($set);
    }
}
