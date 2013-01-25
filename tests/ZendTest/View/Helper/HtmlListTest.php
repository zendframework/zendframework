<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use Zend\View\Helper;
use Zend\View\Renderer\PhpRenderer as View;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class HtmlListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_HtmlList
     */
    public $helper;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->view   = new View();
        $this->helper = new Helper\HtmlList();
        $this->helper->setView($this->view);
    }

    public function tearDown()
    {
        unset($this->helper);
    }

    public function testMakeUnorderedList()
    {
        $items = array('one', 'two', 'three');

        $list = $this->helper->__invoke($items);

        $this->assertContains('<ul>', $list);
        $this->assertContains('</ul>', $list);
        foreach ($items as $item) {
            $this->assertContains('<li>' . $item . '</li>', $list);
        }
    }

    public function testMakeOrderedList()
    {
        $items = array('one', 'two', 'three');

        $list = $this->helper->__invoke($items, true);

        $this->assertContains('<ol>', $list);
        $this->assertContains('</ol>', $list);
        foreach ($items as $item) {
            $this->assertContains('<li>' . $item . '</li>', $list);
        }
    }

    public function testMakeUnorderedListWithAttribs()
    {
        $items = array('one', 'two', 'three');
        $attribs = array('class' => 'selected', 'name' => 'list');

        $list = $this->helper->__invoke($items, false, $attribs);

        $this->assertContains('<ul', $list);
        $this->assertContains('class="selected"', $list);
        $this->assertContains('name="list"', $list);
        $this->assertContains('</ul>', $list);
        foreach ($items as $item) {
            $this->assertContains('<li>' . $item . '</li>', $list);
        }
    }

    public function testMakeOrderedListWithAttribs()
    {
        $items = array('one', 'two', 'three');
        $attribs = array('class' => 'selected', 'name' => 'list');

        $list = $this->helper->__invoke($items, true, $attribs);

        $this->assertContains('<ol', $list);
        $this->assertContains('class="selected"', $list);
        $this->assertContains('name="list"', $list);
        $this->assertContains('</ol>', $list);
        foreach ($items as $item) {
            $this->assertContains('<li>' . $item . '</li>', $list);
        }
    }

    /*
     * @group ZF-5018
     */
    public function testMakeNestedUnorderedList()
    {
        $items = array('one', array('four', 'five', 'six'), 'two', 'three');

        $list = $this->helper->__invoke($items);

        $this->assertContains('<ul>' . Helper\HtmlList::EOL, $list);
        $this->assertContains('</ul>' . Helper\HtmlList::EOL, $list);
        $this->assertContains('one<ul>' . Helper\HtmlList::EOL.'<li>four', $list);
        $this->assertContains('<li>six</li>' . Helper\HtmlList::EOL . '</ul>' .
            Helper\HtmlList::EOL . '</li>' . Helper\HtmlList::EOL . '<li>two', $list);
    }

    /*
     * @group ZF-5018
     */
    public function testMakeNestedDeepUnorderedList()
    {
        $items = array('one', array('four', array('six', 'seven', 'eight'), 'five'), 'two', 'three');

        $list = $this->helper->__invoke($items);

        $this->assertContains('<ul>' . Helper\HtmlList::EOL, $list);
        $this->assertContains('</ul>' . Helper\HtmlList::EOL, $list);
        $this->assertContains('one<ul>' . Helper\HtmlList::EOL . '<li>four', $list);
        $this->assertContains('<li>four<ul>' . Helper\HtmlList::EOL . '<li>six', $list);
        $this->assertContains('<li>five</li>' . Helper\HtmlList::EOL . '</ul>' .
            Helper\HtmlList::EOL . '</li>' . Helper\HtmlList::EOL . '<li>two', $list);
    }

    public function testListWithValuesToEscapeForZF2283()
    {
        $items = array('one <small> test', 'second & third', 'And \'some\' "final" test');

        $list = $this->helper->__invoke($items);

        $this->assertContains('<ul>', $list);
        $this->assertContains('</ul>', $list);

        $this->assertContains('<li>one &lt;small&gt; test</li>', $list);
        $this->assertContains('<li>second &amp; third</li>', $list);
        $this->assertContains('<li>And &#039;some&#039; &quot;final&quot; test</li>', $list);
    }

    public function testListEscapeSwitchedOffForZF2283()
    {
        $items = array('one <b>small</b> test');

        $list = $this->helper->__invoke($items, false, false, false);

        $this->assertContains('<ul>', $list);
        $this->assertContains('</ul>', $list);

        $this->assertContains('<li>one <b>small</b> test</li>', $list);
    }

    /**
     * @group ZF-2527
     */
    public function testEscapeFlagHonoredForMultidimensionalLists()
    {
        $items = array('<b>one</b>', array('<b>four</b>', '<b>five</b>', '<b>six</b>'), '<b>two</b>', '<b>three</b>');

        $list = $this->helper->__invoke($items, false, false, false);

        foreach ($items[1] as $item) {
            $this->assertContains($item, $list);
        }
    }

    /**
     * @group ZF-2527
     * Added the s modifier to match newlines after ZF-5018
     */
    public function testAttribsPassedIntoMultidimensionalLists()
    {
        $items = array('one', array('four', 'five', 'six'), 'two', 'three');

        $list = $this->helper->__invoke($items, false, array('class' => 'foo'));

        foreach ($items[1] as $item) {
            $this->assertRegexp('#<ul[^>]*?class="foo"[^>]*>.*?(<li>' . $item . ')#s', $list);
        }

    }

    /**
     * @group ZF-2870
     */
    public function testEscapeFlagShouldBePassedRecursively()
    {
        $items = array(
            '<b>one</b>',
            array(
                '<b>four</b>',
                '<b>five</b>',
                '<b>six</b>',
                array(
                    '<b>two</b>',
                    '<b>three</b>',
                ),
            ),
        );

        $list = $this->helper->__invoke($items, false, false, false);

        $this->assertContains('<ul>', $list);
        $this->assertContains('</ul>', $list);

        array_walk_recursive($items, array($this, 'validateItems'), $list);
    }

    public function validateItems($value, $key, $userdata)
    {
        $this->assertContains('<li>' . $value, $userdata);
    }
}
