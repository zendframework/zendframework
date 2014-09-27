<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Helper;

use Zend\View\Renderer\PhpRenderer as View;
use Zend\View\Helper\HtmlTag;

/**
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class HtmlTagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HtmlTag
     */
    public $helper;

    protected function setUp()
    {
        $this->view   = new View();
        $this->helper = new HtmlTag();
        $this->helper->setView($this->view);
    }

    public function tearDown()
    {
        unset($this->helper);
    }
    
    protected function assertAttribute($name, $value = null)
    {
        $attributes = $this->helper->getAttributes();
        $this->assertArrayHasKey($name, $attributes);
        if ($value) {
            $this->assertEquals($value, $attributes[$name]);
        }
    }

    public function testSettingSingleAttribute()
    {
        $this->helper->setAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
        $this->assertAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
    }
    
    public function testAddingMultipleAttributes()
    {
        $attribs = array(
            'xmlns' => 'http://www.w3.org/1999/xhtml',
            'prefix' => 'og: http://ogp.me/ns#',
        );
        $this->helper->addAttributes($attribs);
        
        foreach ($attribs as $name => $value) {
            $this->assertAttribute($name, $value);
        }
    }
    
    public function testRenderingOpenTagWithNoAttributes()
    {
        $this->assertEquals('<html>', $this->helper->openTag());
    }
    
    public function testRenderingOpenTagWithAttributes()
    {
        $attribs = array(
            'xmlns' => 'http://www.w3.org/1999/xhtml',
            'xmlns:og' => 'http://ogp.me/ns#',
        );
        
        $this->helper->addAttributes($attribs);
        
        $tag = $this->helper->openTag();
        
        $this->assertStringStartsWith('<html', $tag);
        
        $escape = $this->view->plugin('escapehtmlattr');
        foreach ($attribs as $name => $value) {
            $this->assertContains(sprintf('%s="%s"', $name, $escape($value)), $tag);
        }
    }
    
    public function testRenderingCloseTag()
    {
        $this->assertEquals('</html>', $this->helper->closeTag());
    }
}
