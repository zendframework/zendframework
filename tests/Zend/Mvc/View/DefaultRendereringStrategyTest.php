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
    stdClass,
    Zend\EventManager\Event,
    Zend\EventManager\EventManager,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\View\DefaultRenderingStrategy,
    Zend\View\Model,
    Zend\View\PhpRenderer,
    Zend\View\Resolver\TemplateMapResolver,
    Zend\View\View;

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

    public function testLayoutIsSetByDefault()
    {
        $this->assertEquals('layout', $this->strategy->getDefaultLayout());
    }

    public function testLayoutIsMutable()
    {
        $this->strategy->setDefaultLayout('foobar');
        $this->assertEquals('foobar', $this->strategy->getDefaultLayout());
    }

    public function testErrorExceptionsAreNotDisplayedByDefault()
    {
        $this->assertFalse($this->strategy->displayExceptions());
    }

    public function testErrorExceptionDisplayFlagIsMutable()
    {
        $this->strategy->setDisplayExceptions('true');
        $this->assertTrue($this->strategy->displayExceptions());
    }

    public function testLayoutIsEnabledForErrorsByDefault()
    {
        $this->assertTrue($this->strategy->enableLayoutForErrors());
    }

    public function testErrorEnabledLayoutsAreMutable()
    {
        $this->strategy->setEnableLayoutForErrors(false);
        $this->assertFalse($this->strategy->enableLayoutForErrors());
    }

    public function testLayoutIncapableModelsIncludeJsonAndFeedByDefault()
    {
        $list = $this->strategy->getLayoutIncapableModels();
        $this->assertContains('Zend\View\Model\JsonModel', $list);
        $this->assertContains('Zend\View\Model\FeedModel', $list);
    }

    public function testLayoutIncapableModelsListIsMutable()
    {
        $this->strategy->setLayoutIncapableModels(array(
            'Zend\View\Model\ViewModel',
        ));
        $this->assertEquals(array('Zend\View\Model\ViewModel'), $this->strategy->getLayoutIncapableModels());
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

    public function testAttaches404RendererAtExpectedPriority()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $listeners = $events->getListeners('dispatch');

        $expectedCallback = array($this->strategy, 'render404');
        $expectedPriority = -1000;
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
        $this->assertTrue($found, '404 Renderer not found');
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

    public function testAttachesErrorRendererAtExpectedPriority()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $listeners = $events->getListeners('dispatch.error');

        $expectedCallback = array($this->strategy, 'renderError');
        $found            = false;
        foreach ($listeners as $listener) {
            $callback = $listener->getCallback();
            if ($callback === $expectedCallback) {
                $found = true;
            }
        }
        $this->assertTrue($found, 'Error Renderer not found');
    }

    public function testCanDetachListenersFromEventManager()
    {
        $events = new EventManager();
        $events->attachAggregate($this->strategy);
        $this->assertEquals(2, count($events->getListeners('dispatch')));
        $this->assertEquals(1, count($events->getListeners('dispatch.error')));

        $events->detachAggregate($this->strategy);
        $this->assertEquals(0, count($events->getListeners('dispatch')));
        $this->assertEquals(0, count($events->getListeners('dispatch.error')));
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

    public function testRendersContentInLayout()
    {
        $this->resolver->add('content', __DIR__ . '/_files/content.phtml');
        $model = new Model\ViewModel();
        $model->setOption('template', 'content')
              ->setOption('enable_layout', true);
        $this->event->setResult($model);
        $this->view->addRenderer($this->renderer);

        $result = $this->strategy->render($this->event);
        $this->assertSame($this->response, $result);
        $this->assertContains('<layout>content</layout>', $result->getContent());
    }

    public function testRendersContentByItselfWhenLayoutDisabled()
    {
        $this->resolver->add('content', __DIR__ . '/_files/content.phtml');
        $model = new Model\ViewModel();
        $model->setOption('template', 'content')
              ->setOption('enable_layout', false);
        $this->event->setResult($model);
        $this->view->addRenderer($this->renderer);

        $result = $this->strategy->render($this->event);
        $this->assertSame($this->response, $result);
        $this->assertContains('content', $result->getContent());
        $this->assertNotContains('<layout>', $result->getContent());
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

    public function testSets404StatusForControllerNotFoundError()
    {
        $this->resolver->add('pages/404', __DIR__ . '/_files/error.phtml');
        $this->view->addRenderer($this->renderer);
        $this->strategy->setEnableLayoutForErrors(false);
        $this->event->setError(Application::ERROR_CONTROLLER_NOT_FOUND);

        $result = $this->strategy->renderError($this->event);
        $this->assertSame($this->response, $result);

        $this->assertTrue($this->response->isNotFound());
        $this->assertContains('Page not found.', $this->response->getContent());
    }

    public function testSets404StatusForInvalidController()
    {
        $this->resolver->add('pages/404', __DIR__ . '/_files/error.phtml');
        $this->view->addRenderer($this->renderer);
        $this->strategy->setEnableLayoutForErrors(false);
        $this->event->setError(Application::ERROR_CONTROLLER_INVALID);

        $result = $this->strategy->renderError($this->event);
        $this->assertSame($this->response, $result);

        $this->assertTrue($this->response->isNotFound());
        $this->assertContains('Page not found.', $this->response->getContent());
    }

    public function testSets500StatusForDetectedException()
    {
        $this->resolver->add('error', __DIR__ . '/_files/error.phtml');
        $this->view->addRenderer($this->renderer);
        $this->strategy->setEnableLayoutForErrors(false);
        $this->strategy->setDisplayExceptions(false);
        $this->event->setError(Application::ERROR_EXCEPTION);
        $this->event->setParam('exception', new \Exception('Test exception'));

        $result = $this->strategy->renderError($this->event);
        $this->assertSame($this->response, $result);

        $this->assertTrue($this->response->isServerError());
        $content = $this->response->getContent();
        $this->assertContains('error occurred during execution', $content);
        $this->assertNotContains('Test exception', $content, $content);
    }

    public function testRendersStackTraceForDetectedExceptionWhenDisplayExceptionsEnabled()
    {
        $this->resolver->add('error', __DIR__ . '/_files/error.phtml');
        $this->view->addRenderer($this->renderer);
        $this->strategy->setEnableLayoutForErrors(false);
        $this->strategy->setDisplayExceptions(true);
        $this->event->setError(Application::ERROR_EXCEPTION);
        $this->event->setParam('exception', new \Exception('Test exception'));

        $result = $this->strategy->renderError($this->event);
        $this->assertSame($this->response, $result);

        $this->assertTrue($this->response->isServerError());
        $content = $this->response->getContent();
        $this->assertContains('error occurred during execution', $content);
        $this->assertContains('Test exception', $content, $content);
    }

    public function testErrorInjectedIntoLayoutWhenErrorLayoutsAreEnabled()
    {
        $this->resolver->add('error', __DIR__ . '/_files/error.phtml');
        $this->view->addRenderer($this->renderer);
        $this->strategy->setEnableLayoutForErrors(true);
        $this->strategy->setDisplayExceptions(true);
        $this->event->setError(Application::ERROR_EXCEPTION);
        $this->event->setParam('exception', new \Exception('Test exception'));

        $result = $this->strategy->renderError($this->event);
        $this->assertSame($this->response, $result);

        $this->assertTrue($this->response->isServerError());
        $content = $this->response->getContent();
        $this->assertContains('error occurred during execution', $content);
        $this->assertContains('Test exception', $content, $content);
        $this->assertContains('<layout>', $content, $content);
    }

    public function test404RendererIsSkippedIfEventResultIsAResponseObject()
    {
        $this->resolver->add('pages/404', __DIR__ . '/_files/error.phtml');
        $this->view->addRenderer($this->renderer);
        $this->strategy->setEnableLayoutForErrors(false);
        $this->strategy->setDisplayExceptions(false);

        $this->event->setResult($this->response);
        $result = $this->strategy->render404($this->event);
        $this->assertNull($result);
    }

    public function test404RendererIsSkippedIfNon404StatusDetected()
    {
        $this->resolver->add('pages/404', __DIR__ . '/_files/error.phtml');
        $this->view->addRenderer($this->renderer);
        $this->strategy->setEnableLayoutForErrors(false);
        $this->strategy->setDisplayExceptions(false);

        $this->response->setStatusCode(200);
        $result = $this->strategy->render404($this->event);
        $this->assertNull($result);
    }

    public function test404RendererWillRenderContentWhenLayoutsAreDisabled()
    {
        $this->resolver->add('pages/404', __DIR__ . '/_files/error.phtml');
        $this->view->addRenderer($this->renderer);
        $this->strategy->setEnableLayoutForErrors(false);
        $this->strategy->setDisplayExceptions(false);

        $this->response->setStatusCode(404);
        $result = $this->strategy->render404($this->event);
        $this->assertSame($this->response, $result);
        $this->assertEquals(404, $result->getStatusCode());
        $this->assertContains('Page not found.', $result->getContent());
    }

    public function test404RendererWillRenderContentWithLayout()
    {
        $this->resolver->add('pages/404', __DIR__ . '/_files/error.phtml');
        $this->view->addRenderer($this->renderer);
        $this->strategy->setEnableLayoutForErrors(true);
        $this->strategy->setDisplayExceptions(false);

        $this->response->setStatusCode(404);
        $result = $this->strategy->render404($this->event);
        $this->assertSame($this->response, $result);
        $this->assertEquals(404, $result->getStatusCode());
        $this->assertContains('Page not found.', $result->getContent());
        $this->assertContains('<layout>', $result->getContent());
    }

    /**
     * @todo selectLayout with non-viable view model
     * @todo selectLayout with XHR request
     * @todo selectLayout when layouts are disabled
     * @todo selectLayout with no PhpRenderer attached
     * @todo selectLayout with layout specified
     * @todo selectLayout with default layout
     *
     * @todo selectRendererByContext with JsonModel and no JsonRenderer attached
     * @todo selectRendererByContext with JsonModel and JsonRenderer attached
     * @todo selectRendererByContext with FeedModel and no FeedRenderer attached
     * @todo selectRendererByContext with FeedModel and FeedRenderer attached
     * @todo selectRendererByContext with ViewModel and JSON accept and no JsonRenderer attached
     * @todo selectRendererByContext with ViewModel and JSON accept and JsonRenderer attached
     * @todo selectRendererByContext with ViewModel and Rss accept and no FeedRenderer attached
     * @todo selectRendererByContext with ViewModel and Rss accept and FeedRenderer attached
     * @todo selectRendererByContext with ViewModel and Atom accept and no FeedRenderer attached
     * @todo selectRendererByContext with ViewModel and Atom accept and FeedRenderer attached
     * @todo selectRendererByContext with ViewModel and HTML accept and no PhpRenderer attached
     * @todo selectRendererByContext with ViewModel and HTML accept and PhpRenderer attached
     *
     * @todo populateResponse with empty result and empty placeholders
     * @todo populateResponse with empty result and filled article placeholder
     * @todo populateResponse with empty result and filled content placeholder
     * @todo populateResponse with empty result and filled article and content placeholders
     * @todo populateResponse with JsonRenderer selected
     * @todo populateResponse with FeedRenderer selected and RSS feed type
     * @todo populateResponse with FeedRenderer selected and Atom feed type
     */
}
