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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Feed_Writer_Renderer_RendererAbstract
 */
require_once 'Zend/Feed/Writer/Renderer/RendererAbstract.php';

/**
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Writer_Renderer_Entry_Rss
extends Zend_Feed_Writer_Renderer_RendererAbstract
implements Zend_Feed_Writer_Renderer_RendererInterface
{

    public function __construct (Zend_Feed_Writer_Entry $container)
    {
        parent::__construct($container);
    }
    
    public function render()
    {
        $this->_dom = new DOMDocument('1.0', $this->_container->getEncoding());
        $this->_dom->formatOutput = true;
        $entry = $this->_dom->createElement('item');
        $this->_dom->appendChild($entry);
        
        $this->_setTitle($this->_dom, $entry);
        $this->_setDescription($this->_dom, $entry);
        $this->_setDateCreated($this->_dom, $entry);
        $this->_setDateModified($this->_dom, $entry);
        $this->_setLink($this->_dom, $entry);
        $this->_setId($this->_dom, $entry);
        $this->_setAuthors($this->_dom, $entry);
        $this->_setEnclosure($this->_dom, $entry);
        $this->_setCommentLink($this->_dom, $entry);
        foreach ($this->_extensions as $ext) {
            $ext->setType($this->getType());
            $ext->setRootElement($this->getRootElement());
            $ext->setDomDocument($this->getDomDocument(), $entry);
            $ext->render();
        }

        return $this;
    }
    
    protected function _setTitle(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getDescription()
        && !$this->getDataContainer()->getTitle()) {
            require_once 'Zend/Feed/Exception.php';
            $message = 'RSS 2.0 entry elements SHOULD contain exactly one'
            . ' title element but a title has not been set. In addition, there'
            . ' is no description as required in the absence of a title.';
            $exception = new Zend_Feed_Exception($message);
            if (!$this->_ignoreExceptions) {
                throw $exception;
            } else {
                $this->_exceptions[] = $exception;
                return;
            }
        }
        $title = $dom->createElement('title');
        $root->appendChild($title);
        $title->nodeValue = htmlentities(
            $this->getDataContainer()->getTitle(),
            ENT_QUOTES,
            $this->getDataContainer()->getEncoding()
        );
    }
    
    protected function _setDescription(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getDescription()
        && !$this->getDataContainer()->getTitle()) {
            require_once 'Zend/Feed/Exception.php';
            $message = 'RSS 2.0 entry elements SHOULD contain exactly one'
            . ' description element but a description has not been set. In'
            . ' addition, there is no title element as required in the absence'
            . ' of a description.';
            $exception = new Zend_Feed_Exception($message);
            if (!$this->_ignoreExceptions) {
                throw $exception;
            } else {
                $this->_exceptions[] = $exception;
                return;
            }
        }
        $subtitle = $dom->createElement('description');
        $root->appendChild($subtitle);
        $subtitle->nodeValue = htmlentities(
            $this->getDataContainer()->getDescription(),
            ENT_QUOTES,
            $this->getDataContainer()->getEncoding()
        );
    }
    
    protected function _setDateModified(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getDateModified()) {
            return;
        }

        $updated = $dom->createElement('pubDate');
        $root->appendChild($updated);
        $updated->nodeValue = $this->getDataContainer()->getDateModified()
            ->get(Zend_Date::RSS);
    }
    
    protected function _setDateCreated(DOMDocument $dom, DOMElement $root)
    {
        if (!$this->getDataContainer()->getDateCreated()) {
            return;
        }
        if (!$this->getDataContainer()->getDateModified()) {
            $this->getDataContainer()->setDateModified(
                $this->getDataContainer()->getDateCreated()
            );
        }
    }
    
    protected function _setAuthors(DOMDocument $dom, DOMElement $root)
    {
        $authors = $this->_container->getAuthors();
        if ((!$authors || empty($authors))) {
            return;
        }
        foreach ($authors as $data) {
            $author = $this->_dom->createElement('author');
            $name = $data['name'];
            if (array_key_exists('email', $data)) {
                $name = $data['email'] . ' (' . $data['name'] . ')';
            }
            $author->nodeValue = $name;
            $root->appendChild($author);
        }
    }
    
    protected function _setEnclosure(DOMDocument $dom, DOMElement $root)
    {
        $data = $this->_container->getEnclosure();
        if ((!$data || empty($data))) {
            return;
        }
        $enclosure = $this->_dom->createElement('enclosure');
        $enclosure->setAttribute('type', $data['type']);
        $enclosure->setAttribute('length', $data['length']);
        $enclosure->setAttribute('url', $data['uri']);
        $root->appendChild($enclosure);
    }
    
    protected function _setLink(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getLink()) {
            return;
        }
        $link = $dom->createElement('link');
        $root->appendChild($link);
        $link->nodeValue = $this->getDataContainer()->getLink();
    }
    
    protected function _setId(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getId()
        && !$this->getDataContainer()->getLink()) {
            return;
        }

        $id = $dom->createElement('guid');
        $root->appendChild($id);
        if (!$this->getDataContainer()->getId()) {
            $this->getDataContainer()->setId(
                $this->getDataContainer()->getLink());
        }
        $id->nodeValue = $this->getDataContainer()->getId();
        if (!Zend_Uri::check($this->getDataContainer()->getId())) {
            $id->setAttribute('isPermaLink', 'false');
        }
    }
    
    protected function _setCommentLink(DOMDocument $dom, DOMElement $root)
    {
        $link = $this->getDataContainer()->getCommentLink();
        if (!$link) {
            return;
        }
        $clink = $this->_dom->createElement('comments');
        $clink->nodeValue = $link;
        $root->appendChild($clink);
    }

}
