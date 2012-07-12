<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\App\Extension;

/**
 * Class that represents elements which were not handled by other parsing
 * code in the library.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage App
 */
class Element extends AbstractExtension
{

    public function __construct($rootElement=null, $rootNamespace=null, $rootNamespaceURI=null, $text=null)
    {
        parent::__construct();
        $this->_rootElement = $rootElement;
        $this->_rootNamespace = $rootNamespace;
        $this->_rootNamespaceURI = $rootNamespaceURI;
        $this->_text = $text;
    }

    public function transferFromDOM($node)
    {
        parent::transferFromDOM($node);
        $this->_rootNamespace = null;
        $this->_rootNamespaceURI = $node->namespaceURI;
        $this->_rootElement = $node->localName;
    }

}
