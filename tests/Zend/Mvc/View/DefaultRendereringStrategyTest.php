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

    /**
     * @todo render PHTML strategy with layout
     * @todo render PHTML strategy with no layout
     * @todo render JSON strategy
     * @todo render Feed strategy as RSS
     * @todo render Feed strategy as Atom
     * @todo render with alternate strategy
     *
     * @todo render error for controller not found
     * @todo render error for controller invalid
     * @todo render error for exception detected
     * @todo render error for exception detected with display exceptions true
     * @todo render error without layout
     *
     * @todo render 404 with event result a response
     * @todo render 404 with non-404 response status
     * @todo render 404 with layout
     * @todo render 404 without layout
     *
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
