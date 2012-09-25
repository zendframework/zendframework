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

use Zend\Mvc\Controller\Plugin\AcceptedModel;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Http\Header\Accept;
use ZendTest\Mvc\Controller\TestAsset\SampleController;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTests
 */
class AcceptedModelTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->request = new Request();

        $event = new MvcEvent();
        $event->setRequest($this->request);
        $this->event = $event;

        $this->controller = new SampleController();
        $this->controller->setEvent($event);

        /** @var Zend\Mvc\Controller\Plugin\AcceptedModel */
        $this->plugin = $this->controller->plugin('acceptedModel');
    }

    public function test_TestingTheTestSetup()
    {
        $plugin   = $this->plugin;
        $header   = Accept::fromString('Accept: text/plain; q=0.5, text/html, text/xml; q=0, text/x-dvi; q=0.8, text/x-c');
        $this->request->getHeaders()->addHeader($header);
        $result = $plugin(array(
            'Zend\View\Model\JsonModel' => array(
                'application/json',
                'application/javascript'
            ),
            'Zend\View\Model\FeedModel' => array(
                'application/rss+xml',
                'application/atom+xml'
            ),
            'Zend\View\Model\ViewModel' => '*/*'
        ));
    }
}
