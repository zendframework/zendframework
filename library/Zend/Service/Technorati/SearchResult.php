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

use DomElement;
use Zend\Uri;

/**
 * Represents a single Technorati Search query result object.
 * It is never returned as a standalone object,
 * but it always belongs to a valid SearchResultSet object.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 */
class SearchResult extends AbstractResult
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
     * @var     \DateTime
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
     * @return  \DateTime
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
