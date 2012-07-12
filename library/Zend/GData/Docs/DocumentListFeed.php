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
 * Data model for a Google Documents List feed of documents
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Docs
 */
class DocumentListFeed extends \Zend\GData\Feed
{

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend\GData\Docs\DocumentListEntry';

    /**
     * The classname for the feed.
     *
     * @var string
     */
    protected $_feedClassName = 'Zend\GData\Docs\DocumentListFeed';

    /**
     * Create a new instance of a feed for a list of documents.
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
