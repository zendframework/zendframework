<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Navigation
 */

namespace ZendTest\Navigation\Page;

use Zend\Navigation\Page;
use Zend\Navigation;

/**
 * Tests the class Zend_Navigation_Page_Uri
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage UnitTests
 * @group      Zend_Navigation
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    public function testUriOptionAsString()
    {
        $page = new Page\Uri(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $this->assertEquals('#', $page->getUri());
    }

    public function testUriOptionAsNull()
    {
        $page = new Page\Uri(array(
            'label' => 'foo',
            'uri' => null
        ));

        $this->assertNull($page->getUri(), 'getUri() should return null');
    }

    public function testUriOptionAsInteger()
    {
        try {
            $page = new Page\Uri(array('uri' => 1337));
            $this->fail('An invalid \'uri\' was given, but ' .
                        'a Zend\Navigation\Exception\InvalidArgumentException was not thrown');
        } catch (Navigation\Exception\InvalidArgumentException $e) {

        }
    }

    public function testUriOptionAsObject()
    {
        try {
            $uri = new \stdClass();
            $uri->foo = 'bar';

            $page = new Page\Uri(array('uri' => $uri));
            $this->fail('An invalid \'uri\' was given, but ' .
                        'a Zend\Navigation\Exception\InvalidArgumentException was not thrown');
        } catch (Navigation\Exception\InvalidArgumentException $e) {

        }
    }

    public function testSetAndGetUri()
    {
        $page = new Page\Uri(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $page->setUri('http://www.example.com/')->setUri('about:blank');

        $this->assertEquals('about:blank', $page->getUri());
    }

    public function testGetHref()
    {
        $uri = 'spotify:album:4YzcWwBUSzibRsqD9Sgu4A';

        $page = new Page\Uri();
        $page->setUri($uri);

        $this->assertEquals($uri, $page->getHref());
    }

    /**
     * @group ZF-8922
     */
    public function testGetHrefWithFragmentIdentifier()
    {
        $uri = 'http://www.example.com/foo.html';

        $page = new Page\Uri();
        $page->setUri($uri);
        $page->setFragment('bar');

        $this->assertEquals($uri . '#bar', $page->getHref());

        $page->setUri('#');

        $this->assertEquals('#bar', $page->getHref());
    }
}
