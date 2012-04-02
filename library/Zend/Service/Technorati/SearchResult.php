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

use DomElement,
    Zend\Uri;

/**
 * Represents a single Technorati Search query result object.
 * It is never returned as a standalone object,
 * but it always belongs to a valid SearchResultSet object.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SearchResult extends Result
{
    /**
     * Technorati weblog object corresponding to queried keyword.
     *
     * @var     Weblog
     * @access  protected
     */
    protected $weblog;

    /**
     * The title of the entry.
     *
     * @var     string
     * @access  protected
     */
    protected $title;

    /**
     * The blurb from entry with search term highlighted.
     *
     * @var     string
     * @access  protected
     */
    protected $excerpt;

    /**
     * The datetime the entry was created.
     *
     * @var     ZendDate
     * @access  protected
     */
    protected $created;

    /**
     * The permalink of the blog entry.
     *
     * @var     Uri\Http
     * @access  protected
     */
    protected $permalink;


    /**
     * Constructs a new object object from DOM Element.
     *
     * @param   DomElement $dom the ReST fragment for this object
     */
    public function __construct(DomElement $dom)
    {
        $this->fields = array( 'permalink'    => 'permalink',
                               'excerpt'      => 'excerpt',
                               'created'      => 'created',
                               'title'        => 'title');
        parent::__construct($dom);

        // weblog object field
        $this->parseWeblog();

        // filter fields
        $this->permalink = Utils::normalizeUriHttp($this->permalink);
        $this->created   = Utils::normalizeDate($this->created);
    }

    /**
     * Returns the weblog object that links queried URL.
     *
     * @return  Weblog
     */
    public function getWeblog()
    {
        return $this->weblog;
    }

    /**
     * Returns the title of the entry.
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns the blurb from entry with search term highlighted.
     *
     * @return  string
     */
    public function getExcerpt()
    {
        return $this->excerpt;
    }

    /**
     * Returns the datetime the entry was created.
     *
     * @return  ZendDate
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Returns the permalink of the blog entry.
     *
     * @return  Uri\Http
     */
    public function getPermalink()
    {
        return $this->permalink;
    }

}
