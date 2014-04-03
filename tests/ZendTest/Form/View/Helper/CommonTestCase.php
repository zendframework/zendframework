<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\View\HelperConfig;
use Zend\View\Helper\Doctype;
use Zend\View\Renderer\PhpRenderer;

/**
 * Abstract base test case for all form view helpers
 */
abstract class CommonTestCase extends TestCase
{
    /**
     * @var \Zend\Form\View\Helper\AbstractHelper
     */
    public $helper;

    /**
     * @var \Zend\View\Renderer\RendererInterface
     */
    public $renderer;

    public function setUp()
    {
        Doctype::unsetDoctypeRegistry();

        $this->renderer = new PhpRenderer;
        $helpers = $this->renderer->getHelperPluginManager();
        $config  = new HelperConfig();
        $config->configureServiceManager($helpers);

        $this->helper->setView($this->renderer);
    }

    public function testUsesUtf8ByDefault()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->assertEquals('UTF-8', $this->helper->getEncoding());
    }

    public function testCanInjectEncoding()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->helper->setEncoding('iso-8859-1');
        $this->assertEquals('iso-8859-1', $this->helper->getEncoding());
    }

    public function testInjectingEncodingProxiesToEscapeHelper()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $escape = $this->renderer->plugin('escapehtml');
        $this->helper->setEncoding('iso-8859-1');
        $this->assertEquals('iso-8859-1', $escape->getEncoding());
    }

    public function testAssumesHtml4LooseDoctypeByDefault()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $helperClass = get_class($this->helper);
        $helper = new $helperClass();
        $this->assertEquals(Doctype::HTML4_LOOSE, $helper->getDoctype());
    }

    public function testCanInjectDoctype()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->helper->setDoctype(Doctype::HTML5);
        $this->assertEquals(Doctype::HTML5, $this->helper->getDoctype());
    }

    public function testCanGetDoctypeFromDoctypeHelper()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->renderer->doctype(Doctype::XHTML1_STRICT);
        $this->assertEquals(Doctype::XHTML1_STRICT, $this->helper->getDoctype());
    }
}
