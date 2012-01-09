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
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Feed\PubSubHubbub\Subscriber;
use Zend\Feed\PubSubHubbub\Model;
use Zend\Feed\PubSubHubbub;
use Zend\Date;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Subsubhubbub
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CallbackTest extends \PHPUnit_Framework_TestCase
{

    protected $_originalServer = null;

    public function setUp()
    {
        $this->_callback = new \Zend\Feed\PubSubHubbub\Subscriber\Callback;

        $this->_adapter = $this->_getCleanMock(
            'Zend\Db\Adapter\AbstractAdapter'
        );
        $this->_tableGateway = $this->_getCleanMock(
            'Zend\Db\Table\AbstractTable'
        );
        $this->_rowset = $this->_getCleanMock(
            'Zend\Db\Table\AbstractRowset'
        );

        $this->_tableGateway->expects($this->any())->method('getAdapter')
            ->will($this->returnValue($this->_adapter));
        $storage = new Model\Subscription($this->_tableGateway);
        $this->_callback->setStorage($storage);

        $this->_get = array(
            'hub_mode' => 'subscribe',
            'hub_topic' => 'http://www.example.com/topic',
            'hub_challenge' => 'abc',
            'hub_verify_token' => 'cba',
            'hub_mode' => 'subscribe',
            'hub_lease_seconds' => '1234567'
        );

        $this->_originalServer = $_SERVER;
        $_SERVER['REQUEST_METHOD'] = 'get';
        $_SERVER['QUERY_STRING'] = 'xhub.subscription=verifytokenkey';
    }

    public function tearDown()
    {
        $_SERVER = $this->_originalServer;
    }


    public function testCanSetHttpResponseObject()
    {
        $this->_callback->setHttpResponse(new PubSubHubbub\HttpResponse);
        $this->assertTrue($this->_callback->getHttpResponse() instanceof PubSubHubbub\HttpResponse);
    }

    public function testCanUsesDefaultHttpResponseObject()
    {
        $this->assertTrue($this->_callback->getHttpResponse() instanceof PubSubHubbub\HttpResponse);
    }

    public function testThrowsExceptionOnInvalidHttpResponseObjectSet()
    {
        try {
            $this->_callback->setHttpResponse(new \stdClass);
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testThrowsExceptionIfNonObjectSetAsHttpResponseObject()
    {
        try {
            $this->_callback->setHttpResponse('');
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
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
        try {
            $this->_callback->setSubscriberCount(0);
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testThrowsExceptionOnSettingLessThanZeroAsSubscriberCount()
    {
        try {
            $this->_callback->setSubscriberCount(-1);
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testThrowsExceptionOnSettingAnyScalarTypeCastToAZeroOrLessIntegerAsSubscriberCount()
    {
        try {
            $this->_callback->setSubscriberCount('0aa');
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
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
        $mockReturnValue = $this->getMock('Result', array('toArray'));
        $mockReturnValue->expects($this->any())->method('toArray')->will($this->returnValue(array(
                'verify_token' => hash('sha256', 'cba')
            )));

        $this->_tableGateway->expects($this->any())
            ->method('find')
            ->with($this->equalTo('verifytokenkey'))
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
        $mockReturnValue = $this->getMock('Result', array('toArray'));
        $mockReturnValue->expects($this->any())->method('toArray')->will($this->returnValue(array(
                'verify_token' => hash('sha256', 'cba')
            )));

        $this->_get['hub_mode'] = 'unsubscribe';
        $this->_tableGateway->expects($this->any())
            ->method('find')
            ->with($this->equalTo('verifytokenkey'))
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
        //$this->_callback->setStorage(new Zend_Feed_PubSubHubbub_Subscriber_CallbackTestStorageHasNot);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testReturnsFalseIfVerifyTokenRecordDoesNotAgreeWithConfirmRequest()
    {
        //$this->_callback->setStorage(new Zend_Feed_PubSubHubbub_Subscriber_CallbackTestStorageHasButWrong);
        $this->assertFalse($this->_callback->isValidHubVerification($this->_get));
    }

    public function testRespondsToInvalidConfirmationWith404Response()
    {
        unset($this->_get['hub_mode']);
        $this->_callback->handle($this->_get);
        $this->assertTrue($this->_callback->getHttpResponse()->getHttpResponseCode() == 404);
    }

    public function testRespondsToValidConfirmationWith200Response()
    {
        $this->_get['hub_mode'] = 'unsubscribe';
        $this->_tableGateway->expects($this->any())
            ->method('find')
            ->with($this->equalTo('verifytokenkey'))
            ->will($this->returnValue($this->_rowset));

        $t = new Date\Date;
        $rowdata = array(
            'id' => 'verifytokenkey',
            'verify_token' => hash('sha256', 'cba'),
            'created_time' => $t->get(Date\Date::TIMESTAMP),
            'lease_seconds' => 10000
            );

        $row = new \Zend\Db\Table\Row(array('data' => $rowdata));

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
                $this->equalTo(array('id'=>'verifytokenkey','verify_token'=>hash('sha256', 'cba'),'created_time'=>$t->get(Date\Date::TIMESTAMP),'lease_seconds'=>1234567,'subscription_state'=>'verified','expiration_time'=>$t->add(1234567,Date\Date::SECOND)->get('yyyy-MM-dd HH:mm:ss'))),
                $this->equalTo('id = \'verifytokenkey\'')
            );
        $this->_adapter->expects($this->once())
            ->method('quoteInto')
            ->with($this->equalTo('id = ?'), $this->equalTo('verifytokenkey'))
            ->will($this->returnValue('id = \'verifytokenkey\''));

        $this->_callback->handle($this->_get);
        $this->assertTrue($this->_callback->getHttpResponse()->getHttpResponseCode() == 200);
    }

    public function testRespondsToValidConfirmationWithBodyContainingHubChallenge()
    {
        $this->_tableGateway->expects($this->any())
            ->method('find')
            ->with($this->equalTo('verifytokenkey'))
            ->will($this->returnValue($this->_rowset));

        $t = new Date\Date;
        $rowdata = array(
            'id' => 'verifytokenkey',
            'verify_token' => hash('sha256', 'cba'),
            'created_time' => $t->get(Date\Date::TIMESTAMP),
            'lease_seconds' => 10000
            );

        $row = new \Zend\Db\Table\Row(array('data' => $rowdata));

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
                $this->equalTo(array('id'=>'verifytokenkey','verify_token'=>hash('sha256', 'cba'),'created_time'=>$t->get(Date\Date::TIMESTAMP),'lease_seconds'=>1234567,'subscription_state'=>'verified','expiration_time'=>$t->add(1234567,Date\Date::SECOND)->get('yyyy-MM-dd HH:mm:ss'))),
                $this->equalTo('id = \'verifytokenkey\'')
            );
        $this->_adapter->expects($this->once())
            ->method('quoteInto')
            ->with($this->equalTo('id = ?'), $this->equalTo('verifytokenkey'))
            ->will($this->returnValue('id = \'verifytokenkey\''));
        $this->_callback->handle($this->_get);
        $this->assertTrue($this->_callback->getHttpResponse()->getBody() == 'abc');
    }

    public function testRespondsToValidFeedUpdateRequestWith200Response()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE'] = 'application/atom+xml';
        $feedXml = file_get_contents(__DIR__ . '/_files/atom10.xml');
        $GLOBALS['HTTP_RAW_POST_DATA'] = $feedXml; // dirty  alternative to php://input

        $this->_tableGateway->expects($this->any())
            ->method('find')
            ->with($this->equalTo('verifytokenkey'))
            ->will($this->returnValue($this->_rowset));

        $t = new Date\Date;
        $rowdata = array(
            'id' => 'verifytokenkey',
            'verify_token' => hash('sha256', 'cba'),
            'created_time' => time()
            );

        $row = new \Zend\Db\Table\Row(array('data' => $rowdata));

        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_callback->handle(array());
        $this->assertTrue($this->_callback->getHttpResponse()->getHttpResponseCode() == 200);
    }

    public function testRespondsToInvalidFeedUpdateNotPostWith404Response()
    {   // yes, this example makes no sense for GET - I know!!!
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE'] = 'application/atom+xml';
        $feedXml = file_get_contents(__DIR__ . '/_files/atom10.xml');
        $GLOBALS['HTTP_RAW_POST_DATA'] = $feedXml;

        $this->_callback->handle(array());
        $this->assertTrue($this->_callback->getHttpResponse()->getHttpResponseCode() == 404);
    }

    public function testRespondsToInvalidFeedUpdateWrongMimeWith404Response()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE'] = 'application/kml+xml';
        $feedXml = file_get_contents(__DIR__ . '/_files/atom10.xml');
        $GLOBALS['HTTP_RAW_POST_DATA'] = $feedXml;
        $this->_callback->handle(array());
        $this->assertTrue($this->_callback->getHttpResponse()->getHttpResponseCode() == 404);
    }

    /**
     * As a judgement call, we must respond to any successful request, regardless
     * of the wellformedness of any XML payload, by returning a 2xx response code.
     * The validation of feeds and their processing must occur outside the Hubbub
     * protocol.
     */
    public function testRespondsToInvalidFeedUpdateWrongFeedTypeForMimeWith200Response()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE'] = 'application/rss+xml';
        $feedXml = file_get_contents(__DIR__ . '/_files/atom10.xml');
        $GLOBALS['HTTP_RAW_POST_DATA'] = $feedXml;

        $this->_tableGateway->expects($this->any())
            ->method('find')
            ->with($this->equalTo('verifytokenkey'))
            ->will($this->returnValue($this->_rowset));

        $rowdata = array(
            'id' => 'verifytokenkey',
            'verify_token' => hash('sha256', 'cba'),
            'created_time' => time(),
            'lease_seconds' => 10000
            );

        $row = new \Zend\Db\Table\Row(array('data' => $rowdata));

        $this->_rowset->expects($this->any())
            ->method('current')
            ->will($this->returnValue($row));
        // require for the count call on the rowset in Model/Subscription
        $this->_rowset->expects($this->any())
            ->method('count')
            ->will($this->returnValue(1));

        $this->_callback->handle(array());
        $this->assertTrue($this->_callback->getHttpResponse()->getHttpResponseCode() == 200);
    }

    public function testRespondsToValidFeedUpdateWithXHubOnBehalfOfHeader()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/some/path/callback/verifytokenkey';
        $_SERVER['CONTENT_TYPE'] = 'application/atom+xml';
        $feedXml = file_get_contents(__DIR__ . '/_files/atom10.xml');
        $GLOBALS['HTTP_RAW_POST_DATA'] = $feedXml;

        $this->_tableGateway->expects($this->any())
            ->method('find')
            ->with($this->equalTo('verifytokenkey'))
            ->will($this->returnValue($this->_rowset));

        $rowdata = array(
            'id' => 'verifytokenkey',
            'verify_token' => hash('sha256', 'cba'),
            'created_time' => time(),
            'lease_seconds' => 10000
            );

        $row = new \Zend\Db\Table\Row(array('data' => $rowdata));

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
        $class = new \ReflectionClass($className);
        $methods = $class->getMethods();
        $stubMethods = array();
        foreach ($methods as $method) {
            if ($method->isPublic() || ($method->isProtected()
            && $method->isAbstract())) {
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

/**
 * Stubs for storage access
 * DEPRECATED
class Zend_Feed_PubSubHubbub_Subscriber_CallbackTestStorageHas implements Zend_Feed_PubSubHubbub_Storage_StorageInterface
{
    public function setSubscription($key, array $data){}
    public function getSubscription($key){
        if ($key == 'verifytokenkey') {
            return array(
                'id' => 'verifytokenkey',
                'verify_token' => hash('sha256', 'cba')
            );
        }
    }
    public function hasSubscription($key){return true;}
    public function removeSubscription($key){}
    public function cleanup($type){}
}
class Zend_Feed_PubSubHubbub_Subscriber_CallbackTestStorageHasNot implements Zend_Feed_PubSubHubbub_Storage_StorageInterface
{
    public function setSubscription($key, array $data){}
    public function getSubscription($key){}
    public function hasSubscription($key){return false;}
    public function removeSubscription($key){}
    public function cleanup($type){}
}
class Zend_Feed_PubSubHubbub_Subscriber_CallbackTestStorageHasButWrong implements Zend_Feed_PubSubHubbub_Storage_StorageInterface
{
    public function setSubscription($key, array $data){}
    public function getSubscription($key){return 'wrong';}
    public function hasSubscription($key){return true;}
    public function removeSubscription($key){}
    public function cleanup($type){}
}*/
