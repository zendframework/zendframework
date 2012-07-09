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
 * Represents a Technorati Search query result set.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 */
class SearchResultSet extends AbstractResultSet
{
    /**
     * Number of query results.
     *
     * @var     int
     * @access  protected
     */
    protected $queryCount;

    /**
     * Parses the search response and retrieve the results for iteration.
     *
     * @param   DomDocument $dom    the ReST fragment for this object
     * @param   array $options      query options as associative array
     */
    public function __construct(DomDocument $dom, $options = array())
    {
        parent::__construct($dom, $options);

        $result = $this->xpath->query('/tapi/document/result/querycount/text()');
        if ($result->length == 1) {
            $this->queryCount = (int) $result->item(0)->data;
        }

        $this->totalResultsReturned  = (int) $this->xpath->evaluate("count(/tapi/document/item)");
        $this->totalResultsAvailable = (int) $this->queryCount;
    }

    /**
     * Implements AbstractResultSet::current().
     *
     * @return SearchResult current result
     */
    public function current()
    {
        return new SearchResult($this->results->item($this->currentIndex));
    }
}
