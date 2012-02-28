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

use \DomElement,
    Zend\Uri;

/**
 * Represents a single Technorati Cosmos query result object.
 * It is never returned as a standalone object,
 * but it always belongs to a valid CosmosResultSet object.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class CosmosResult extends Result
{
    /**
     * Technorati weblog object that links queried URL.
     *
     * @var     Weblog
     * @access  protected
     */
    protected $_weblog;

    /**
     * The nearest permalink tracked for queried URL.
     *
     * @var     Uri\Http
     * @access  protected
     */
    protected $_nearestPermalink;

    /**
     * The excerpt of the blog/page linking queried URL.
     *
     * @var     string
     * @access  protected
     */
    protected $_excerpt;

    /**
     * The the datetime the link was created.
     *
     * @var     ZendDate
     * @access  protected
     */
    protected $_linkCreated;

    /**
     * The URL of the specific link target page
     *
     * @var     Uri\Http
     * @access  protected
     */
    protected $_linkUrl;


    /**
     * Constructs a new object object from DOM Element.
     *
     * @param   DomElement $dom the ReST fragment for this object
     */
    public function __construct(DomElement $dom)
    {
        $this->_fields = array( '_nearestPermalink' => 'nearestpermalink',
                                '_excerpt'          => 'excerpt',
                                '_linkCreated'      => 'linkcreated',
                                '_linkUrl'          => 'linkurl');
        parent::__construct($dom);

        // weblog object field
        $this->_parseWeblog();

        // filter fields
        $this->_nearestPermalink = Utils::normalizeUriHttp($this->_nearestPermalink);
        $this->_linkUrl          = Utils::normalizeUriHttp($this->_linkUrl);
        $this->_linkCreated      = Utils::normalizeDate($this->_linkCreated);
    }

    /**
     * Returns the weblog object that links queried URL.
     *
     * @return  Weblog
     */
    public function getWeblog() {
        return $this->_weblog;
    }

    /**
     * Returns the nearest permalink tracked for queried URL.
     *
     * @return  Uri\Http
     */
    public function getNearestPermalink() {
        return $this->_nearestPermalink;
    }

    /**
     * Returns the excerpt of the blog/page linking queried URL.
     *
     * @return  string
     */
    public function getExcerpt() {
        return $this->_excerpt;
    }

    /**
     * Returns the datetime the link was created.
     *
     * @return  ZendDate
     */
    public function getLinkCreated() {
        return $this->_linkCreated;
    }

    /**
     * If queried URL is a valid blog,
     * returns the URL of the specific link target page.
     *
     * @return  Uri\Http
     */
    public function getLinkUrl() {
        return $this->_linkUrl;
    }

}
