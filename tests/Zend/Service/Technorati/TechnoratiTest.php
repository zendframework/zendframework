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
 * Test helper
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
class TechnoratiTest extends TestCase
{
    const TEST_APY_KEY = 'somevalidapikey';
    const TEST_PARAM_COSMOS = 'http://www.simonecarletti.com/blog/';
    const TEST_PARAM_SEARCH = 'google';
    const TEST_PARAM_TAG = 'google';
    const TEST_PARAM_DAILYCOUNT = 'google';
    const TEST_PARAM_GETINFO = 'weppos';
    const TEST_PARAM_BLOGINFO = 'http://www.simonecarletti.com/blog/';
    const TEST_PARAM_BLOGPOSTTAGS = 'http://www.simonecarletti.com/blog/';

    public function setUp()
    {
        /**
         * @see \Zend\Http\Client\Adapter\Test
         */
        $adapter = new \Zend\Http\Client\Adapter\Test();

        /**
         * @see \Zend\Http\Client
         */
        $client = new \Zend\Http\Client(Technorati\Technorati::API_URI_BASE, array(
            'adapter' => $adapter
        ));

        $this->technorati = new Technorati\Technorati(self::TEST_APY_KEY);
        $this->adapter = $adapter;
        $this->technorati->getRestClient()->setHttpClient($client);
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\Technorati', array(self::TEST_APY_KEY));
    }

    public function testApiKeyMatches()
    {
        $object = $this->technorati;
        $this->assertEquals(self::TEST_APY_KEY, $object->getApiKey());
    }

    public function testSetGetApiKey()
    {
        $object = $this->technorati;

        $set = 'just a test';
        $get = $object->setApiKey($set)->getApiKey();
        $this->assertEquals($set, $get);
    }

    public function testCosmos()
    {
        $result = $this->_setResponseFromFile('TestCosmosSuccess.xml')->cosmos(self::TEST_PARAM_COSMOS);

        $this->assertInstanceOf('Zend\Service\Technorati\CosmosResultSet', $result);
        $this->assertEquals(2, $result->totalResults());
        $result->seek(0);
        $this->assertInstanceOf('Zend\Service\Technorati\CosmosResult', $result->current());
        // content is validated in Zend_Service_Technorati_CosmosResultSet tests
    }

    public function testCosmosThrowsExceptionWithError()
    {
        try {
            $this->_setResponseFromFile('TestCosmosError.xml')->cosmos(self::TEST_PARAM_COSMOS);
            $this->fail('Expected Zend\Service\Technorati\Exception\RuntimeException not thrown');
        } catch (Technorati\Exception\RuntimeException $e) {
            $this->assertContains("Invalid request: url is required", $e->getMessage());
        }
    }

    public function testCosmosThrowsExceptionWithInvalidUrl()
    {
        // url is mandatory --> validated by PHP interpreter
        // url must not be empty
        $this->_testThrowsExceptionWithInvalidMandatoryOption('cosmos', 'url');
    }

    public function testCosmosThrowsExceptionWithInvalidOption()
    {
        $options = array(
            array('type'      => 'foo'),
            array('limit'     => 'foo'),
            array('limit'     => 0),
            array('limit'     => 101),
            array('start'     => 0),
            // 'current'    =>  // cast to int
            // 'claim'      =>  // cast to int
            // 'highlight'  =>  // cast to int
        );
        $this->_testThrowsExceptionWithInvalidOption($options, 'TestCosmosSuccess.xml', 'cosmos', array(self::TEST_PARAM_COSMOS));
    }

    public function testCosmosOption()
    {
        $options = array(
            array('type'      => 'link'),
            array('type'      => 'weblog'),
            array('limit'     => 1),
            array('limit'     => 50),
            array('limit'     => 100),
            array('start'     => 1),
            array('start'     => 1000),
            array('current'   => false),   // cast to int
            array('current'   => 0),       // cast to int
            array('claim'     => false),   // cast to int
            array('claim'     => 0),       // cast to int
            array('highlight' => false),   // cast to int
            array('highlight' => 0),       // cast to int
        );
        $this->_testOption($options, 'TestCosmosSuccess.xml', 'cosmos', array(self::TEST_PARAM_COSMOS));
    }

    public function testSearch()
    {
        $result = $this->_setResponseFromFile('TestSearchSuccess.xml')->search(self::TEST_PARAM_SEARCH);

        $this->assertInstanceOf('Zend\Service\Technorati\SearchResultSet', $result);
        $this->assertEquals(2, $result->totalResults());
        $result->seek(0);
        $this->assertInstanceOf('Zend\Service\Technorati\SearchResult', $result->current());
        // content is validated in Zend_Service_Technorati_SearchResultSet tests
    }

    /**
     * @see /_files/MISSING
     *
    public function testSearchThrowsExceptionWithError()
    {
        try {
            $this->_setResponseFromFile('TestSearchError.xml')->cosmos(self::TEST_PARAM_COSMOS);
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains("Invalid request: url is required", $e->getMessage());
        }
    } */

    public function testSearchThrowsExceptionWithInvalidQuery()
    {
        // query is mandatory --> validated by PHP interpreter
        // query must not be empty
        $this->_testThrowsExceptionWithInvalidMandatoryOption('search', 'query');
    }

    public function testSearchThrowsExceptionWithInvalidOption()
    {
        $options = array(
            array('authority' => 'foo'),
            array('limit'     => 'foo'),
            array('limit'     => 0),
            array('limit'     => 101),
            array('start'     => 0),
            // 'claim'      =>  // cast to int
        );
        $this->_testThrowsExceptionWithInvalidOption($options, 'TestSearchSuccess.xml', 'search', array(self::TEST_PARAM_SEARCH));
    }

    public function testSearchOption()
    {
        $options = array(
            array('language'  => 'en'),    // not validated
            array('authority' => 'n'),
            array('authority' => 'a1'),
            array('authority' => 'a4'),
            array('authority' => 'a7'),
            array('limit'     => 1),
            array('limit'     => 50),
            array('limit'     => 100),
            array('start'     => 1),
            array('start'     => 1000),
            array('claim'     => false),   // cast to int
            array('claim'     => 0),       // cast to int
        );
        $this->_testOption($options, 'TestSearchSuccess.xml', 'search', array(self::TEST_PARAM_SEARCH));
    }

    public function testTag()
    {
        $result = $this->_setResponseFromFile('TestTagSuccess.xml')->tag(self::TEST_PARAM_TAG);

        $this->assertInstanceOf('Zend\Service\Technorati\TagResultSet', $result);
        $this->assertEquals(2, $result->totalResults());
        $result->seek(0);
        $this->assertInstanceOf('Zend\Service\Technorati\TagResult', $result->current());
        // content is validated in Zend_Service_Technorati_TagResultSet tests
    }

    public function testTagThrowsExceptionWithError()
    {
        try {
            $this->_setResponseFromFile('TestTagError.xml')->tag(self::TEST_PARAM_TAG);
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch (Technorati\Exception\RuntimeException $e) {
            $this->assertContains("Invalid request.", $e->getMessage());
        }
    }

    public function testTagThrowsExceptionWithInvalidTag()
    {
        // tag is mandatory --> validated by PHP interpreter
        // tag must not be empty
        $this->_testThrowsExceptionWithInvalidMandatoryOption('tag', 'tag');
    }

    public function testTagThrowsExceptionWithInvalidOption()
    {
        $options = array(
            array('limit'     => 'foo'),
            array('limit'     => 0),
            array('limit'     => 101),
            array('start'     => 0),
            // 'excerptsize'    =>  // cast to int
            // 'topexcerptsize' =>  // cast to int
            );
        $this->_testThrowsExceptionWithInvalidOption($options, 'TestTagSuccess.xml', 'tag', array(self::TEST_PARAM_TAG));
    }

    public function testTagOption()
    {
        $options = array(
            array('excerptsize'     => 150),    // cast to int
            array('excerptsize'     => '150'),  // cast to int
            array('topexcerptsize'  => 150),    // cast to int
            array('topexcerptsize'  => '150'),  // cast to int
            array('limit'     => 1),
            array('limit'     => 50),
            array('limit'     => 100),
            array('start'     => 1),
            array('start'     => 1000),
        );
        $this->_testOption($options, 'TestTagSuccess.xml', 'tag', array(self::TEST_PARAM_TAG));
    }

    public function testDailyCounts()
    {
        $result = $this->_setResponseFromFile('TestDailyCountsSuccess.xml')->dailyCounts(self::TEST_PARAM_DAILYCOUNT);

        $this->assertInstanceOf('Zend\Service\Technorati\DailyCountsResultSet', $result);
        $this->assertEquals(180, $result->totalResults());
        $result->seek(0);
        $this->assertInstanceOf('Zend\Service\Technorati\DailyCountsResult', $result->current());
        // content is validated in Zend_Service_Technorati_DailyCountsResultSet tests
    }

    public function testDailyCountsThrowsExceptionWithError()
    {
        try {
            $this->_setResponseFromFile('TestDailyCountsError.xml')->dailyCounts(self::TEST_PARAM_DAILYCOUNT);
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch (Technorati\Exception\RuntimeException $e) {
            $this->assertContains("Missing required parameter", $e->getMessage());
        }
    }

    public function testDailyCountsThrowsExceptionWithInvalidQuery()
    {
        // q is mandatory --> validated by PHP interpreter
        // q must not be empty
        $this->_testThrowsExceptionWithInvalidMandatoryOption('dailyCounts', 'q');
    }

    public function testDailyCountsThrowsExceptionWithInvalidOption()
    {
        $options = array(
            array('days' => 0),
            array('days' => '0'),
            array('days' => 181),
            array('days' => '181'),
            );
        $this->_testThrowsExceptionWithInvalidOption($options, 'TestDailyCountsSuccess.xml', 'dailyCounts', array(self::TEST_PARAM_DAILYCOUNT));
    }

    public function testDailyCountsOption()
    {
        $options = array(
            array('days' => 120),   // cast to int
            array('days' => '120'), // cast to int
            array('days' => 180),   // cast to int
            array('days' => '180'), // cast to int
            );
        $this->_testOption($options, 'TestDailyCountsSuccess.xml', 'dailyCounts', array(self::TEST_PARAM_DAILYCOUNT));
    }

    public function testBlogInfo()
    {
        $result = $this->_setResponseFromFile('TestBlogInfoSuccess.xml')->blogInfo(self::TEST_PARAM_BLOGINFO);

        $this->assertInstanceOf('Zend\Service\Technorati\BlogInfoResult', $result);
        // content is validated in Zend_Service_Technorati_BlogInfoResult tests
    }

    public function testBlogInfoThrowsExceptionWithError()
    {
        try {
            $this->_setResponseFromFile('TestBlogInfoError.xml')->blogInfo(self::TEST_PARAM_BLOGINFO);
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch (Technorati\Exception\RuntimeException $e) {
            $this->assertContains("Invalid request: url is required", $e->getMessage());
        }
    }

    public function testBlogInfoThrowsExceptionWithInvalidUrl()
    {
        // url is mandatory --> validated by PHP interpreter
        // url must not be empty
        $this->_testThrowsExceptionWithInvalidMandatoryOption('blogInfo', 'url');
    }

    public function testBlogInfoThrowsExceptionWithUrlNotWeblog()
    {
        // emulate Technorati exception
        // when URL is not a recognized weblog
        try {
            $this->_setResponseFromFile('TestBlogInfoErrorUrlNotWeblog.xml')->blogInfo('www.simonecarletti.com');
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch (Technorati\Exception\RuntimeException $e) {
            $this->assertContains("Technorati weblog", $e->getMessage());
        }
    }

    public function testBlogPostTags()
    {
        $result = $this->_setResponseFromFile('TestBlogPostTagsSuccess.xml')->blogPostTags(self::TEST_PARAM_BLOGPOSTTAGS);

        $this->assertInstanceOf('Zend\Service\Technorati\TagsResultSet', $result);
        // content is validated in Zend_Service_Technorati_TagsResultSet tests
    }

    public function testBlogPostTagsThrowsExceptionWithError()
    {
        try {
            $this->_setResponseFromFile('TestBlogPostTagsError.xml')->blogPostTags(self::TEST_PARAM_BLOGPOSTTAGS);
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch (Technorati\Exception\RuntimeException $e) {
            $this->assertContains("Invalid request: url is required", $e->getMessage());
        }
    }

    public function testBlogPostTagsThrowsExceptionWithInvalidUrl()
    {
        // url is mandatory --> validated by PHP interpreter
        // url must not be empty
        $this->_testThrowsExceptionWithInvalidMandatoryOption('blogPostTags', 'url');
    }

    public function testBlogPostTagsThrowsExceptionWithInvalidOption()
    {
        $options = array(
            array('limit'     => 'foo'),
            array('limit'     => 0),
            array('limit'     => 101),
            array('start'     => 0),
        );
        $this->_testThrowsExceptionWithInvalidOption($options, 'TestBlogPostTagsSuccess.xml', 'blogPostTags', array(self::TEST_PARAM_BLOGPOSTTAGS));
    }

    public function testBlogPostTagsOption()
    {
        $options = array(
            array('limit'     => 1),
            array('limit'     => 50),
            array('limit'     => 100),
            array('start'     => 1),
            array('start'     => 1000),
        );
        $this->_testOption($options, 'TestBlogPostTagsSuccess.xml', 'blogPostTags', array(self::TEST_PARAM_BLOGPOSTTAGS));
    }

    public function testTopTags()
    {
        $result = $this->_setResponseFromFile('TestTopTagsSuccess.xml')->topTags();

        $this->assertInstanceOf('Zend\Service\Technorati\TagsResultSet', $result);
        // content is validated in Zend_Service_Technorati_TagsResultSet tests
    }

    public function testTopTagsThrowsExceptionWithError()
    {
        try {
            $this->_setResponseFromFile('TestTopTagsError.xml')->topTags();
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch (Technorati\Exception\RuntimeException $e) {
            $this->assertContains("Invalid key.", $e->getMessage());
        }
    }

    public function testTopTagsThrowsExceptionWithInvalidOption()
    {
        $options = array(
            array('limit'     => 'foo'),
            array('limit'     => 0),
            array('limit'     => 101),
            array('start'     => 0),
        );
        $this->_testThrowsExceptionWithInvalidOption($options, 'TestTopTagsSuccess.xml', 'topTags');
    }

    public function testTopTagsOption()
    {
        $options = array(
            array('limit'     => 1),
            array('limit'     => 50),
            array('limit'     => 100),
            array('start'     => 1),
            array('start'     => 1000),
        );
        $this->_testOption($options, 'TestTopTagsSuccess.xml', 'topTags');
    }

    public function testGetInfo()
    {
        $result = $this->_setResponseFromFile('TestGetInfoSuccess.xml')->getInfo(self::TEST_PARAM_GETINFO);

        $this->assertInstanceOf('Zend\Service\Technorati\GetInfoResult', $result);
        // content is validated in Zend_Service_Technorati_GetInfoResult tests
    }

    public function testGetInfoThrowsExceptionWithError()
    {
        try {
            $this->_setResponseFromFile('TestGetInfoError.xml')->getInfo(self::TEST_PARAM_GETINFO);
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch (Technorati\Exception\RuntimeException $e) {
            $this->assertContains("Username is a required field.", $e->getMessage());
        }
    }

    public function testGetInfoThrowsExceptionWithInvalidUsername()
    {
        // username is mandatory --> validated by PHP interpreter
        // username must not be empty
        $this->_testThrowsExceptionWithInvalidMandatoryOption('getInfo', 'username');
    }

    public function testKeyInfo()
    {
        $result = $this->_setResponseFromFile('TestKeyInfoSuccess.xml')->keyInfo();

        $this->assertInstanceOf('Zend\Service\Technorati\KeyInfoResult', $result);
        // content is validated in Zend_Service_Technorati_KeyInfoResult tests
    }

    public function testKeyInfoThrowsExceptionWithError()
    {
        try {
            $this->_setResponseFromFile('TestKeyInfoError.xml')->keyInfo();
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch (Technorati\Exception\RuntimeException $e) {
            $this->assertContains("Invalid key.", $e->getMessage());
        }
    }

    public function testAllThrowsExceptionWithInvalidOptionFormat()
    {
        $invalidFormatOption = array('format' => 'rss');
        // format must be XML
        $methods = array('cosmos'       => self::TEST_PARAM_COSMOS,
                         'search'       => self::TEST_PARAM_SEARCH,
                         'tag'          => self::TEST_PARAM_TAG,
                         'dailyCounts'  => self::TEST_PARAM_DAILYCOUNT,
                         'topTags'      => null,
                         'blogInfo'     => self::TEST_PARAM_BLOGINFO,
                         'blogPostTags' => self::TEST_PARAM_BLOGPOSTTAGS,
                         'getInfo'      => self::TEST_PARAM_GETINFO);
        $technorati = $this->technorati;

        foreach ($methods as $method => $param) {
            $options = array_merge((array) $param, array($invalidFormatOption));
            try {
                call_user_func_array(array($technorati, $method), $options);
                $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
            } catch (Technorati\Exception\RuntimeException $e) {
                $this->assertContains("'format'", $e->getMessage());
            }
        }
    }

    public function testAllThrowsExceptionWithUnknownOption()
    {
        $invalidOption = array('foo' => 'bar');
        $methods = array('cosmos'       => self::TEST_PARAM_COSMOS,
                         'search'       => self::TEST_PARAM_SEARCH,
                         'tag'          => self::TEST_PARAM_TAG,
                         'dailyCounts'  => self::TEST_PARAM_DAILYCOUNT,
                         'topTags'      => null,
                         'blogInfo'     => self::TEST_PARAM_BLOGINFO,
                         'blogPostTags' => self::TEST_PARAM_BLOGPOSTTAGS,
                         'getInfo'      => self::TEST_PARAM_GETINFO);

        $technorati = $this->technorati;
        foreach ($methods as $method => $param) {
            $options = array_merge((array) $param, array($invalidOption));
            try {
                call_user_func_array(array($technorati, $method), $options);
                $this->fail('Expected Zend\Service\Technorati\Exception\RuntimeException not thrown');
            } catch (Technorati\Exception\RuntimeException $e) {
                $this->assertContains("'foo'", $e->getMessage());
            }
        }
    }

    /**
     * Tests whether $callbackMethod method throws an Exception
     * with Invalid Url.
     *
     * @param   string $callbackMethod
     */
    private function _testThrowsExceptionWithInvalidMandatoryOption($callbackMethod, $name)
    {
        try {
            $this->technorati->$callbackMethod('');
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch (Technorati\Exception\RuntimeException $e) {
            $this->assertContains("'$name'", $e->getMessage());
        }
    }

    /**
     * Tests whether for each $validOptions a method call is successful.
     *
     * @param   array $validOptions
     * @param   string $xmlFile
     * @param   string $callbackMethod
     * @param   null|array $callbackRequiredOptions
     */
    private function _testOption($validOptions, $xmlFile, $callbackMethod, $callbackRequiredOptions = null)
    {
        $technorati = $this->_setResponseFromFile($xmlFile);
        foreach ($validOptions as $pair) {
            list($option, $value) = each($pair);
            $options = is_array($callbackRequiredOptions) ?
                            array_merge($callbackRequiredOptions, array($pair)) :
                            array($pair);

            call_user_func_array(array($technorati, $callbackMethod), $options);
        }
    }

    /**
     * Tests whether for each $validOptions a method call is successful.
     *
     * @param   array $invalidOptions
     * @param   string $xmlFile
     * @param   string $callbackMethod
     * @param   null|array $callbackRequiredOptions
     */
    private function _testThrowsExceptionWithInvalidOption($invalidOptions, $xmlFile, $callbackMethod, $callbackRequiredOptions = null)
    {
        $technorati = $this->_setResponseFromFile($xmlFile);
        foreach ($invalidOptions as $pair) {
            list($option, $value) = each($pair);
            $options = is_array($callbackRequiredOptions) ?
                            array_merge($callbackRequiredOptions, array($pair)) :
                            array($pair);

            try {
                call_user_func_array(array($technorati, $callbackMethod), $options);
                $this->fail("Expected Zend_Service_Technorati_Exception not thrown " .
                            "for option '$option' value '$value'");
            } catch (Technorati\Exception\RuntimeException $e) {
                $this->assertContains("'$option'", $e->getMessage());
            }
        }
    }

    /**
     * Loads a response content from a test case file
     * and sets the content to current Test Adapter.
     *
     * Returns current Zend_Service_Technorati instance
     * to let developers use the powerful chain call.
     *
     * Do not execute any file validation. Please use this method carefully.
     *
     * @params  string $file
     * @return  Technorati\Technorati
     */
    private function _setResponseFromFile($file)
    {
        $response = "HTTP/1.0 200 OK\r\n"
                  . "Date: " . date(DATE_RFC1123) . "\r\n"
                  . "Server: Apache\r\n"
                  . "Cache-Control: max-age=60\r\n"
                  . "Content-Type: text/xml; charset=UTF-8\r\n"
                  . "X-Powered-By: PHP/5.2.1\r\n"
                  . "Connection: close\r\n"
                  . "\r\n"
                  . file_get_contents(__DIR__ . '/_files/' . $file) ;

        $this->adapter->setResponse($response);
        return $this->technorati; // allow chain call
     }
}
