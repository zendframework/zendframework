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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
 
/**
* @namespace
*/
namespace Zend\Feed\Writer\Extension\WellFormedWeb\Renderer;
use Zend\Feed\Writer\Extension;

/**
* @uses \Zend\Feed\Writer\Extension\RendererAbstract
* @category Zend
* @package Zend_Feed_Writer
* @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
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
            return; // RSS 2.0 only
        }
        $this->_setCommentFeedLinks($this->_dom, $this->_base);
        if ($this->_called) {
            $this->_appendNamespaces();
        }
    }
    
    /**
     * Append entry namespaces
     * 
     * @return void
     */
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute('xmlns:wfw',
            'http://wellformedweb.org/CommentAPI/');  
    }
    
    /**
     * Set entry comment feed links
     * 
     * @param  DOMDocument $dom 
     * @param  DOMElement $root 
     * @return void
     */
    protected function _setCommentFeedLinks(\DOMDocument $dom, \DOMElement $root)
    {
        $links = $this->getDataContainer()->getCommentFeedLinks();
        if (!$links || empty($links)) {
            return;
        }
        foreach ($links as $link) {
            if ($link['type'] == 'rss') {
                $flink = $this->_dom->createElement('wfw:commentRss');
                $text = $dom->createTextNode($link['uri']);
                $flink->appendChild($text);
                $root->appendChild($flink);
            }
        }
        $this->_called = true;
    }
}
