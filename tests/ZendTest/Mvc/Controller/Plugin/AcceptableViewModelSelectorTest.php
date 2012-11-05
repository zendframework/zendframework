<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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

        /** @var Zend\Mvc\Controller\Plugin\AcceptableViewModelSelector */
        $this->plugin = $this->controller->plugin('acceptableViewModelSelector');
    }

    public function testInvoke()
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
        $header   = Accept::fromString('Accept: text/plain; q=0.5, text/html, text/xml; q=0, text/x-dvi; q=0.8, text/x-c');
        $this->request->getHeaders()->addHeader($header);
        $result = $plugin($arr);

        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
    }

    public function testInvoke_2()
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


    public function testInvokeWithoutDefaults()
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

        $ref = null;
        $result = $plugin(array( 'Zend\View\Model\ViewModel' => '*/*'), false, $ref);
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $result);
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

        $result = $plugin->match(array( 'Zend\View\Model\ViewModel' => '*/*'));
        $this->assertInstanceOf(
                'Zend\Http\Header\Accept\FieldValuePart\AcceptFieldValuePart',
                $result
        );
    }

    public function testInvalidModel()
    {
        $arr = array('DoesNotExist' => 'text/xml');
        $header   = Accept::fromString('Accept: */*');
        $this->request->getHeaders()->addHeader($header);

        try {
            $result = $this->plugin->getViewModel($arr);
            $this->fail('Exception expected');
        } catch(\Zend\Mvc\Exception\InvalidArgumentException $e) {
            $this->assertEquals('The supplied View Model could not be found', $e->getMessage());
        }

    }
}
