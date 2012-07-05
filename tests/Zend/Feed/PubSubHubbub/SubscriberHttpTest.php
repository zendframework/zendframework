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

namespace ZendTest\Feed\PubSubHubbub;

use Zend\Feed\PubSubHubbub\PubSubHubbub;
use Zend\Feed\PubSubHubbub\Subscriber;
use Zend\Http\Client as HttpClient;

/**
 * Note that $this->_baseuri must point to a directory on a web server
 * containing all the files under the _files directory. You should symlink
 * or copy these files and set '_baseuri' properly using the constant in
 * TestConfiguration.php (based on TestConfiguration.php.dist)
 *
 * You can also set the proper constant in your test configuration file to
 * point to the right place.
 *
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Subsubhubbub
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SubscriberHttpTest extends \PHPUnit_Framework_TestCase
{

    /** @var Subscriber */
    protected $_subscriber = null;

    /** @var string */
    protected $_baseuri;

    /** @var HttpClient */
    protected $_client = null;

    protected $_storage;

    public function setUp()
    {
        $this->_baseuri = constant('TESTS_ZEND_FEED_PUBSUBHUBBUB_BASEURI');
        if ($this->_baseuri) {
            if (substr($this->_baseuri, -1) != '/') {
                $this->_baseuri .= '/';
            }
            $name = $this->getName();
            if (($pos = strpos($name, ' ')) !== false) {
                $name = substr($name, 0, $pos);
            }
            $uri = $this->_baseuri . $name . '.php';
            $this->_client = new HttpClient($uri);
            $this->_client->setAdapter('\Zend\Http\Client\Adapter\Socket');
            PubSubHubbub::setHttpClient($this->_client);
            $this->_subscriber = new Subscriber;
            
            $this->_storage = $this->_getCleanMock('\Zend\Feed\PubSubHubbub\Model\Subscription');
            $this->_subscriber->setStorage($this->_storage);

        } else {
            // Skip tests
            $this->markTestSkipped('Zend\Feed\PubSubHubbub\Subscriber dynamic tests are not enabled in TestConfiguration.php');
        }
    }

    public function testSubscriptionRequestSendsExpectedPostData()
    {
        $this->_subscriber->setTopicUrl('http://www.example.com/topic');
        $this->_subscriber->addHubUrl($this->_baseuri . '/testRawPostData.php');
        $this->_subscriber->setCallbackUrl('http://www.example.com/callback');
        $this->_subscriber->setTestStaticToken('abc'); // override for testing
        $this->_subscriber->subscribeAll();
        $this->assertEquals(
            'hub.callback=http%3A%2F%2Fwww.example.com%2Fcallback%3Fxhub.subscription%3D5536df06b5d'
            .'cb966edab3a4c4d56213c16a8184b&hub.lease_seconds=2592000&hub.mode='
            .'subscribe&hub.topic=http%3A%2F%2Fwww.example.com%2Ftopic&hub.veri'
            .'fy=sync&hub.verify=async&hub.verify_token=abc',
            $this->_client->getResponse()->getBody());
    }

    public function testUnsubscriptionRequestSendsExpectedPostData()
    {
        $this->_subscriber->setTopicUrl('http://www.example.com/topic');
        $this->_subscriber->addHubUrl($this->_baseuri . '/testRawPostData.php');
        $this->_subscriber->setCallbackUrl('http://www.example.com/callback');
        $this->_subscriber->setTestStaticToken('abc'); //override for testing
        $this->_subscriber->unsubscribeAll();
        $this->assertEquals(
            'hub.callback=http%3A%2F%2Fwww.example.com%2Fcallback%3Fxhub.subscription%3D5536df06b5d'
            .'cb966edab3a4c4d56213c16a8184b&hub.mode=unsubscribe&hub.topic=http'
            .'%3A%2F%2Fwww.example.com%2Ftopic&hub.verify=sync&hub.verify=async'
            .'&hub.verify_token=abc',
            $this->_client->getResponse()->getBody());
    }
    
    protected function _getCleanMock($className) {
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
            $stubMethods
        );
        return $mocked;
    }

}
