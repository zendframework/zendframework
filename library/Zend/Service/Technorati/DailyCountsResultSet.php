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
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Technorati;

use DomDocument;

/**
 * Represents a Technorati Tag query result set.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
    public function getSearchUrl() {
        return $this->searchUrl;
    }

    /**
     * Returns the number of days for which counts provided.
     *
     * @return  int
     */
    public function getDays() {
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
