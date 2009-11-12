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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

/**
 * @see Zend_Service_Technorati_Author
 */
require_once 'Zend/Service/Technorati/Author.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class Zend_Service_Technorati_AuthorTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->domElement = self::getTestFileElementAsDom('TestAuthor.xml', '//author');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_Author', array($this->domElement));
    }

    public function testConstructThrowsExceptionWithInvalidDom()
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_Author', 'DOMElement');
    }

    public function testAuthor()
    {
        $author = new Zend_Service_Technorati_Author($this->domElement);

        $this->assertType('string', $author->getFirstName());
        $this->assertEquals('Cesare', $author->getFirstName());

        $this->assertType('string', $author->getLastName());
        $this->assertEquals('Lamanna', $author->getLastName());

        $this->assertType('string', $author->getUsername());
        $this->assertEquals('cesarehtml', $author->getUsername());

        $this->assertType('string', $author->getDescription());
        $this->assertEquals('This is a description.', $author->getDescription());

        $this->assertType('string', $author->getFirstName());
        $this->assertEquals('This is a bio.', $author->getBio());

        $this->assertType('Zend_Uri_Http', $author->getThumbnailPicture());
        $this->assertEquals(Zend_Uri::factory('http://static.technorati.com/progimages/photo.jpg?uid=117217'), $author->getThumbnailPicture());
    }

    public function testSetGet()
    {
        $author = new Zend_Service_Technorati_Author($this->domElement);

        // check first name
        $set = 'first';
        $get = $author->setFirstName($set)->getFirstName();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);

        // check last name
        $set = 'last';
        $get = $author->setLastName($set)->getLastName();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);

        // check username
        $set = 'user';
        $get = $author->setUsername($set)->getUsername();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);

        // check description
        $set = 'desc';
        $get = $author->setUsername($set)->getUsername();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);

        // check bio
        $set = 'biography';
        $get = $author->setBio($set)->getBio();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);

        // check thubmnail picture

        $set = Zend_Uri::factory('http://www.simonecarletti.com/');
        $get = $author->setThumbnailPicture($set)->getThumbnailPicture();
        $this->assertType('Zend_Uri_Http', $get);
        $this->assertEquals($set, $get);

        $set = 'http://www.simonecarletti.com/';
        $get = $author->setThumbnailPicture($set)->getThumbnailPicture();
        $this->assertType('Zend_Uri_Http', $get);
        $this->assertEquals(Zend_Uri::factory($set), $get);

        $set = 'http:::/foo';
        try {
            $author->setThumbnailPicture($set);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch(Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Invalid URI", $e->getMessage());
        }
    }
}
