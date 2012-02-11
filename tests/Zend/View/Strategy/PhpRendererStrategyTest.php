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
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Strategy;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request as HttpRequest,
    Zend\Http\Response as HttpResponse,
    Zend\View\Model,
    Zend\View\PhpRenderer,
    Zend\View\Strategy\PhpRendererStrategy,
    Zend\View\ViewEvent;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhpRendererStrategyTest extends TestCase
{
    public function setUp()
    {
        $this->renderer = new PhpRenderer;
        $this->strategy = new PhpRendererStrategy($this->renderer);
        $this->event    = new ViewEvent();
        $this->response = new HttpResponse();
    }

    public function testSelectRendererAlwaysSelectsPhpRenderer()
    {
        $result = $this->strategy->selectRenderer($this->event);
        $this->assertSame($this->renderer, $result);
    }

    protected function assertResponseNotInjected()
    {
        $content = $this->response->getContent();
        $headers = $this->response->headers();
        $this->assertTrue(empty($content));
        $this->assertFalse($headers->has('content-type'));
    }

    public function testNonMatchingRendererDoesNotInjectResponse()
    {
        $this->event->setResponse($this->response);

        // test empty renderer
        $this->strategy->injectResponse($this->event);
        $this->assertResponseNotInjected();

        // test non-matching renderer
        $renderer = new PhpRenderer();
        $this->event->setRenderer($renderer);
        $this->strategy->injectResponse($this->event);
        $this->assertResponseNotInjected();
    }
    
    public function testResponseContentSetToContentPlaceholderWhenResultAndArticlePlaceholderAreEmpty()
    {
        $this->renderer->placeholder('content')->set('Content');
        $event = new ViewEvent();
        $event->setResponse($this->response)
              ->setRenderer($this->renderer);

        $this->strategy->injectResponse($event);
        $content = $this->response->getContent();
        $this->assertEquals('Content', $content);
    }

    public function testResponseContentSetToArticlePlaceholderWhenResultIsEmptyAndBothArticleAndContentPlaceholdersSet()
    {
        $this->renderer->placeholder('article')->set('Article Content');
        $this->renderer->placeholder('content')->set('Content');
        $event = new ViewEvent();
        $event->setResponse($this->response)
              ->setRenderer($this->renderer);

        $this->strategy->injectResponse($event);
        $content = $this->response->getContent();
        $this->assertEquals('Article Content', $content);
    }

    public function testResponseContentSetToResultIfNotEmpty()
    {
        $this->renderer->placeholder('article')->set('Article Content');
        $this->renderer->placeholder('content')->set('Content');
        $event = new ViewEvent();
        $event->setResponse($this->response)
              ->setRenderer($this->renderer)
              ->setResult('Result Content');

        $this->strategy->injectResponse($event);
        $content = $this->response->getContent();
        $this->assertEquals('Result Content', $content);
    }
}
