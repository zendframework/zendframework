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

namespace ZendTest\Service\Flickr;

use Zend\Http\Client\Adapter\Socket as SocketAdapter;
use Zend\Http\Client\Adapter\Test as TestAdapter;
use Zend\Service\Flickr\Flickr;
use Zend\Service\Flickr\Exception\OutOfBoundsException;

/**
 * @category   Zend
 * @package    Zend_Service_Flickr
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Flickr
 */
class OfflineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to Flickr service consumer object
     *
     * @var Flickr
     */
    protected $flickr;

    /**
     * Proxy to protected methods of Flickr
     *
     * @var TestAsset\FlickrProtectedMethodProxy
     */
    protected $flickrProxy;

    /**
     * Path to test data files
     *
     * @var string
     */
    protected $filesPath;

    /**
     * HTTP client adapter for testing
     *
     * @var TestAdapter
     */
    protected $httpClientAdapterTest;

    /**
     * Socket based HTTP client adapter
     *
     * @var SocketAdapter
     */
    protected $httpClientAdapterSocket;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        $this->flickr      = new Flickr(constant('TESTS_ZEND_SERVICE_FLICKR_ONLINE_APIKEY'));
        $this->flickrProxy = new TestAsset\FlickrProtectedMethodProxy(
            constant('TESTS_ZEND_SERVICE_FLICKR_ONLINE_APIKEY')
        );
        $this->filesPath   = __DIR__ . '/_files';

        $this->httpClientAdapterSocket = new SocketAdapter();
        $this->httpClientAdapterTest   = new TestAdapter();
    }

    /**
     * Basic testing to ensure that tagSearch() works as expected
     *
     * @return void
     */
    public function testTagSearchBasic()
    {
        $this->flickr->getRestClient()
            ->getHttpClient()
            ->setAdapter($this->httpClientAdapterTest);

        $this->httpClientAdapterTest->setResponse($this->loadResponse(__FUNCTION__));

        $options = array(
            'per_page' => 10,
            'page'     => 1,
            'tag_mode' => 'or',
            'extras'   => 'license, date_upload, date_taken, owner_name, icon_server'
        );

        $resultSet = $this->flickr->tagSearch('php', $options);

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
            $this->httpClientAdapterTest->setResponse($this->loadResponse(__FUNCTION__ . "-result_$resultSetId"));
            $result = $resultSet->current();
            $this->assertInstanceOf('Zend\Service\Flickr\Result', $result);
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
        $this->flickr->getRestClient()
            ->getHttpClient()
            ->setAdapter($this->httpClientAdapterTest);

        $this->httpClientAdapterTest->setResponse($this->loadResponse(__FUNCTION__));

        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\RuntimeException',
            'User not found'
        );
        $this->flickr->userSearch('2e38a9d9425d7e2c9d0788455e9ccc61');
    }

    /**
     * Ensures that userSearch() throws an exception when an invalid e-mail address is given
     *
     * @return void
     */
    public function testUserSearchExceptionEmailInvalid()
    {
        $this->flickr->getRestClient()
            ->getHttpClient()
            ->setAdapter($this->httpClientAdapterTest);

        $this->httpClientAdapterTest->setResponse($this->loadResponse(__FUNCTION__));

        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\RuntimeException',
            'User not found'
        );
        $this->flickr->userSearch('2e38a9d9425d7e2c9d0788455e9ccc61@example.com');
    }

    /**
     * Ensures that getIdByUsername() throws an exception given an empty argument
     *
     * @return void
     */
    public function testGetIdByUsernameExceptionUsernameEmpty()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\InvalidArgumentException',
            'supply a username'
        );
        $this->flickr->getIdByUsername('0');
    }

    /**
     * Ensures that getIdByEmail() throws an exception given an empty argument
     *
     * @return void
     */
    public function testGetIdByEmailExceptionEmailEmpty()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\InvalidArgumentException',
            'supply an e-mail'
        );
        $this->flickr->getIdByEmail('0');
    }

    /**
     * Ensures that getImageDetails() throws an exception given an empty argument
     *
     * @return void
     */
    public function testGetImageDetailsExceptionIdEmpty()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\InvalidArgumentException',
            'supply a photo'
        );
        $this->flickr->getImageDetails('0');
    }

    /**
     * Ensures that validateUserSearch() throws an exception when the per_page option is invalid
     *
     * @return void
     */
    public function testValidateUserSearchExceptionPerPageInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\DomainException',
            '"per_page" option'
        );
        $this->flickrProxy->proxyValidateUserSearch(array('per_page' => -1));
    }

    /**
     * Ensures that validateUserSearch() throws an exception when the page option is invalid
     *
     * @return void
     */
    public function testValidateUserSearchExceptionPageInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\DomainException',
            '"page" option'
        );
        $this->flickrProxy->proxyValidateUserSearch(array('per_page' => 10, 'page' => 1.23));
    }

    /**
     * Ensures that validateTagSearch() throws an exception when the per_page option is invalid
     *
     * @return void
     */
    public function testValidateTagSearchExceptionPerPageInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\DomainException',
            '"per_page" option'
        );
        $this->flickrProxy->proxyValidateTagSearch(array('per_page' => -1));
    }

    /**
     * Ensures that validateTagSearch() throws an exception when the page option is invalid
     *
     * @return void
     */
    public function testValidateTagSearchExceptionPageInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\DomainException',
            '"page" option'
        );
        $this->flickrProxy->proxyValidateTagSearch(array('per_page' => 10, 'page' => 1.23));
    }

    /**
     * Ensures that compareOptions() throws an exception when an option is invalid
     *
     * @return void
     */
    public function testCompareOptionsExceptionOptionInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\InvalidArgumentException',
            'parameters are invalid'
        );
        $this->flickrProxy->proxyCompareOptions(array('unexpected' => null), array());
    }

    /**
     * Ensures that tagSearch() throws an exception when an option is invalid
     *
     * @return void
     */
    public function testTagSearchExceptionOptionInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\InvalidArgumentException',
            'parameters are invalid'
        );
        $this->flickr->tagSearch('irrelevant', array('unexpected' => null));
    }

    /**
     * Basic testing to ensure that groupPoolGetPhotos() works as expected
     *
     * @return void
     */
    public function testGroupPoolGetPhotosBasic()
    {
        $this->flickr->getRestClient()
            ->getHttpClient()
            ->setAdapter($this->httpClientAdapterTest);

        $this->httpClientAdapterTest->setResponse($this->loadResponse(__FUNCTION__));

        $options = array(
            'per_page' => 10,
            'page'     => 1,
            'extras'   => 'license, date_upload, date_taken, owner_name, icon_server'
        );

        $resultSet = $this->flickr->groupPoolGetPhotos('20083316@N00', $options);

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
            $this->httpClientAdapterTest->setResponse($this->loadResponse(__FUNCTION__ . "-result_$resultSetId"));
            $result = $resultSet->current();
            $this->assertInstanceOf('Zend\Service\Flickr\Result', $result);
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
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\InvalidArgumentException',
            'parameters are invalid'
        );
        $this->flickr->groupPoolGetPhotos('irrelevant', array('unexpected' => null));
    }

    /**
     * Ensures that validateGroupPoolGetPhotos() throws an exception when the per_page option is invalid
     *
     * @return void
     */
    public function testValidateGroupPoolGetPhotosExceptionPerPageInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\DomainException',
            '"per_page" option'
        );
        $this->flickrProxy->proxyValidateGroupPoolGetPhotos(array('per_page' => -1));
    }

    /**
     * Ensures that validateGroupPoolGetPhotos() throws an exception when the page option is invalid
     *
     * @return void
     */
    public function testValidateGroupPoolGetPhotosExceptionPageInvalid()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\DomainException',
            '"page" option'
        );
        $this->flickrProxy->proxyValidateGroupPoolGetPhotos(array('per_page' => 10, 'page' => 1.23));
    }

    /**
     * Ensures that groupPoolGetPhotos() throws an exception when an invalid group_id is given
     *
     * @return void
     */
    public function testGroupPoolGetPhotosExceptionGroupIdInvalid()
    {
        $this->flickr->getRestClient()
            ->getHttpClient()
            ->setAdapter($this->httpClientAdapterTest);

        $this->httpClientAdapterTest->setResponse($this->loadResponse(__FUNCTION__));

        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\RuntimeException',
            'Group not found'
        );
        $this->flickr->groupPoolGetPhotos('2e38a9d9425d7e2c9d0788455e9ccc61');
    }

    /**
     * Ensures that groupPoolGetPhotos() throws an exception when an invalid group_id is given
     *
     * @return void
     */
    public function testGroupPoolGetPhotosExceptionGroupIdEmpty()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\InvalidArgumentException',
            'supply a group'
        );
        $this->flickr->groupPoolGetPhotos('0');
    }

    /**
     * Ensures that groupPoolGetPhotos() throws an exception when an array is given for group_id
     *
     * @return void
     */
    public function testGroupPoolGetPhotosExceptionGroupIdArray()
    {
        $this->setExpectedException(
            'Zend\Service\Flickr\Exception\InvalidArgumentException',
            'supply a group'
        );
        $this->flickr->groupPoolGetPhotos(array());
    }

    /**
     * Utility method that saves an HTTP response to a file
     *
     * @param  string $name
     * @return void
     */
    protected function saveResponse($name)
    {
        file_put_contents("$this->filesPath/$name.response",
                          $this->flickr->getRestClient()->getHttpClient()->getLastResponse()->asString());
    }

    /**
     * Utility method for returning a string HTTP response, which is loaded from a file
     *
     * @param  string $name
     * @return string
     */
    protected function loadResponse($name)
    {
        return file_get_contents("$this->filesPath/$name.response");
    }
}