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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Feed/Pubsubhubbub/Subscriber.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Http/Client/Adapter/Socket.php';
require_once 'Zend/Uri/Http.php';

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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Pubsubhubbub_SubscriberHttpTest extends PHPUnit_Framework_TestCase
{

    protected $_subscriber = null;

    protected $_baseuri;

    protected $_client = null;

    protected $_adapter = null;

    protected $_config = array(
        'adapter'     => 'Zend_Http_Client_Adapter_Socket'
    );

    public function setUp()
    {
        if (defined('TESTS_Zend_Feed_Pubsubhubbub_BASEURI') &&
            Zend_Uri_Http::check(TESTS_Zend_Feed_Pubsubhubbub_BASEURI)) {
            $this->_baseuri = TESTS_Zend_Feed_Pubsubhubbub_BASEURI;
            if (substr($this->_baseuri, -1) != '/') $this->_baseuri .= '/';
            $name = $this->getName();
            if (($pos = strpos($name, ' ')) !== false) {
                $name = substr($name, 0, $pos);
            }
            $uri = $this->_baseuri . $name . '.php';
            $this->_adapter = new $this->_config['adapter'];
            $this->_client = new Zend_Http_Client($uri, $this->_config);
            $this->_client->setAdapter($this->_adapter);
            Zend_Feed_Pubsubhubbub::setHttpClient($this->_client);
            $this->_subscriber = new Zend_Feed_Pubsubhubbub_Subscriber;
            
            
            $this->_storage = $this->_getCleanMock('Zend_Feed_Pubsubhubbub_Entity_TopicSubscription');
            $this->_subscriber->setStorage($this->_storage);

        } else {
            // Skip tests
            $this->markTestSkipped("Zend_Feed_Pubsubhubbub_Subscriber dynamic tests'
            . ' are not enabled in TestConfiguration.php");
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
            $this->_client->getLastResponse()->getBody());
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
            $this->_client->getLastResponse()->getBody());
    }
    
    protected function _getCleanMock($className) {
        $class = new ReflectionClass($className);
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
            $className . '_SubscriberHttpTestMock_' . uniqid(),
            false
        );
        return $mocked;
    }

}
