<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Docs;

use Zend\GData\Docs;

/**
 * Represents a Documents List entry in the Documents List data API meta feed
 * of a user's documents.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Docs
 */
class DocumentListEntry extends \Zend\GData\Entry
{

    /**
     * Create a new instance of an entry representing a document.
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Docs::$namespaces);
        parent::__construct($element);
    }

}
