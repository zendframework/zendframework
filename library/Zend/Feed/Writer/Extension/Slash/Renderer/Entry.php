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
class Zend_Feed_Writer_Extension_Slash_Renderer_Entry
extends Zend_Feed_Writer_Extension_RendererAbstract
{

    public function render()
    {
        if (strtolower($this->getType()) == 'atom') {
            return; // RSS 2.0 only
        }
        $this->_appendNamespaces();
        $this->_setCommentCount($this->_dom, $this->_base);
    }
    
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute('xmlns:slash',
            'http://purl.org/rss/1.0/modules/slash/');  
    }

    protected function _setCommentCount(DOMDocument $dom, DOMElement $root)
    {
        $count = $this->getDataContainer()->getCommentCount();
        if (!$count) {
            $count = 0;
        }
        $tcount = $this->_dom->createElement('slash:comments');
        $tcount->nodeValue = $count;
        $root->appendChild($tcount);
    }

}
