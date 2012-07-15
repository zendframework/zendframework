<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\Writer\Renderer\Feed;

use DOMDocument;
use Zend\Feed\Writer;
use Zend\Feed\Writer\Renderer;

/**
* @category Zend
* @package Zend_Feed_Writer
*/
class Atom extends AbstractAtom implements Renderer\RendererInterface
{
    /**
     * Constructor
     *
     * @param  Writer\Feed $container
     * @return void
     */
    public function __construct (Writer\Feed $container)
    {
        parent::__construct($container);
    }

    /**
     * Render Atom feed
     *
     * @return Atom
     */
    public function render()
    {
        if (!$this->_container->getEncoding()) {
            $this->_container->setEncoding('UTF-8');
        }
        $this->_dom = new DOMDocument('1.0', $this->_container->getEncoding());
        $this->_dom->formatOutput = true;
        $root = $this->_dom->createElementNS(
            Writer\Writer::NAMESPACE_ATOM_10, 'feed'
        );
        $this->setRootElement($root);
        $this->_dom->appendChild($root);
        $this->_setLanguage($this->_dom, $root);
        $this->_setBaseUrl($this->_dom, $root);
        $this->_setTitle($this->_dom, $root);
        $this->_setDescription($this->_dom, $root);
        $this->_setImage($this->_dom, $root);
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
            $ext->setDOMDocument($this->getDOMDocument(), $root);
            $ext->render();
        }

        foreach ($this->_container as $entry) {
            if ($this->getDataContainer()->getEncoding()) {
                $entry->setEncoding($this->getDataContainer()->getEncoding());
            }
            if ($entry instanceof Writer\Entry) {
                $renderer = new Renderer\Entry\Atom($entry);
            } else {
                if (!$this->_dom->documentElement->hasAttribute('xmlns:at')) {
                    $this->_dom->documentElement->setAttribute(
                        'xmlns:at', 'http://purl.org/atompub/tombstones/1.0'
                    );
                }
                $renderer = new Renderer\Entry\AtomDeleted($entry);
            }
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

}
