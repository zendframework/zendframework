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
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mvc\View;

use PHPUnit_Framework_TestCase as TestCase,
    ReflectionClass,
    stdClass,
    Zend\EventManager\Event,
    Zend\EventManager\EventManager,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\View\DefaultRenderingStrategy,
    Zend\Registry,
    Zend\View\Helper\Placeholder\Registry as PlaceholderRegistry,
    Zend\View\Model,
    Zend\View\PhpRenderer,
    Zend\View\Renderer\FeedRenderer,
    Zend\View\Renderer\JsonRenderer,
    Zend\View\Resolver\TemplateMapResolver,
    Zend\View\View,
    Zend\View\ViewEvent;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DefaultRenderingStrategyTest extends TestCase
{
    protected $event;
    protected $request;
    protected $response;
    protected $view;

    public function setUp()
    {
        // Necessary to ensure placeholders do not persist between individual tests
        if (Registry::isRegistered(PlaceholderRegistry::REGISTRY_KEY)) {
            Registry::getInstance()->offsetUnset(PlaceholderRegistry::REGISTRY_KEY);
        }

        $this->view     = new View();
        $this->request  = new Request();
        $this->response = new Response();
        $this->event    = new MvcEvent();
        $this->resolver = new TemplateMapResolver(array(
            'layout' => __DIR__ . '/_files/layout.phtml',
        ));
        $this->renderer = new PhpRenderer();
        $this->renderer->setResolver($this->resolver);

        $this->event->setRequest($this->request)
                    ->setResponse($this->response);

        $this->strategy = new DefaultRenderingStrategy($this->view);
    }

    public function testEnablesDefaultRenderingStrategiesByDefault()
    {
        $this->assertTrue($this->strategy->useDefaultRenderingStrategy());
    }

    public function testFlagEnablingDefaultRenderingStrategiesIsMutable()
    {
        $this->strategy->setUseDefaultRenderingStrategy(false);
        $this->assertFalse($this->strategy->useDefaultRenderingStrategy());
    }

    public function testAttachesRendererAtExpectedPriority()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $listeners = $events->getListeners('dispatch');

        $expectedCallback = array($this->strategy, 'render');
        $expectedPriority = -10000;
        $found            = false;
        foreach ($listeners as $listener) {
            $callback = $listener->getCallback();
            if ($callback === $expectedCallback) {
                if ($listener->getMetadatum('priority') == $expectedPriority) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Renderer not found');
    }

    public function testCanDetachListenersFromEventManager()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $this->assertEquals(1, count($events->getListeners('dispatch')));

        $events->detachAggregate($this->strategy);
        $this->assertEquals(0, count($events->getListeners('dispatch')));
    }

    public function testRenderReturnsNullForNonMvcEvent()
    {
        $event = new Event();
        $result = $this->strategy->render($event);
        $this->assertNull($result);
    }

    public function testRenderReturnsNullWhenModelIsNotDerivedFromViewModelOrArrayOrTraversable()
    {
        $this->event->setResult(new stdClass);
        $result = $this->strategy->render($this->event);
        $this->assertNull($result);
    }

    public function testRendersJson()
    {
        $model = new Model\JsonModel();
        $model->setVariable('foo', 'bar');
        $this->event->setResult($model);

        $result = $this->strategy->render($this->event);
        $this->assertSame($this->response, $result);
        $this->assertContains(json_encode(array('foo' => 'bar')), $result->getContent());
    }

    protected function getFeedData($type)
    {
        return array(
            'copyright' => date('Y'),
            'date_created' => time(),
            'date_modified' => time(),
            'last_build_date' => time(),
            'description' => __CLASS__,
            'id' => 'http://framework.zend.com/',
            'language' => 'en_US',
            'feed_link' => array(
                'link' => 'http://framework.zend.com/feed.xml',
                'type' => $type,
            ),
            'link' => 'http://framework.zend.com/feed.xml',
            'title' => 'Testing',
            'encoding' => 'UTF-8',
            'base_url' => 'http://framework.zend.com/',
            'entries' => array(
                array(
                    'content' => 'test content',
                    'date_created' => time(),
                    'date_modified' => time(),
                    'description' => __CLASS__,
                    'id' => 'http://framework.zend.com/1',
                    'link' => 'http://framework.zend.com/1',
                    'title' => 'Test 1',
                ),
                array(
                    'content' => 'test content',
                    'date_created' => time(),
                    'date_modified' => time(),
                    'description' => __CLASS__,
                    'id' => 'http://framework.zend.com/2',
                    'link' => 'http://framework.zend.com/2',
                    'title' => 'Test 2',
                ),
            ),
        );
    }

    public function testRenderRssFeed()
    {
        $model = new Model\FeedModel();
        $model->setVariables($this->getFeedData('rss'));
        $model->setOption('feed_type', 'rss');
        $this->event->setResult($model);

        $result = $this->strategy->render($this->event);
        $this->assertSame($this->response, $result);

        $feed = $model->getFeed();

        $this->assertContains($feed->export('rss'), $result->getContent());
    }

    public function testRenderAtomFeed()
    {
        $model = new Model\FeedModel();
        $model->setVariables($this->getFeedData('atom'));
        $model->setOption('feed_type', 'atom');
        $this->event->setResult($model);

        $result = $this->strategy->render($this->event);
        $this->assertSame($this->response, $result);

        $feed = $model->getFeed();

        $this->assertContains($feed->export('atom'), $result->getContent());
    }

    public function testWillRenderAlternateStrategyWhenSelected()
    {
        $renderer = new TestAsset\DumbStrategy();
        $this->view->addRenderingStrategy(function ($e) use ($renderer) {
            return $renderer;
        }, 100);
        $model = new Model\ViewModel(array('foo' => 'bar'));
        $model->setOption('template', 'content');
        $this->event->setResult($model);

        $result = $this->strategy->render($this->event);
        $this->assertSame($this->response, $result);

        $expected = sprintf('content (%s): %s', json_encode(array('template' => 'content')), json_encode(array('foo' => 'bar')));
    }

    public function makeRendererExtensionsVisible()
    {
        $r = new ReflectionClass($this->renderer);
        $prop = $r->getProperty('extensions');
        $prop->setAccessible(true);
        $this->assertEquals(array(), $prop->getValue($this->renderer));
        $this->extensions = $prop;
    }

    public function testPhpRendererIsSelectedForJsonModelIfNoJsonRendererAttached()
    {
        $model = new Model\JsonModel();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);
        $this->view->addRenderer($this->renderer);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertSame($this->renderer, $result);
    }

    public function testJsonRendererIsSelectedForJsonModel()
    {
        $model = new Model\JsonModel();
        $json  = new JsonRenderer();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);
        $this->view->addRenderer($this->renderer);
        $this->view->addRenderer($json);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertSame($json, $result);
    }

    public function testPhpRendererIsSelectedForFeedModelIfNoFeedRendererAttached()
    {
        $model = new Model\FeedModel();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);
        $this->view->addRenderer($this->renderer);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertSame($this->renderer, $result);
    }

    public function testFeedRendererIsSelectedForFeedModel()
    {
        $model = new Model\FeedModel();
        $feed  = new FeedRenderer();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);
        $this->view->addRenderer($this->renderer);
        $this->view->addRenderer($feed);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertSame($feed, $result);
    }

    public function testPhpRendererIsSelectedForJsonAcceptIfNoJsonRendererAttached()
    {
        $this->request->headers()->addHeaderLine('Accept', 'application/json');
        $model = new Model\ViewModel();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);
        $this->view->addRenderer($this->renderer);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertSame($this->renderer, $result);
    }

    public function testJsonRendererIsSelectedForJsonAccept()
    {
        $this->request->headers()->addHeaderLine('Accept', 'application/json');
        $model = new Model\ViewModel();
        $json  = new JsonRenderer();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);
        $this->view->addRenderer($this->renderer);
        $this->view->addRenderer($json);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertSame($json, $result);
    }

    public function testPhpRendererIsSelectedForRssAcceptIfNoFeedRendererAttached()
    {
        $this->request->headers()->addHeaderLine('Accept', 'application/rss+xml');
        $model = new Model\ViewModel();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);
        $this->view->addRenderer($this->renderer);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertSame($this->renderer, $result);
    }

    public function testFeedRendererIsSelectedForRssAccept()
    {
        $this->request->headers()->addHeaderLine('Accept', 'application/rss+xml');
        $model = new Model\ViewModel();
        $feed  = new FeedRenderer();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);
        $this->view->addRenderer($this->renderer);
        $this->view->addRenderer($feed);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertSame($feed, $result);
    }

    public function testPhpRendererIsSelectedForAtomAcceptIfNoFeedRendererAttached()
    {
        $this->request->headers()->addHeaderLine('Accept', 'application/atom+xml');
        $model = new Model\ViewModel();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);
        $this->view->addRenderer($this->renderer);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertSame($this->renderer, $result);
    }

    public function testFeedRendererIsSelectedForAtomAccept()
    {
        $this->request->headers()->addHeaderLine('Accept', 'application/atom+xml');
        $model = new Model\ViewModel();
        $feed  = new FeedRenderer();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);
        $this->view->addRenderer($this->renderer);
        $this->view->addRenderer($feed);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertSame($feed, $result);
    }

    public function testCreatesAndAttachesPhpRendererIfNoneSetAndOtherAcceptRulesDoNotApply()
    {
        $model = new Model\ViewModel();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertInstanceOf('Zend\View\PhpRenderer', $result);
        $this->assertNotSame($this->renderer, $result);
    }

    public function testSelectsPhpRendererIfAcceptRulesDoNotApply()
    {
        $model = new Model\ViewModel();
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setModel($model);
        $this->view->addRenderer($this->renderer);

        $result = $this->strategy->selectRendererByContext($event);
        $this->assertSame($this->renderer, $result);
    }

    public function testResponseContentIsEmptyWhenResultAndPlaceholdersAreEmpty()
    {
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response);

        $this->strategy->populateResponse($event);
        $content = $this->response->getContent();
        $this->assertTrue(empty($content));
    }

    public function testResponseContentSetToArticlePlaceholderWhenResultIsEmpty()
    {
        $this->renderer->placeholder('article')->set('Article Content');
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setRenderer($this->renderer);

        $this->strategy->populateResponse($event);
        $content = $this->response->getContent();
        $this->assertEquals('Article Content', $content);
    }

    public function testResponseContentSetToContentPlaceholderWhenResultAndArticlePlaceholderAreEmpty()
    {
        $this->renderer->placeholder('content')->set('Content');
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setRenderer($this->renderer);

        $this->strategy->populateResponse($event);
        $content = $this->response->getContent();
        $this->assertEquals('Content', $content);
    }

    public function testResponseContentSetToArticlePlaceholderWhenResultIsEmptyAndBothArticleAndContentPlaceholdersSet()
    {
        $this->renderer->placeholder('article')->set('Article Content');
        $this->renderer->placeholder('content')->set('Content');
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setRenderer($this->renderer);

        $this->strategy->populateResponse($event);
        $content = $this->response->getContent();
        $this->assertEquals('Article Content', $content);
    }

    public function testResponseContentSetToResultIfNotEmpty()
    {
        $this->renderer->placeholder('article')->set('Article Content');
        $this->renderer->placeholder('content')->set('Content');
        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setRenderer($this->renderer)
              ->setResult('Result Content');

        $this->strategy->populateResponse($event);
        $content = $this->response->getContent();
        $this->assertEquals('Result Content', $content);
    }

    public function testResponseContentSetToJsonResultAndContentTypeHeaderSetWhenJsonRendererSelected()
    {
        $content  = json_encode(array('foo' => 'bar'));
        $renderer = new JsonRenderer();

        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setRenderer($renderer)
              ->setResult($content);

        $this->strategy->populateResponse($event);
        $result = $this->response->getContent();
        $this->assertEquals($content, $result);
        $this->assertTrue($this->response->headers()->has('content-type'));
        $this->assertEquals('application/json', $this->response->headers()->get('content-type')->getFieldValue());
    }

    public function testResponseContentSetToResultValueAndContentTypeHeaderSetToRssWhenFeedRendererSelected()
    {
        $content  = 'should be xml';
        $renderer = new FeedRenderer();
        $renderer->setFeedType('rss');

        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setRenderer($renderer)
              ->setResult($content);

        $this->strategy->populateResponse($event);
        $result = $this->response->getContent();
        $this->assertEquals($content, $result);
        $this->assertTrue($this->response->headers()->has('content-type'));
        $this->assertEquals('application/rss+xml', $this->response->headers()->get('content-type')->getFieldValue());
    }

    public function testResponseContentSetToResultValueAndContentTypeHeaderSetToAtomWhenFeedRendererSelected()
    {
        $content  = 'should be xml';
        $renderer = new FeedRenderer();
        $renderer->setFeedType('atom');

        $event = new ViewEvent();
        $event->setTarget($this->view)
              ->setRequest($this->request)
              ->setResponse($this->response)
              ->setRenderer($renderer)
              ->setResult($content);

        $this->strategy->populateResponse($event);
        $result = $this->response->getContent();
        $this->assertEquals($content, $result);
        $this->assertTrue($this->response->headers()->has('content-type'));
        $this->assertEquals('application/atom+xml', $this->response->headers()->get('content-type')->getFieldValue());
    }
}
