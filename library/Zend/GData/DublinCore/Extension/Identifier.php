<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\DublinCore\Extension;

/**
 * An unambiguous reference to the resource within a given context
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage DublinCore
 */
class Identifier extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'dc';
    protected $_rootElement = 'identifier';

    /**
     * Constructor for Zend_Gdata_DublinCore_Extension_Identifier which
     * An unambiguous reference to the resource within a given context
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($value = null)
    {
        $this->registerAllNamespaces(\Zend\GData\DublinCore::$namespaces);
        parent::__construct();
        $this->_text = $value;
    }

}
