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
 * Represents a Technorati Tag query result set.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 */
class DailyCountsResultSet extends AbstractResultSet
{
    /**
     * Technorati search URL for given query.
     *
     * @var     \Zend\Uri\Http
     * @access  protected
     */
    protected $searchUrl;

    /**
     * Number of days for which counts provided.
     *
     * @var     Weblog
     * @access  protected
     */
    protected $days;

    /**
     * Parses the search response and retrieve the results for iteration.
     *
     * @param   DomDocument $dom    the ReST fragment for this object
     * @param   array $options      query options as associative array
     */
    public function __construct(DomDocument $dom, $options = array())
    {
        parent::__construct($dom, $options);

        $result = $this->xpath->query('/tapi/document/result/days/text()');
        if ($result->length == 1) $this->days = (int) $result->item(0)->data;

        $result = $this->xpath->query('/tapi/document/result/searchurl/text()');
        if ($result->length == 1) {
            $this->searchUrl = Utils::normalizeUriHttp($result->item(0)->data);
        }

        $this->totalResultsReturned  = (int) $this->xpath->evaluate("count(/tapi/document/items/item)");
        $this->totalResultsAvailable = (int) $this->getDays();
    }


    /**
     * Returns the search URL for given query.
     *
     * @return  \Zend\Uri\Http
     */
    public function getSearchUrl()
    {
        return $this->searchUrl;
    }

    /**
     * Returns the number of days for which counts provided.
     *
     * @return  int
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Implements AbstractResultSet::current().
     *
     * @return DailyCountsResult current result
     */
    public function current()
    {
        return new DailyCountsResult($this->results->item($this->currentIndex));
    }
}
