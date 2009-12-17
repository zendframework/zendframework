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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
 
/**
 * @see Zend_Feed_Writer_Extension_RendererAbstract
 */
require_once 'Zend/Feed/Writer/Extension/RendererAbstract.php';
 
/**
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Writer_Extension_Content_Renderer_Entry
extends Zend_Feed_Writer_Extension_RendererAbstract
{

    public function render()
    {
        if (strtolower($this->getType()) == 'atom') {
            return;
        }
        $this->_appendNamespaces();
        $this->_setContent($this->_dom, $this->_base);
    }
    
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute('xmlns:content',
            'http://purl.org/rss/1.0/modules/content/');  
    }

    protected function _setContent(DOMDocument $dom, DOMElement $root)
    {
        $content = $this->getDataContainer()->getContent();
        if (!$content) {
            return;
        }
        $element = $dom->createElement('content:encoded');
        $root->appendChild($element);
        $element->nodeValue = htmlentities(
            $this->getDataContainer()->getContent(),
            ENT_QUOTES,
            $this->getDataContainer()->getEncoding()
        );
    }

}
