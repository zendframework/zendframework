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

/**
 * @namespace
 */
namespace Zend\Service\Technorati;

/**
 * Represents a Technorati Cosmos query result set.
 *
 * @uses       \Zend\Service\Technorati\CosmosResult
 * @uses       \Zend\Service\Technorati\ResultSet
 * @uses       \Zend\Service\Technorati\Utils
 * @uses       \Zend\Service\Technorati\Weblog
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CosmosResultSet extends ResultSet
{
    /**
     * Technorati weblog url, if queried URL is a valid weblog.
     *
     * @var     \Zend\Uri\Http
     * @access  protected
     */
    protected $_url;

    /**
     * Technorati weblog, if queried URL is a valid weblog.
     *
     * @var     \Zend\Service\Technorati\Weblog
     * @access  protected
     */
    protected $_weblog;

    /**
     * Number of unique blogs linking this blog
     *
     * @var     integer
     * @access  protected
     */
    protected $_inboundBlogs;

    /**
     * Number of incoming links to this blog
     *
     * @var     integer
     * @access  protected
     */
    protected $_inboundLinks;

    /**
     * Parses the search response and retrieve the results for iteration.
     *
     * @param   DomDocument $dom    the ReST fragment for this object
     * @param   array $options      query options as associative array
     */
    public function __construct(\DomDocument $dom, $options = array())
    {
        parent::__construct($dom, $options);

        $result = $this->_xpath->query('/tapi/document/result/inboundlinks/text()');
        if ($result->length == 1) $this->_inboundLinks = (int) $result->item(0)->data;

        $result = $this->_xpath->query('/tapi/document/result/inboundblogs/text()');
        if ($result->length == 1) $this->_inboundBlogs = (int) $result->item(0)->data;

        $result = $this->_xpath->query('/tapi/document/result/weblog');
        if ($result->length == 1) {
            $this->_weblog = new Weblog($result->item(0));
        }

        $result = $this->_xpath->query('/tapi/document/result/url/text()');
        if ($result->length == 1) {
            try {
                // fetched URL often doens't include schema
                // and this issue causes the following line to fail
                $this->_url = Utils::normalizeUriHttp($result->item(0)->data);
            } catch(Exception $e) {
                if ($this->getWeblog() instanceof Weblog) {
                    $this->_url = $this->getWeblog()->getUrl();
                }
            }
        }

        $this->_totalResultsReturned  = (int) $this->_xpath->evaluate("count(/tapi/document/item)");

        // total number of results depends on query type
        // for now check only getInboundLinks() and getInboundBlogs() value
        if ((int) $this->getInboundLinks() > 0) {
            $this->_totalResultsAvailable = $this->getInboundLinks();
        } elseif ((int) $this->getInboundBlogs() > 0) {
            $this->_totalResultsAvailable = $this->getInboundBlogs();
        } else {
            $this->_totalResultsAvailable = 0;
        }
    }


    /**
     * Returns the weblog URL.
     *
     * @return  \Zend\Uri\Http
     */
    public function getUrl() {
        return $this->_url;
    }

    /**
     * Returns the weblog.
     *
     * @return  \Zend\Service\Technorati\Weblog
     */
    public function getWeblog() {
        return $this->_weblog;
    }

    /**
     * Returns number of unique blogs linking this blog.
     *
     * @return  integer the number of inbound blogs
     */
    public function getInboundBlogs()
    {
        return $this->_inboundBlogs;
    }

    /**
     * Returns number of incoming links to this blog.
     *
     * @return  integer the number of inbound links
     */
    public function getInboundLinks()
    {
        return $this->_inboundLinks;
    }

    /**
     * Implements Zend_Service_Technorati_ResultSet::current().
     *
     * @return \Zend\Service\Technorati\CosmosResult current result
     */
    public function current()
    {
        return new CosmosResult($this->_results->item($this->_currentIndex));
    }
}
