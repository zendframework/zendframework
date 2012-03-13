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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

use Zend\View\Renderer\PhpRenderer as View,
    Zend\View\Helper\Fieldset;

/**
 * Test class for Zend_View_Helper_Fieldset
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class FieldsetTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->view   = new View();
        $this->helper = new Fieldset();
        $this->helper->setView($this->view);
        ob_start();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        ob_end_clean();
    }

    public function testFieldsetHelperCreatesFieldsetWithProvidedContent()
    {
        $html = $this->helper->__invoke('foo', 'foobar');
        $this->assertRegexp('#<fieldset[^>]+id="foo".*?>#', $html);
        $this->assertContains('</fieldset>', $html);
        $this->assertContains('foobar', $html);
    }

    public function testProvidingLegendOptionToFieldsetCreatesLegendTag()
    {
        $html = $this->helper->__invoke('foo', 'foobar', array('legend' => 'Great Scott!'));
        $this->assertRegexp('#<legend>Great Scott!</legend>#', $html);
    }

    /**
     * @group ZF-2913
     */
    public function testEmptyLegendShouldNotRenderLegendTag()
    {
        foreach (array(null, '', ' ', false) as $legend) {
            $html = $this->helper->__invoke('foo', 'foobar', array('legend' => $legend));
            $this->assertNotContains('<legend>', $html, 'Failed with value ' . var_export($legend, 1) . ': ' . $html);
        }
    }

    /**
     * @group ZF-3632
     */
    public function testHelperShouldAllowDisablingEscapingOfLegend()
    {
        $html = $this->helper->__invoke('foo', 'foobar', array('legend' => '<b>Great Scott!</b>', 'escape' => false));
        $this->assertRegexp('#<legend><b>Great Scott!</b></legend>#', $html, $html);
    }
}
