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
 * @package    Zend_Service_Flickr
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Test helper
 */

/**
 * @see Zend_Service_Flickr
 */


/**
 * @category   Zend
 * @package    Zend_Service_Flickr
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Flickr
 */
class Zend_Service_Flickr_OfflineTest extends PHPUnit_Framework_TestCase
{
    /**
     * Reference to Flickr service consumer object
     *
     * @var Zend_Service_Flickr
     */
    protected $_flickr;

    /**
     * Proxy to protected methods of Zend_Service_Flickr
     *
     * @var Zend_Service_Flickr_OfflineTest_FlickrProtectedMethodProxy
     */
    protected $_flickrProxy;

    /**
     * Path to test data files
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * HTTP client adapter for testing
     *
     * @var Zend_Http_Client_Adapter_Test
     */
    protected $_httpClientAdapterTest;

    /**
     * Socket based HTTP client adapter
     *
     * @var Zend_Http_Client_Adapter_Socket
     */
    protected $_httpClientAdapterSocket;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        $this->_flickr      = new Zend_Service_Flickr(constant('TESTS_ZEND_SERVICE_FLICKR_ONLINE_APIKEY'));
        $this->_flickrProxy = new Zend_Service_Flickr_OfflineTest_FlickrProtectedMethodProxy(
            constant('TESTS_ZEND_SERVICE_FLICKR_ONLINE_APIKEY')
            );
        $this->_filesPath   = __DIR__ . '/_files';

        /**
         * @see Zend_Http_Client_Adapter_Socket
         */
        $this->_httpClientAdapterSocket = new Zend\Http\Client\Adapter\Socket();

        /**
         * @see Zend_Http_Client_Adapter_Test
         */
        $this->_httpClientAdapterTest = new Zend\Http\Client\Adapter\Test();
    }

    /**
     * Basic testing to ensure that tagSearch() works as expected
     *
     * @return void
     */
    public function testTagSearchBasic()
    {
        $this->_flickr->getRestClient()
                      ->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterTest);

        $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__));

        $options = array(
            'per_page' => 10,
            'page'     => 1,
            'tag_mode' => 'or',
            'extras'   => 'license, date_upload, date_taken, owner_name, icon_server'
            );

        $resultSet = $this->_flickr->tagSearch('php', $options);

        $this->assertEquals(4285, $resultSet->totalResultsAvailable);
        $this->assertEquals(10, $resultSet->totalResults());
        $this->assertEquals(10, $resultSet->totalResultsReturned);
        $this->assertEquals(1, $resultSet->firstResultPosition);

        $this->assertEquals(0, $resultSet->key());

        try {
            $resultSet->seek(-1);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        $resultSet->seek(9);

        try {
            $resultSet->seek(10);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        $resultSet->rewind();

        $resultSetIds = array(
            '428222530',
            '427883929',
            '427884403',
            '427887192',
            '427883923',
            '427884394',
            '427883930',
            '427884398',
            '427883924',
            '427884401'
            );

        $this->assertTrue($resultSet->valid());

        foreach ($resultSetIds as $resultSetId) {
            $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__ . "-result_$resultSetId"));
            $result = $resultSet->current();
            $this->assertTrue($result instanceof Zend_Service_Flickr_Result);
            $resultSet->next();
        }

        $this->assertFalse($resultSet->valid());
    }

    /**
     * Ensures that userSearch() throws an exception when an invalid username is given
     *
     * @return void
     */
    public function testUserSearchExceptionUsernameInvalid()
    {
        $this->_flickr->getRestClient()
                      ->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterTest);

        $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__));

        try {
            $this->_flickr->userSearch('2e38a9d9425d7e2c9d0788455e9ccc61');
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('User not found', $e->getMessage());
        }
    }

    /**
     * Ensures that userSearch() throws an exception when an invalid e-mail address is given
     *
     * @return void
     */
    public function testUserSearchExceptionEmailInvalid()
    {
        $this->_flickr->getRestClient()
                      ->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterTest);

        $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__));

        try {
            $this->_flickr->userSearch('2e38a9d9425d7e2c9d0788455e9ccc61@example.com');
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('User not found', $e->getMessage());
        }
    }

    /**
     * Ensures that getIdByUsername() throws an exception given an empty argument
     *
     * @return void
     */
    public function testGetIdByUsernameExceptionUsernameEmpty()
    {
        try {
            $this->_flickr->getIdByUsername('0');
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('supply a username', $e->getMessage());
        }
    }

    /**
     * Ensures that getIdByEmail() throws an exception given an empty argument
     *
     * @return void
     */
    public function testGetIdByEmailExceptionEmailEmpty()
    {
        try {
            $this->_flickr->getIdByEmail('0');
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('supply an e-mail address', $e->getMessage());
        }
    }

    /**
     * Ensures that getImageDetails() throws an exception given an empty argument
     *
     * @return void
     */
    public function testGetImageDetailsExceptionIdEmpty()
    {
        try {
            $this->_flickr->getImageDetails('0');
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('supply a photo ID', $e->getMessage());
        }
    }

    /**
     * Ensures that _validateUserSearch() throws an exception when the per_page option is invalid
     *
     * @return void
     */
    public function testValidateUserSearchExceptionPerPageInvalid()
    {
        try {
            $this->_flickrProxy->proxyValidateUserSearch(array('per_page' => -1));
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('"per_page" option', $e->getMessage());
        }
    }

    /**
     * Ensures that _validateUserSearch() throws an exception when the page option is invalid
     *
     * @return void
     */
    public function testValidateUserSearchExceptionPageInvalid()
    {
        try {
            $this->_flickrProxy->proxyValidateUserSearch(array('per_page' => 10, 'page' => 1.23));
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('"page" option', $e->getMessage());
        }
    }

    /**
     * Ensures that _validateTagSearch() throws an exception when the per_page option is invalid
     *
     * @return void
     */
    public function testValidateTagSearchExceptionPerPageInvalid()
    {
        try {
            $this->_flickrProxy->proxyValidateTagSearch(array('per_page' => -1));
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('"per_page" option', $e->getMessage());
        }
    }

    /**
     * Ensures that _validateTagSearch() throws an exception when the page option is invalid
     *
     * @return void
     */
    public function testValidateTagSearchExceptionPageInvalid()
    {
        try {
            $this->_flickrProxy->proxyValidateTagSearch(array('per_page' => 10, 'page' => 1.23));
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('"page" option', $e->getMessage());
        }
    }

    /**
     * Ensures that _compareOptions() throws an exception when an option is invalid
     *
     * @return void
     */
    public function testCompareOptionsExceptionOptionInvalid()
    {
        try {
            $this->_flickrProxy->proxyCompareOptions(array('unexpected' => null), array());
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('parameters are invalid', $e->getMessage());
        }
    }

    /**
     * Ensures that tagSearch() throws an exception when an option is invalid
     *
     * @return void
     */
    public function testTagSearchExceptionOptionInvalid()
    {
        try {
            $this->_flickr->tagSearch('irrelevant', array('unexpected' => null));
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('parameters are invalid', $e->getMessage());
        }
    }

    /**
     * Basic testing to ensure that groupPoolGetPhotos() works as expected
     *
     * @return void
     */
    public function testGroupPoolGetPhotosBasic()
    {
        $this->_flickr->getRestClient()
                      ->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterTest);

        $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__));

        $options = array(
            'per_page' => 10,
            'page'     => 1,
            'extras'   => 'license, date_upload, date_taken, owner_name, icon_server'
            );

        $resultSet = $this->_flickr->groupPoolGetPhotos('20083316@N00', $options);

        $this->assertEquals(4285, $resultSet->totalResultsAvailable);
        $this->assertEquals(10, $resultSet->totalResults());
        $this->assertEquals(10, $resultSet->totalResultsReturned);
        $this->assertEquals(1, $resultSet->firstResultPosition);

        $this->assertEquals(0, $resultSet->key());

        try {
            $resultSet->seek(-1);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        $resultSet->seek(9);

        try {
            $resultSet->seek(10);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }

        $resultSet->rewind();

        $resultSetIds = array(
            '428222530',
            '427883929',
            '427884403',
            '427887192',
            '427883923',
            '427884394',
            '427883930',
            '427884398',
            '427883924',
            '427884401'
            );

        $this->assertTrue($resultSet->valid());

        foreach ($resultSetIds as $resultSetId) {
            $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__ . "-result_$resultSetId"));
            $result = $resultSet->current();
            $this->assertTrue($result instanceof Zend_Service_Flickr_Result);
            $resultSet->next();
        }

        $this->assertFalse($resultSet->valid());
    }

    /**
     * Ensures that groupPoolGetPhotos() throws an exception when an option is invalid
     *
     * @return void
     */
    public function testGroupPoolGetPhotosExceptionOptionInvalid()
    {
      try {
          $this->_flickr->groupPoolGetPhotos('irrelevant', array('unexpected' => null));
          $this->fail('Expected Zend_Service_Exception not thrown');
      } catch (Zend_Service_Exception $e) {
          $this->assertContains('parameters are invalid', $e->getMessage());
      }
    }

    /**
     * Ensures that _validateGroupPoolGetPhotos() throws an exception when the per_page option is invalid
     *
     * @return void
     */
    public function testValidateGroupPoolGetPhotosExceptionPerPageInvalid()
    {
      try {
          $this->_flickrProxy->proxyValidateGroupPoolGetPhotos(array('per_page' => -1));
          $this->fail('Expected Zend_Service_Exception not thrown');
      } catch (Zend_Service_Exception $e) {
          $this->assertContains('"per_page" option', $e->getMessage());
      }
    }

    /**
     * Ensures that _validateGroupPoolGetPhotos() throws an exception when the page option is invalid
     *
     * @return void
     */
    public function testValidateGroupPoolGetPhotosExceptionPageInvalid()
    {
      try {
          $this->_flickrProxy->proxyValidateGroupPoolGetPhotos(array('per_page' => 10, 'page' => 1.23));
          $this->fail('Expected Zend_Service_Exception not thrown');
      } catch (Zend_Service_Exception $e) {
          $this->assertContains('"page" option', $e->getMessage());
      }
    }

    /**
     * Ensures that groupPoolGetPhotos() throws an exception when an invalid group_id is given
     *
     * @return void
     */
    public function testGroupPoolGetPhotosExceptionGroupIdInvalid()
    {
        $this->_flickr->getRestClient()
                      ->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterTest);

        $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__));

        try {
            $this->_flickr->groupPoolGetPhotos('2e38a9d9425d7e2c9d0788455e9ccc61');
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('Group not found', $e->getMessage());
        }
    }

    /**
     * Ensures that groupPoolGetPhotos() throws an exception when an invalid group_id is given
     *
     * @return void
     */
    public function testGroupPoolGetPhotosExceptionGroupIdEmpty()
    {
        try {
            $this->_flickr->groupPoolGetPhotos('0');
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('supply a group id', $e->getMessage());
        }
    }

    /**
     * Ensures that groupPoolGetPhotos() throws an exception when an array is given for group_id
     *
     * @return void
     */
    public function testGroupPoolGetPhotosExceptionGroupIdArray()
    {
        try {
            $this->_flickr->groupPoolGetPhotos(array());
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('supply a group id', $e->getMessage());
        }
    }

    /**
     * Utility method that saves an HTTP response to a file
     *
     * @param  string $name
     * @return void
     */
    protected function _saveResponse($name)
    {
        file_put_contents("$this->_filesPath/$name.response",
                          $this->_flickr->getRestClient()->getHttpClient()->getLastResponse()->asString());
    }

    /**
     * Utility method for returning a string HTTP response, which is loaded from a file
     *
     * @param  string $name
     * @return string
     */
    protected function _loadResponse($name)
    {
        return file_get_contents("$this->_filesPath/$name.response");
    }
}


class Zend_Service_Flickr_OfflineTest_FlickrProtectedMethodProxy extends Zend_Service_Flickr
{
    public function proxyValidateUserSearch(array $options)
    {
        $this->_validateUserSearch($options);
    }

    public function proxyValidateTagSearch(array $options)
    {
        $this->_validateTagSearch($options);
    }

    public function proxyValidateGroupPoolGetPhotos(array $options)
    {
        $this->_validateGroupPoolGetPhotos($options);
    }

    public function proxyCompareOptions(array $options, array $validOptions)
    {
        $this->_compareOptions($options, $validOptions);
    }
}
