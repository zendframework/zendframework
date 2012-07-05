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
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
namespace Zend\Feed\Writer\Extension\Content\Renderer;

use Zend\Feed\Writer\Extension;
use DOMDocument;
use DOMElement;

/**
* @category Zend
* @package Zend_Feed_Writer
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
*/
class Entry extends Extension\AbstractRenderer
{

    /**
     * Set to TRUE if a rendering method actually renders something. This
     * is used to prevent premature appending of a XML namespace declaration
     * until an element which requires it is actually appended.
     *
     * @var bool
     */
    protected $_called = false;
    
    /**
     * Render entry
     * 
     * @return void
     */
    public function render()
    {
        if (strtolower($this->getType()) == 'atom') {
            return;
        }
        $this->_setContent($this->_dom, $this->_base);
        if ($this->_called) {
            $this->_appendNamespaces();
        }
    }
    
    /**
     * Append namespaces to root element
     * 
     * @return void
     */
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute('xmlns:content',
            'http://purl.org/rss/1.0/modules/content/');  
    }

    /**
     * Set entry content
     * 
     * @param  DOMDocument $dom
     * @param  DOMElement $root
     * @return void
     */
    protected function _setContent(DOMDocument $dom, DOMElement $root)
    {
        $content = $this->getDataContainer()->getContent();
        if (!$content) {
            return;
        }
        $element = $dom->createElement('content:encoded');
        $root->appendChild($element);
        $cdata = $dom->createCDATASection($content);
        $element->appendChild($cdata);
        $this->_called = true;
    }
}
