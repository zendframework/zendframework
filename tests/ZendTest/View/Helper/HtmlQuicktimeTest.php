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
use Zend\View\Helper\HtmlQuicktime;

/**
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class HtmlQuicktimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HtmlQuicktime
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
        $this->helper = new HtmlQuicktime();
        $this->helper->setView($this->view);
    }

    public function tearDown()
    {
        unset($this->helper);
    }

    public function testMakeHtmlQuicktime()
    {
        $htmlQuicktime = $this->helper->__invoke('/path/to/quicktime.mov');

        $objectStartElement = '<object data="&#x2F;path&#x2F;to&#x2F;quicktime.mov"'
                            . ' type="video&#x2F;quicktime"'
                            . ' classid="clsid&#x3A;02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"'
                            . ' codebase="http&#x3A;&#x2F;&#x2F;www.apple.com&#x2F;qtactivex&#x2F;qtplugin.cab">';

        $this->assertContains($objectStartElement, $htmlQuicktime);
        $this->assertContains('</object>', $htmlQuicktime);
    }
}
