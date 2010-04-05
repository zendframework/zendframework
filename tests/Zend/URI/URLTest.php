<?php 

namespace ZendTest\URL;
use Zend\URI\URL;

class URLTest extends \PHPUnit_Framework_TestCase
{
    

    /**
     * Tests for proper URI decomposition
     */
    public function testSimple()
    {
        $url = new URL('http://www.zend.com');
        $this->assertTrue($url->isValid());
        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('www.zend.com', $url->getHost());
    }

    /**
     * Test that fromString() works proprerly for simple valid URLs
     *
     */
    public function testURLReturnsProperURL()
    {
        $tests = array(
            'http://www.zend.com',
            'https://www.zend.com',
            'http://www.zend.com/path',
            'http://www.zend.com/path?query=value'
        );

        foreach ($tests as $testURL) {
            $obj = new URL($testURL);
            $this->assertEquals($testURL, $obj->generate(),
                "getUri() returned value that differs from input for $testURL");
        }
    }

    public function testAllParts()
    {
        $url = new URL('http://andi:password@www.zend.com:8080/path/to/file?a=1&b=2#top');
        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('andi', $url->getUsername());
        $this->assertEquals('password', $url->getPassword());
        $this->assertEquals('www.zend.com', $url->getHost());
        $this->assertEquals('8080', $url->getPort());
        $this->assertEquals('/path/to/file', $url->getPath());
        $this->assertEquals('a=1&b=2', $url->getQuery());
        $this->assertEquals('top', $url->getFragment());
        $this->assertTrue($url->isValid());
    }

    public function testURLSupportsVariousCharacters()
    {
        $urlTarget = 'http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com';
        $url = new URL($urlTarget);
        $this->assertEquals($urlTarget, $url->generate());
    }

    public function testInvalidCharacter()
    {
        $url = new URL('http://an`di:password@www.zend.com');
        $this->assertFalse($url->isValid());
    }

    public function testSquareBrackets()
    {
        $url = new URL('https://example.com/foo/?var[]=1&var[]=2&some[thing]=3');
        $this->assertEquals('var[]=1&var[]=2&some[thing]=3', $url->getQuery());
        
        $targetArray = array (
            'var' => array ('1', '2'),
            'some' => array ('thing' => '3'),
            );
        
        $this->assertEquals($targetArray, $url->getQueryAsArray());
    }

    /**
     * Ensures that successive slashes are considered valid
     *
     * @return void
     */
    public function testSuccessiveSlashes()
    {

        $url = new URL('http://example.com/foo//bar/baz//fob/');
        $this->assertTrue($url->isValid());
        $this->assertEquals('/foo//bar/baz//fob/', $url->getPath());
    
    }

    /**
     * Test that setQuery() can handle unencoded query parameters (as other
     * browsers do), ZF-1934
     *
     * @group ZF-1934
     * @return void
     */
    public function testUnencodedQueryParameters()
    {
         $url = new URL('http://foo.com/bar');
         $url->setQuery('id=123&url=http://example.com/?bar=foo baz');
         $this->assertEquals('http://foo.com/bar?id=123&url=http%3A%2F%2Fexample.com%2F%3Fbar%3Dfoo+baz', $url->generate());
    }


    /**
     * Test that an extremely long URI does not break things up
     *
     * @group ZF-3712
     * @group ZF-7840
     */
    public function testVeryLongUrl()
    {
        if(!defined('TESTS_ZEND_URI_CRASH_TEST_ENABLED') || constant('TESTS_ZEND_URI_CRASH_TEST_ENABLED') == false) {
            $this->markTestSkipped('The constant TESTS_ZEND_URI_CRASH_TEST_ENABLED has to be defined and true to allow the test to work.');
        }
        $urlString = file_get_contents(dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR .
           '_files' . DIRECTORY_SEPARATOR . 'testVeryLongUriZF3712.txt');
        $url = new URL($urlString);
        $this->assertEquals($urlString, $url->generate());
    }

    /**
     * @group ZF-1480
     */
    public function testGetQueryAsArrayReturnsCorrectArray()
    {
        $url = new URL('http://example.com/foo/?test=a&var[]=1&var[]=2&some[thing]=3');
        $this->assertEquals(array(
            'test' => 'a',
            'var'  => array(1, 2),
            'some' => array('thing' => 3)
            ),
            $url->getQueryAsArray()
            );
    }

    /**
     * @group ZF-1480
     */
    public function testAddReplaceQueryParametersModifiesQueryAndReturnsOldQuery()
    {
        $url = new URL('http://example.com/foo/?a=1&b=2&c=3');
        $url->addReplaceQueryParameters(array('b' => 4, 'd' => -1));
        $this->assertEquals(array(
            'a' => 1,
            'b' => 4,
            'c' => 3,
            'd' => -1
        ), $url->getQueryAsArray());
        $this->assertEquals('a=1&b=4&c=3&d=-1', $url->getQuery());
    }

    /**
     * @group ZF-1480
     */
    public function testRemoveQueryParametersModifiesQueryAndReturnsOldQuery()
    {
        $url = new URL('http://example.com/foo/?a=1&b=2&c=3&d=4');
        $url->removeQueryParameters(array('b', 'd', 'e'));
        $this->assertEquals(array('a' => 1, 'c' => 3), $url->getQueryAsArray());
        $this->assertEquals('a=1&c=3', $url->getQuery());
    }
    
}