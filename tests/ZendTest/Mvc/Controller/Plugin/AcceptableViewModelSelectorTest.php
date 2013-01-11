<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AcceptableViewModelSelector;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Http\Header\Accept;
use ZendTest\Mvc\Controller\TestAsset\SampleController;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTests
 */
class AcceptableViewModelSelectorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->request = new Request();

        $event = new MvcEvent();
        $event->setRequest($this->request);
        $this->event = $event;

        $this->controller = new SampleController();
        $this->controller->setEvent($event);

        $this->plugin = $this->controller->plugin('acceptableViewModelSelector');
    }

    public function testHonorsAcceptPrecedenceAndPriorityWhenInvoked()
    {
        $arr = array(
            'Zend\View\Model\JsonModel' => array(
                'application/json',
                'application/javascript'
            ),
            'Zend\View\Model\FeedModel' => array(
                'application/rss+xml',
                'application/atom+xml'
            ),
            'Zend\View\Model\ViewModel' => '*/*'
        );

        $header   = Accept::fromString('Accept: text/plain; q=0.5, text/html, text/xml; q=0, text/x-dvi; q=0.8, text/x-c');
        $this->request->getHeaders()->addHeader($header);
        $plugin   = $this->plugin;
        $plugin->setDefaultViewModelName('Zend\View\Model\FeedModel');
        $result   = $plugin($arr);

        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertNotInstanceOf('Zend\View\Model\FeedModel', $result); // Ensure the default wasn't selected
        $this->assertNotInstanceOf('Zend\View\Model\JsonModel', $result);
    }

    public function testDefaultViewModelName()
    {
        $arr = array(
            'Zend\View\Model\JsonModel' => array(
                'application/json',
                'application/javascript'
            ),
            'Zend\View\Model\FeedModel' => array(
                'application/rss+xml',
                'application/atom+xml'
            ),
        );

        $header   = Accept::fromString('Accept: text/plain');
        $this->request->getHeaders()->addHeader($header);
        $plugin   = $this->plugin;
        $result   = $plugin->getViewModelName($arr);

        $this->assertEquals('Zend\View\Model\ViewModel', $result); //   Default Default View Model Name

        $plugin->setDefaultViewModelName('Zend\View\Model\FeedModel');
        $this->assertEquals($plugin->getDefaultViewModelName(), 'Zend\View\Model\FeedModel'); // Test getter along the way
        $this->assertInstanceOf('Zend\View\Model\FeedModel', $plugin($arr));
    }

    public function testSelectsViewModelBasedOnAcceptHeaderWhenInvokedAsFunctor()
    {
        $arr = array(
                'Zend\View\Model\JsonModel' => array(
                        'application/json',
                        'application/javascript'
                ),
                'Zend\View\Model\FeedModel' => array(
                        'application/rss+xml',
                        'application/atom+xml'
                ),
                'Zend\View\Model\ViewModel' => '*/*'
        );

        $plugin   = $this->plugin;
        $header   = Accept::fromString('Accept: application/rss+xml; version=0.2');
        $this->request->getHeaders()->addHeader($header);
        $result = $plugin($arr);

        $this->assertInstanceOf('Zend\View\Model\FeedModel', $result);
    }


    public function testInvokeWithoutDefaultsReturnsNullWhenNoMatchesOccur()
    {
        $arr = array(
                'Zend\View\Model\JsonModel' => array(
                        'application/json',
                        'application/javascript'
                ),
                'Zend\View\Model\FeedModel' => array(
                        'application/rss+xml',
                        'application/atom+xml'
                ),
        );

        $plugin   = $this->plugin;
        $header   = Accept::fromString('Accept: text/html; version=0.2');
        $this->request->getHeaders()->addHeader($header);

        $result = $plugin($arr, false);
        $this->assertNull($result);
    }

    public function testInvokeReturnsFieldValuePartOnMatchWhenReferenceProvided()
    {
        $plugin   = $this->plugin;
        $header   = Accept::fromString('Accept: text/html; version=0.2');
        $this->request->getHeaders()->addHeader($header);

        $ref = null;
        $result = $plugin(array( 'Zend\View\Model\ViewModel' => '*/*'), false, $ref);
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
        $this->assertNotInstanceOf('Zend\View\Model\JsonModel', $result);
        $this->assertNotInstanceOf('Zend\View\Model\FeedModel', $result);
        $this->assertInstanceOf('Zend\Http\Header\Accept\FieldValuePart\AcceptFieldValuePart', $ref);
    }

    public function testGetViewModelNameWithoutDefaults()
    {
        $arr = array(
                'Zend\View\Model\JsonModel' => array(
                        'application/json',
                        'application/javascript'
                ),
                'Zend\View\Model\FeedModel' => array(
                        'application/rss+xml',
                        'application/atom+xml'
                ),
        );

        $plugin   = $this->plugin;
        $header   = Accept::fromString('Accept: text/html; version=0.2');
        $this->request->getHeaders()->addHeader($header);

        $result = $plugin->getViewModelName($arr, false);
        $this->assertNull($result);

        $ref = null;
        $result = $plugin->getViewModelName(array( 'Zend\View\Model\ViewModel' => '*/*'), false, $ref);
        $this->assertEquals('Zend\View\Model\ViewModel', $result);
        $this->assertInstanceOf('Zend\Http\Header\Accept\FieldValuePart\AcceptFieldValuePart', $ref);
    }

    public function testMatch()
    {
        $plugin   = $this->plugin;
        $header   = Accept::fromString('Accept: text/html; version=0.2');
        $this->request->getHeaders()->addHeader($header);

        $arr = array( 'Zend\View\Model\ViewModel' => '*/*');
        $plugin->setDefaultMatchAgainst($arr);
        $this->assertEquals($plugin->getDefaultMatchAgainst(), $arr);
        $result = $plugin->match();
        $this->assertInstanceOf(
                'Zend\Http\Header\Accept\FieldValuePart\AcceptFieldValuePart',
                $result
        );
        $this->assertEquals($plugin->getDefaultMatchAgainst(), $arr);
    }

    public function testInvalidModel()
    {
        $arr = array('DoesNotExist' => 'text/xml');
        $header   = Accept::fromString('Accept: */*');
        $this->request->getHeaders()->addHeader($header);

        $this->setExpectedException('\Zend\Mvc\Exception\InvalidArgumentException');

        $this->plugin->getViewModel($arr);
    }
}
