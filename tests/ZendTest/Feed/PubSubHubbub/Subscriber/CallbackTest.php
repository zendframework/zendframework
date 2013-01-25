<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace ZendTest\Feed\PubSubHubbub\Subscriber;

use DateInterval;
use DateTime;
use Zend\Feed\PubSubHubbub\HttpResponse;
use Zend\Feed\PubSubHubbub\Model;
use Zend\Feed\PubSubHubbub\Subscriber\Callback as CallbackSubscriber;
use ArrayObject;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Subsubhubbub
 */
class CallbackTest extends \PHPUnit_Framework_TestCase
{
    /** @var CallbackSubscriber */
    public $_callback;
    /** @var \Zend\Db\Adapter\Adapter|\PHPUnit_Framework_MockObject_MockObject */
    public $_adapter;
    /** @var \Zend\Db\TableGateway\TableGateway|\PHPUnit_Framework_MockObject_MockObject */
    public $_tableGateway;
    /** @var \Zend\Db\ResultSet\ResultSet|\PHPUnit_Framework_MockObject_MockObject */
    public $_rowset;
    /** @var array */
    public $_get;
    /** @var DateTime */
    public $now;

    public function setUp()
    {
        $this->_callback = new CallbackSubscriber;

        $this->_adapter      = $this->_getCleanMock(
            '\Zend\Db\Adapter\Adapter'
        );
        $this->_tableGateway = $this->_getCleanMock(
            '\Zend\Db\TableGateway\TableGateway'
        );
        $this->_rowset       = $this->_getCleanMock(
            '\Zend\Db\ResultSet\ResultSet'
        );

        $this->_tableGateway->expects($this->any())
            ->method('getAdapter')
            ->will($this->returnValue($this->_adapter));
        $storage = new Model\Subscription($this->_tableGateway);

        $this->now = new DateTime();
        $storage->setNow(clone $this->now);

        $this->_callback->setStorage($storage);

        $this->_get = array(
            'hub_mode'          => 'subscribe',
            'hub_topic'         => 'http://www.example.com/topic',
            'hub_challenge'     => 'abc',
            'hub_verify_token'  => 'cba',
            'hub_lease_seconds' => '1234567'
        );

        $_SERVER['REQUEST_METHOD'] = 'get';
        $_SERVER['QUERY_STRING']   = 'xhub.subscription=verifytokenkey';
    }

    public function testCanSetHttpResponseObject()
    {
        $this->_callback->setHttpResponse(new HttpResponse);
        $this->assertTrue($this->_callback->getHttpResponse() instanceof HttpResponse);
    }

    public function testCanUsesDefaultHttpResponseObject()
    {
        $this->assertTrue($this->_callback->getHttpResponse() instanceof HttpResponse);
    }

    public function testThrowsExceptionOnInvalidHttpResponseObjectSet()
    {
        $this->setExpectedException('\Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_callback->setHttpResponse(new \stdClass);
    }

    public function testThrowsExceptionIfNonObjectSetAsHttpResponseObject()
    {
        $this->setExpectedException('\Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_callback->setHttpResponse('');
    }

    public function testCanSetSubscriberCount()
    {
        $this->_callback->setSubscriberCount('10000');
        $this->assertEquals(10000, $this->_callback->getSubscriberCount());
    }

    public function testDefaultSubscriberCountIsOne()
    {
        $this->assertEquals(1, $this->_callback->getSubscriberCount());
    }

    public function testThrowsExceptionOnSettingZeroAsSubscriberCount()
    {
        $this->setExpectedException('\Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_callback->setSubscriberCount(0);
    }

    public function testThrowsExceptionOnSettingLessThanZeroAsSubscriberCount()
    {
        $this->setExpectedException('\Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_callback->setSubscriberCount(-1);
    }

    public function testThrowsExceptionOnSettingAnyScalarTypeCastToAZeroOrLessIntegerAsSubscriberCount()
    {
        $this->setExpectedException('\Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_callback->setSubscriberCount('0aa');
    }


    public function testCanSetStorageImplementation()
    {
        $storage = new Model\Subscription($this->_tableGateway);
        $this->_callback->setStorage($storage);
        $this->assertThat($this->_callback->getStorage(), $this->identicalTo($storage));
    }

    /**
     * @group ZF2_CONFLICT
     */
    public function testValidatesValidHttpGetData()
    {
        $mockReturnValue = $this->getMock('Result', array('getArrayCopy'));
        $mockReturnValue->expects($this->any())
            ->method('getArrayCopy')
            ->will($this->returnValue(array(
                                           'verify_token' => hash('sha256',
                                                                  'cba')
                                      )));

        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(array('id' => 'verifytokenkey')))
            ->will($this->returnValue($this->_rowset));
        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($mockReturnValue));
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->assertTrue($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfHubVerificationNotAGetRequest()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfModeMissingFromHttpGetData()
    {
        unset($this->_get['hub_mode']);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfTopicMissingFromHttpGetData()
    {
        unset($this->_get['hub_topic']);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfChallengeMissingFromHttpGetData()
    {
        unset($this->_get['hub_challenge']);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfVerifyTokenMissingFromHttpGetData()
    {
        unset($this->_get['hub_verify_token']);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsTrueIfModeSetAsUnsubscribeFromHttpGetData()
    {
        $mockReturnValue = $this->getMock('Result', array('getArrayCopy'));
        $mockReturnValue->expects($this->any())
            ->method('getArrayCopy')
            ->will($this->returnValue(array(
                                           'verify_token' => hash('sha256',
                                                                  'cba')
                                      )));

        $this->_get['hub_mode'] = 'unsubscribe';
        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(array('id' => 'verifytokenkey')))
            ->will($this->returnValue($this->_rowset));
        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($mockReturnValue));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->assertTrue($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfModeNotRecognisedFromHttpGetData()
    {
        $this->_get['hub_mode'] = 'abc';
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfLeaseSecondsMissedWhenModeIsSubscribeFromHttpGetData()
    {
        unset($this->_get['hub_lease_seconds']);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfHubTopicInvalidFromHttpGetData()
    {
        $this->_get['hub_topic'] = 'http://';
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfVerifyTokenRecordDoesNotExistForConfirmRequest()
    {
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfVerifyTokenRecordDoesNotAgreeWithConfirmRequest()
    {
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testRespondsToInvalidConfirmationWith404Response()
    {
        unset($this->_get['hub_mode']);
        $this->_callback->handle($this->_get);
        $this->assertTrue($this->_callback->getHttpResponse()->getStatusCode() == 404);
    }

    public function testRespondsToValidConfirmationWith200Response()
    {
        $this->_get['hub_mode'] = 'unsubscribe';
        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(array('id' => 'verifytokenkey')))
            ->will($this->returnValue($this->_rowset));

        $t = clone $this->now;
        $rowdata = array(
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => $t->getTimestamp(),
            'lease_seconds' => 10000
        );

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_tableGateway->expects($this->once())
            ->method('delete')
            ->with($this->equalTo(array('id' => 'verifytokenkey')))
            ->will($this->returnValue(true));

        $this->_callback->handle($this->_get);
        $this->assertTrue($this->_callback->getHttpResponse()->getStatusCode() == 200);
    }

    public function testRespondsToValidConfirmationWithBodyContainingHubChallenge()
    {
        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(array('id' => 'verifytokenkey')))
            ->will($this->returnValue($this->_rowset));

        $t = clone $this->now;
        $rowdata = array(
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => $t->getTimestamp(),
            'lease_seconds' => 10000
        );

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_tableGateway->expects($this->once())
            ->method('update')
            ->with(
            $this->equalTo(array('id'                => 'verifytokenkey',
                                 'verify_token'      => hash('sha256', 'cba'),
                                 'created_time'      => $t->getTimestamp(),
                                 'lease_seconds'     => 1234567,
                                 'subscription_state'=> 'verified',
                                 'expiration_time'   => $t->add(new DateInterval('PT1234567S'))
                                     ->format('Y-m-d H:i:s'))),
            $this->equalTo(array('id' => 'verifytokenkey'))
        );

        $this->_callback->handle($this->_get);
        $this->assertTrue($this->_callback->getHttpResponse()->getContent() == 'abc');
    }

    public function testRespondsToValidFeedUpdateRequestWith200Response()
    {
        $_SERVER['REQUEST_METHOD']     = 'POST';
        $_SERVER['REQUEST_URI']        = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']       = 'application/atom+xml';
        $feedXml                       = file_get_contents(__DIR__ . '/_files/atom10.xml');
        $GLOBALS['HTTP_RAW_POST_DATA'] = $feedXml; // dirty  alternative to php://input

        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(array('id' => 'verifytokenkey')))
            ->will($this->returnValue($this->_rowset));

        $rowdata = array(
            'id'           => 'verifytokenkey',
            'verify_token' => hash('sha256', 'cba'),
            'created_time' => time()
        );

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);

        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_callback->handle(array());
        $this->assertTrue($this->_callback->getHttpResponse()->getStatusCode() == 200);
    }

    public function testRespondsToInvalidFeedUpdateNotPostWith404Response()
    { // yes, this example makes no sense for GET - I know!!!
        $_SERVER['REQUEST_METHOD']     = 'GET';
        $_SERVER['REQUEST_URI']        = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']       = 'application/atom+xml';
        $feedXml                       = file_get_contents(__DIR__ . '/_files/atom10.xml');
        $GLOBALS['HTTP_RAW_POST_DATA'] = $feedXml;

        $this->_callback->handle(array());
        $this->assertTrue($this->_callback->getHttpResponse()->getStatusCode() == 404);
    }

    public function testRespondsToInvalidFeedUpdateWrongMimeWith404Response()
    {
        $_SERVER['REQUEST_METHOD']     = 'POST';
        $_SERVER['REQUEST_URI']        = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']       = 'application/kml+xml';
        $feedXml                       = file_get_contents(__DIR__ . '/_files/atom10.xml');
        $GLOBALS['HTTP_RAW_POST_DATA'] = $feedXml;
        $this->_callback->handle(array());
        $this->assertTrue($this->_callback->getHttpResponse()->getStatusCode() == 404);
    }

    /**
     * As a judgement call, we must respond to any successful request, regardless
     * of the wellformedness of any XML payload, by returning a 2xx response code.
     * The validation of feeds and their processing must occur outside the Hubbub
     * protocol.
     */
    public function testRespondsToInvalidFeedUpdateWrongFeedTypeForMimeWith200Response()
    {
        $_SERVER['REQUEST_METHOD']     = 'POST';
        $_SERVER['REQUEST_URI']        = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']       = 'application/rss+xml';
        $feedXml                       = file_get_contents(__DIR__ . '/_files/atom10.xml');
        $GLOBALS['HTTP_RAW_POST_DATA'] = $feedXml;

        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(array('id' => 'verifytokenkey')))
            ->will($this->returnValue($this->_rowset));

        $rowdata = array(
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => time(),
            'lease_seconds' => 10000
        );

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);


        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_callback->handle(array());
        $this->assertTrue($this->_callback->getHttpResponse()->getStatusCode() == 200);
    }

    public function testRespondsToValidFeedUpdateWithXHubOnBehalfOfHeader()
    {
        $_SERVER['REQUEST_METHOD']     = 'POST';
        $_SERVER['REQUEST_URI']        = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE']       = 'application/atom+xml';
        $feedXml                       = file_get_contents(__DIR__ . '/_files/atom10.xml');
        $GLOBALS['HTTP_RAW_POST_DATA'] = $feedXml;

        $this->_tableGateway->expects($this->any())
            ->method('select')
            ->with($this->equalTo(array('id' => 'verifytokenkey')))
            ->will($this->returnValue($this->_rowset));

        $rowdata = array(
            'id'            => 'verifytokenkey',
            'verify_token'  => hash('sha256', 'cba'),
            'created_time'  => time(),
            'lease_seconds' => 10000
        );

        $row = new ArrayObject($rowdata, ArrayObject::ARRAY_AS_PROPS);


        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_callback->handle(array());
        $this->assertTrue($this->_callback->getHttpResponse()->getHeader('X-Hub-On-Behalf-Of') == 1);
    }

    protected function _getCleanMock($className)
    {
        $class       = new \ReflectionClass($className);
        $methods     = $class->getMethods();
        $stubMethods = array();
        foreach ($methods as $method) {
            if ($method->isPublic() || ($method->isProtected()
                                        && $method->isAbstract())
            ) {
                $stubMethods[] = $method->getName();
            }
        }
        $mocked = $this->getMock(
            $className,
            $stubMethods,
            array(),
            str_replace('\\', '_', ($className . '_PubsubSubscriberMock_' . uniqid())),
            false
        );
        return $mocked;
    }

}
