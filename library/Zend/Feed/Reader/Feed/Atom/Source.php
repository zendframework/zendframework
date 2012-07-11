<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\Reader\Feed\Atom;

use DOMElement;
use DOMXPath;
use Zend\Feed\Reader;
use Zend\Feed\Reader\Feed;

/**
* @category Zend
* @package Reader
*/
class Source extends Feed\Atom
{

    /**
     * Constructor: Create a Source object which is largely just a normal
     * Zend\Feed\Reader\AbstractFeed object only designed to retrieve feed level
     * metadata from an Atom entry's source element.
     *
     * @param DOMElement $source
     * @param string $xpathPrefix Passed from parent Entry object
     * @param string $type Nearly always Atom 1.0
     */
    public function __construct(DOMElement $source, $xpathPrefix, $type = Reader\Reader::TYPE_ATOM_10)
    {
        $this->_domDocument = $source->ownerDocument;
        $this->_xpath = new DOMXPath($this->_domDocument);
        $this->_data['type'] = $type;
        $this->_registerNamespaces();
        $this->_loadExtensions();
        
        $atomClass = Reader\Reader::getPluginLoader()->getClassName('Atom\\Feed');
        $this->_extensions['Atom\\Feed'] = new $atomClass($this->_domDocument, $this->_data['type'], $this->_xpath);
        $atomClass = Reader\Reader::getPluginLoader()->getClassName('DublinCore\\Feed');
        $this->_extensions['DublinCore\\Feed'] = new $atomClass($this->_domDocument, $this->_data['type'], $this->_xpath);
        foreach ($this->_extensions as $extension) {
            $extension->setXpathPrefix(rtrim($xpathPrefix, '/') . '/atom:source');
        }
    }
    
    /**
     * Since this is not an Entry carrier but a vehicle for Feed metadata, any
     * applicable Entry methods are stubbed out and do nothing.
     */
     
    /**
     * @return void
     */
    public function count() {}

    /**
     * @return void
     */
    public function current() {}
    
    /**
     * @return void
     */
    public function key() {}

    /**
     * @return void
     */
    public function next() {}

    /**
     * @return void
     */
    public function rewind() {}
    
    /**
     * @return void
     */
    public function valid() {}
    
    /**
     * @return void
     */
    protected function _indexEntries() {}

}
