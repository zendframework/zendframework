<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\YouTube\Extension;

/**
 * Represents the yt:token element used by the YouTube data API
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 */
class Token extends \Zend\GData\App\Extension\AbstractExtension
{

    protected $_rootNamespace = 'yt';
    protected $_rootElement = 'token';

    /**
     * Constructs a new Zend_Gdata_YouTube_Extension_Token object.
     */
    public function __construct($text = null)
    {
        $this->registerAllNamespaces(\Zend\GData\YouTube::$namespaces);
        parent::__construct();
        $this->_text = $text;
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for sending to the server upon updates, or
     * for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     * child properties.
     */
    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        return $element;
    }

}
