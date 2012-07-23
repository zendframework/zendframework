<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Technorati;

use DomDocument;

/**
 * Represents a Technorati TopTags or BlogPostTags queries result set.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 */
class TagsResultSet extends AbstractResultSet
{
    /**
     * Constructs a new object object from DOM Document.
     *
     * @param   DomDocument $dom the ReST fragment for this object
     */
    public function __construct(DomDocument $dom, $options = array())
    {
        parent::__construct($dom, $options);

        $this->totalResultsReturned  = (int) $this->xpath->evaluate("count(/tapi/document/item)");
        $this->totalResultsAvailable = (int) $this->totalResultsReturned;
    }

    /**
     * Implements AbstractResultSet::current().
     *
     * @return TagsResult current result
     */
    public function current()
    {
        return new TagsResult($this->results->item($this->currentIndex));
    }
}
