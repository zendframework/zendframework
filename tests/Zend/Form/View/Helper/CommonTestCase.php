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
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\View\HelperConfiguration;
use Zend\View\Helper\Doctype;
use Zend\View\Renderer\PhpRenderer;

/**
 * Abstract base test case for all form view helpers
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class CommonTestCase extends TestCase
{
    public $helper;
    public $renderer;

    public function setUp()
    {
        Doctype::unsetDoctypeRegistry();

        $this->renderer = new PhpRenderer;
        $helpers = $this->renderer->getHelperPluginManager();
        $config  = new HelperConfiguration();
        $config->configureServiceManager($helpers);

        $this->helper->setView($this->renderer);
    }

    public function testUsesUtf8ByDefault()
    {
        $this->assertEquals('UTF-8', $this->helper->getEncoding());
    }

    public function testCanInjectEncoding()
    {
        $this->helper->setEncoding('iso-8859-1');
        $this->assertEquals('iso-8859-1', $this->helper->getEncoding());
    }

    public function testInjectingEncodingProxiesToEscapeHelper()
    {
        $escape = $this->renderer->plugin('escapehtml');
        $this->helper->setEncoding('iso-8859-1');
        $this->assertEquals('iso-8859-1', $escape->getEncoding());
    }

    public function testAssumesHtml4LooseDoctypeByDefault()
    {
        $helperClass = get_class($this->helper);
        $helper = new $helperClass();
        $this->assertEquals(Doctype::HTML4_LOOSE, $helper->getDoctype());
    }

    public function testCanInjectDoctype()
    {
        $this->helper->setDoctype(Doctype::HTML5);
        $this->assertEquals(Doctype::HTML5, $this->helper->getDoctype());
    }

    public function testCanGetDoctypeFromDoctypeHelper()
    {
        $this->renderer->doctype(Doctype::XHTML1_STRICT);
        $this->assertEquals(Doctype::XHTML1_STRICT, $this->helper->getDoctype());
    }
}
