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
use Zend\Form\Element;
use Zend\Form\View\HelperConfiguration;
use Zend\Form\View\Helper\FormCollection as FormCollectionHelper;
use Zend\View\Helper\Doctype;
use Zend\View\Renderer\PhpRenderer;
use ZendTest\Form\TestAsset\FormCollection;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FormCollectionTest extends TestCase
{
    public $helper;
    public $form;
    public $renderer;

    public function setUp()
    {
        $this->helper = new FormCollectionHelper();

        Doctype::unsetDoctypeRegistry();

        $this->renderer = new PhpRenderer;
        $helpers = $this->renderer->getHelperPluginManager();
        $config  = new HelperConfiguration();
        $config->configureServiceManager($helpers);

        $this->helper->setView($this->renderer);
    }

    public function getForm()
    {
        $form = new FormCollection();
        $form->prepare();

        return $form;
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    public function testCanGenerateTemplate()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(true);

        $markup = $this->helper->render($collection);
        $this->assertContains('<span data-template', $markup);
        $this->assertContains($collection->getTemplatePlaceholder(), $markup);
    }

    public function testDoesNotGenerateTemplateByDefault()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');
        $collection->setShouldCreateTemplate(false);

        $markup = $this->helper->render($collection);
        $this->assertNotContains('<span data-template', $markup);
    }

    public function testCorrectlyIndexElementsInCollection()
    {
        $form = $this->getForm();
        $collection = $form->get('colors');

        $markup = $this->helper->render($collection);
        $this->assertContains('name="colors[0]"', $markup);
        $this->assertContains('name="colors[1]"', $markup);
    }

    public function testCorrectlyIndexNestedElementsInCollection()
    {
        $form = $this->getForm();
        $collection = $form->get('fieldsets');

        $markup = $this->helper->render($collection);
        $this->assertContains('fieldsets[0][field]', $markup);
        $this->assertContains('fieldsets[1][field]', $markup);
        $this->assertContains('fieldsets[1][nested_fieldset][anotherField]', $markup);
    }
}
