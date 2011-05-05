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
 * @package    Reader\Reader
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
* @namespace
*/
namespace Zend\Feed\Reader\Feed\Atom;
use Zend\Feed\Reader;
use Zend\Feed\Reader\Feed;
use Zend\Date;

/**
* @uses \Zend\Feed\Reader\Reader
* @uses \Zend\Feed\Reader\Extension\Atom\Feed
* @uses \Zend\Feed\Reader\Feed\AbstractFeed
* @category Zend
* @package Reader
* @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
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
    public function __construct(\DOMElement $source, $xpathPrefix, $type = Reader\Reader::TYPE_ATOM_10)
    {
        $this->_domDocument = $source->ownerDocument;
        $this->_xpath = new \DOMXPath($this->_domDocument);
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
