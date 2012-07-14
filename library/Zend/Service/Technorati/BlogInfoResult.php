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
use DOMXPath;

/**
 * Represents a single Technorati BlogInfo query result object.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 */
class BlogInfoResult
{
    /**
     * Technorati weblog url, if queried URL is a valid weblog.
     *
     * @var     \Zend\Uri\Http
     * @access  protected
     */
    protected $url;

    /**
     * Technorati weblog, if queried URL is a valid weblog.
     *
     * @var     Weblog
     * @access  protected
     */
    protected $weblog;

    /**
     * Number of unique blogs linking this blog
     *
     * @var     integer
     * @access  protected
     */
    protected $inboundBlogs;

    /**
     * Number of incoming links to this blog
     *
     * @var     integer
     * @access  protected
     */
    protected $inboundLinks;


    /**
     * Constructs a new object object from DOM Document.
     *
     * @param   DomDocument $dom the ReST fragment for this object
     */
    public function __construct(DomDocument $dom)
    {
        $xpath = new DOMXPath($dom);
        $result = $xpath->query('//result/weblog');
        if ($result->length == 1) {
            $this->weblog = new Weblog($result->item(0));
        } else {
            // follow the same behavior of blogPostTags
            // and raise an Exception if the URL is not a valid weblog
            throw new Exception\RuntimeException(
                "Your URL is not a recognized Technorati weblog");
        }

        $result = $xpath->query('//result/url/text()');
        if ($result->length == 1) {
            try {
                // fetched URL often doens't include schema
                // and this issue causes the following line to fail
                $this->url = Utils::normalizeUriHttp($result->item(0)->data);
            } catch (Exception $e) {
                if ($this->getWeblog() instanceof Weblog) {
                    $this->url = $this->getWeblog()->getUrl();
                }
            }
        }

        $result = $xpath->query('//result/inboundblogs/text()');
        if ($result->length == 1) $this->inboundBlogs = (int) $result->item(0)->data;

        $result = $xpath->query('//result/inboundlinks/text()');
        if ($result->length == 1) $this->inboundLinks = (int) $result->item(0)->data;

    }


    /**
     * Returns the weblog URL.
     *
     * @return  \Zend\Uri\Http
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns the weblog.
     *
     * @return  Weblog
     */
    public function getWeblog()
    {
        return $this->weblog;
    }

    /**
     * Returns number of unique blogs linking this blog.
     *
     * @return  integer the number of inbound blogs
     */
    public function getInboundBlogs()
    {
        return (int) $this->inboundBlogs;
    }

    /**
     * Returns number of incoming links to this blog.
     *
     * @return  integer the number of inbound links
     */
    public function getInboundLinks()
    {
        return (int) $this->inboundLinks;
    }

}
