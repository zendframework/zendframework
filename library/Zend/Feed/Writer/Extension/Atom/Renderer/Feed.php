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
class Zend_Feed_Writer_Extension_Atom_Renderer_Feed
extends Zend_Feed_Writer_Extension_RendererAbstract
{

    public function render()
    {
        /**
         * RSS 2.0 only. Used mainly to include Atom links and
         * Pubsubhubbub Hub endpoint URIs under the Atom namespace
         */
        if (strtolower($this->getType()) == 'atom') {
            return;
        }
        $this->_appendNamespaces();
        $this->_setFeedLinks($this->_dom, $this->_base);
        $this->_setHubs($this->_dom, $this->_base);
    }
    
    protected function _appendNamespaces()
    {
        $this->getRootElement()->setAttribute('xmlns:atom',
            'http://www.w3.org/2005/Atom');  
    }

    protected function _setFeedLinks(DOMDocument $dom, DOMElement $root)
    {
        $flinks = $this->getDataContainer()->getFeedLinks();
        if(!$flinks || empty($flinks)) {
            return;
        }
        foreach ($flinks as $type => $href) {
            $mime = 'application/' . strtolower($type) . '+xml';
            $flink = $dom->createElement('atom:link');
            $root->appendChild($flink);
            $flink->setAttribute('rel', 'self');
            $flink->setAttribute('type', $mime);
            $flink->setAttribute('href', $href);
        }
    }
    
    protected function _setHubs(DOMDocument $dom, DOMElement $root)
    {
        $hubs = $this->getDataContainer()->getHubs();
        if (!$hubs || empty($hubs)) {
            return;
        }
        foreach ($hubs as $hubUrl) {
            $hub = $dom->createElement('atom:link');
            $hub->setAttribute('rel', 'hub');
            $hub->setAttribute('href', $hubUrl);
            $root->appendChild($hub);
        }
    }

}
