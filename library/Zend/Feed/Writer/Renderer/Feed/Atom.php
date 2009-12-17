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

require_once 'Zend/Feed/Writer/Feed.php';

require_once 'Zend/Version.php';

require_once 'Zend/Feed/Writer/Renderer/RendererInterface.php';

require_once 'Zend/Feed/Writer/Renderer/Entry/Atom.php';

require_once 'Zend/Feed/Writer/Renderer/RendererAbstract.php';

/**
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Writer_Renderer_Feed_Atom
extends Zend_Feed_Writer_Renderer_RendererAbstract
implements Zend_Feed_Writer_Renderer_RendererInterface
{

    public function __construct (Zend_Feed_Writer_Feed $container)
    {
        parent::__construct($container);
    }

    public function render()
    {
        if (!$this->_container->getEncoding()) {
            $this->_container->setEncoding('UTF-8');
        }
        $this->_dom = new DOMDocument('1.0', $this->_container->getEncoding());
        $this->_dom->formatOutput = true;
        $root = $this->_dom->createElementNS(
            Zend_Feed_Writer::NAMESPACE_ATOM_10, 'feed'
        );
        $this->setRootElement($root);
        $this->_dom->appendChild($root);
        $this->_setLanguage($this->_dom, $root);
        $this->_setBaseUrl($this->_dom, $root);
        $this->_setTitle($this->_dom, $root);
        $this->_setDescription($this->_dom, $root);
        $this->_setDateCreated($this->_dom, $root);
        $this->_setDateModified($this->_dom, $root);
        $this->_setGenerator($this->_dom, $root);
        $this->_setLink($this->_dom, $root);
        $this->_setFeedLinks($this->_dom, $root);
        $this->_setId($this->_dom, $root);
        $this->_setAuthors($this->_dom, $root);
        $this->_setCopyright($this->_dom, $root);
        $this->_setCategories($this->_dom, $root);
        $this->_setHubs($this->_dom, $root);
        
        foreach ($this->_extensions as $ext) {
            $ext->setType($this->getType());
            $ext->setRootElement($this->getRootElement());
            $ext->setDomDocument($this->getDomDocument(), $root);
            $ext->render();
        }
        
        foreach ($this->_container as $entry) {
            if ($this->getDataContainer()->getEncoding()) {
                $entry->setEncoding($this->getDataContainer()->getEncoding());
            }
            $renderer = new Zend_Feed_Writer_Renderer_Entry_Atom($entry);
            if ($this->_ignoreExceptions === true) {
                $renderer->ignoreExceptions();
            }
            $renderer->setType($this->getType());
            $renderer->setRootElement($this->_dom->documentElement);
            $renderer->render();
            $element = $renderer->getElement();
            $imported = $this->_dom->importNode($element, true);
            $root->appendChild($imported);
        }
        return $this;
    }

    protected function _setLanguage(DOMDocument $dom, DOMElement $root)
    {
        if ($this->getDataContainer()->getLanguage()) {
            $root->setAttribute('xml:lang', $this->getDataContainer()
                ->getLanguage());
        }
    }

    protected function _setTitle(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getTitle()) {
            require_once 'Zend/Feed/Exception.php';
            $message = 'Atom 1.0 feed elements MUST contain exactly one'
            . ' atom:title element but a title has not been set';
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
        $title->setAttribute('type', 'text');
        $title->nodeValue = $this->getDataContainer()->getTitle();
    }

    protected function _setDescription(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getDescription()) {
            return;
        }
        $subtitle = $dom->createElement('subtitle');
        $root->appendChild($subtitle);
        $subtitle->setAttribute('type', 'text');
        $subtitle->nodeValue = $this->getDataContainer()->getDescription();
    }

    protected function _setDateModified(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getDateModified()) {
            require_once 'Zend/Feed/Exception.php';
            $message = 'Atom 1.0 feed elements MUST contain exactly one'
            . ' atom:updated element but a modification date has not been set';
            $exception = new Zend_Feed_Exception($message);
            if (!$this->_ignoreExceptions) {
                throw $exception;
            } else {
                $this->_exceptions[] = $exception;
                return;
            }
        }

        $updated = $dom->createElement('updated');
        $root->appendChild($updated);
        $updated->nodeValue = $this->getDataContainer()->getDateModified()
            ->get(Zend_Date::ISO_8601);
    }

    protected function _setGenerator(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getGenerator()) {
            $this->getDataContainer()->setGenerator('Zend_Feed_Writer',
                Zend_Version::VERSION, 'http://framework.zend.com');
        }

        $gdata = $this->getDataContainer()->getGenerator();
        $generator = $dom->createElement('generator');
        $root->appendChild($generator);
        $generator->nodeValue = $gdata['name'];
        if (array_key_exists('uri', $gdata)) {
            $generator->setAttribute('uri', $gdata['uri']);
        }
        if (array_key_exists('version', $gdata)) {
            $generator->setAttribute('version', $gdata['version']);
        }
    }

    protected function _setLink(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getLink()) {
            return;
        }
        $link = $dom->createElement('link');
        $root->appendChild($link);
        $link->setAttribute('rel', 'alternate');
        $link->setAttribute('type', 'text/html');
        $link->setAttribute('href', $this->getDataContainer()->getLink());
    }

    protected function _setFeedLinks(DOMDocument $dom, DOMElement $root)
    {
        $flinks = $this->getDataContainer()->getFeedLinks();
        if(!$flinks || !array_key_exists('atom', $flinks)) {
            require_once 'Zend/Feed/Exception.php';
            $message = 'Atom 1.0 feed elements SHOULD contain one atom:link '
            . 'element with a rel attribute value of "self".  This is the '
            . 'preferred URI for retrieving Atom Feed Documents representing '
            . 'this Atom feed but a feed link has not been set';
            $exception = new Zend_Feed_Exception($message);
            if (!$this->_ignoreExceptions) {
                throw $exception;
            } else {
                $this->_exceptions[] = $exception;
                return;
            }
        }

        foreach ($flinks as $type => $href) {
            $mime = 'application/' . strtolower($type) . '+xml';
            $flink = $dom->createElement('link');
            $root->appendChild($flink);
            $flink->setAttribute('rel', 'self');
            $flink->setAttribute('type', $mime);
            $flink->setAttribute('href', $href);
        }
    }
    
    protected function _setAuthors(DOMDocument $dom, DOMElement $root)
    {
        $authors = $this->_container->getAuthors();
        if (!$authors || empty($authors)) {
            /**
             * Technically we should defer an exception until we can check
             * that all entries contain an author. If any entry is missing
             * an author, then a missing feed author element is invalid
             */
            return;
        }
        foreach ($authors as $data) {
            $author = $this->_dom->createElement('author');
            $name = $this->_dom->createElement('name');
            $author->appendChild($name);
            $root->appendChild($author);
            $name->nodeValue = $data['name'];
            if (array_key_exists('email', $data)) {
                $email = $this->_dom->createElement('email');
                $author->appendChild($email);
                $email->nodeValue = $data['email'];
            }
            if (array_key_exists('uri', $data)) {
                $uri = $this->_dom->createElement('uri');
                $author->appendChild($uri);
                $uri->nodeValue = $data['uri'];
            }
        }
    }

    protected function _setId(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getId()
        && !$this->getDataContainer()->getLink()) {
            require_once 'Zend/Feed/Exception.php';
            $message = 'Atom 1.0 feed elements MUST contain exactly one '
            . 'atom:id element, or as an alternative, we can use the same '
            . 'value as atom:link however neither a suitable link nor an '
            . 'id have been set';
            $exception = new Zend_Feed_Exception($message);
            if (!$this->_ignoreExceptions) {
                throw $exception;
            } else {
                $this->_exceptions[] = $exception;
                return;
            }
        }

        if (!$this->getDataContainer()->getId()) {
            $this->getDataContainer()->setId(
                $this->getDataContainer()->getLink());
        }
        $id = $dom->createElement('id');
        $root->appendChild($id);
        $id->nodeValue = $this->getDataContainer()->getId();
    }
    
    protected function _setCopyright(DOMDocument $dom, DOMElement $root)
    {
        $copyright = $this->getDataContainer()->getCopyright();
        if (!$copyright) {
            return;
        }
        $copy = $dom->createElement('rights');
        $root->appendChild($copy);
        $copy->nodeValue = $copyright;
    }
    
    protected function _setDateCreated(DOMDocument $dom, DOMElement $root)
    {
        if(!$this->getDataContainer()->getDateCreated()) {
            return;
        }
        if(!$this->getDataContainer()->getDateModified()) {
            $this->getDataContainer()->setDateModified(
                $this->getDataContainer()->getDateCreated()
            );
        }
    }
    
    protected function _setBaseUrl(DOMDocument $dom, DOMElement $root)
    {
        $baseUrl = $this->getDataContainer()->getBaseUrl();
        if (!$baseUrl) {
            return;
        }
        $root->setAttribute('xml:base', $baseUrl);
    }
    
    protected function _setHubs(DOMDocument $dom, DOMElement $root)
    {
        $hubs = $this->getDataContainer()->getHubs();
        if (!$hubs) {
            return;
        }
        foreach ($hubs as $hubUrl) {
            $hub = $dom->createElement('link');
            $hub->setAttribute('rel', 'hub');
            $hub->setAttribute('href', $hubUrl);
            $root->appendChild($hub);
        }
    }
    
    protected function _setCategories(DOMDocument $dom, DOMElement $root)
    {
        $categories = $this->getDataContainer()->getCategories();
        if (!$categories) {
            return;
        }
        foreach ($categories as $cat) {
            $category = $dom->createElement('category');
            $category->setAttribute('term', $cat['term']);
            if (isset($cat['label'])) {
                $category->setAttribute('label',
                    htmlentities($cat['label'], ENT_QUOTES, $this->getDataContainer()->getEncoding())
                );
            } else {
                $category->setAttribute('label',
                    htmlentities($cat['term'], ENT_QUOTES, $this->getDataContainer()->getEncoding())
                );
            }
            if (isset($cat['scheme'])) {
                $category->setAttribute('scheme', $cat['scheme']);
            }
            $root->appendChild($category);
        }
    }

}
