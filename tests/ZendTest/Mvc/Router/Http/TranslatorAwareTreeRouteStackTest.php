<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\I18n\Translator\Translator;
use Zend\Http\Request as Request;
use Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack;
use Zend\Uri\Http as HttpUri;

class TranslatorAwareTreeRouteStackTest extends TestCase
{
    public function testTranslatorAwareInterfaceImplementation()
    {
        $stack = new TranslatorAwareTreeRouteStack();
        $this->assertInstanceOf('Zend\I18n\Translator\TranslatorAwareInterface', $stack);

        // Defaults
        $this->assertNull($stack->getTranslator());
        $this->assertFalse($stack->hasTranslator());
        $this->assertEquals('default', $stack->getTranslatorTextDomain());
        $this->assertTrue($stack->isTranslatorEnabled());

        // Inject translator without text domain
        $translator = new Translator();
        $stack->setTranslator($translator);
        $this->assertSame($translator, $stack->getTranslator());
        $this->assertEquals('default', $stack->getTranslatorTextDomain());
        $this->assertTrue($stack->hasTranslator());

        // Reset translator
        $stack->setTranslator(null);
        $this->assertNull($stack->getTranslator());
        $this->assertFalse($stack->hasTranslator());

        // Inject translator with text domain
        $stack->setTranslator($translator, 'alternative');
        $this->assertSame($translator, $stack->getTranslator());
        $this->assertEquals('alternative', $stack->getTranslatorTextDomain());

        // Set text domain
        $stack->setTranslatorTextDomain('default');
        $this->assertEquals('default', $stack->getTranslatorTextDomain());

        // Disable translator
        $stack->setTranslatorEnabled(false);
        $this->assertFalse($stack->isTranslatorEnabled());
    }

    public function testTranslatorIsPassedThroughMatchMethod()
    {
        $translator = new Translator();
        $request    = new Request();

        $route = $this->getMock('Zend\Mvc\Router\Http\RouteInterface');
        $route->expects($this->once())
              ->method('match')
              ->with(
                  $this->equalTo($request),
                  $this->isNull(),
                  $this->equalTo(array('translator' => $translator, 'text_domain' => 'default'))
              );

        $stack = new TranslatorAwareTreeRouteStack();
        $stack->addRoute('test', $route);

        $stack->match($request, null, array('translator' => $translator));
    }

    public function testTranslatorIsPassedThroughAssembleMethod()
    {
        $translator = new Translator();
        $uri        = new HttpUri();

        $route = $this->getMock('Zend\Mvc\Router\Http\RouteInterface');
        $route->expects($this->once())
              ->method('assemble')
              ->with(
                  $this->equalTo(array()),
                  $this->equalTo(array('translator' => $translator, 'text_domain' => 'default', 'uri' => $uri))
              );

        $stack = new TranslatorAwareTreeRouteStack();
        $stack->addRoute('test', $route);

        $stack->assemble(array(), array('name' => 'test', 'translator' => $translator, 'uri' => $uri));
    }
}
