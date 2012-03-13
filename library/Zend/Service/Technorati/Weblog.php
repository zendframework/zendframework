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

use DomElement;

/**
 * Represents a Weblog object successful recognized by Technorati.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Weblog
{
    /**
     * Blog name as written in the feed.
     *
     * @var     string
     * @access  protected
     */
    protected $name;

    /**
     * Base blog URL.
     *
     * @var     \Zend\\Zend\Uri\Http
     * @access  protected
     */
    protected $url;

    /**
     * RSS feed URL, if any.
     *
     * @var     null|\Zend\Uri\Http
     * @access  protected
     */
    protected $rssUrl;

    /**
     * Atom feed URL, if any.
     *
     * @var     null|\Zend\Uri\Http
     * @access  protected
     */
    protected $atomUrl;

    /**
     * Number of unique blogs linking this blog.
     *
     * @var     integer
     * @access  protected
     */
    protected $inboundBlogs;

    /**
     * Number of incoming links to this blog.
     *
     * @var     integer
     * @access  protected
     */
    protected $inboundLinks;

    /**
     * Last blog update UNIX timestamp.
     *
     * @var     null|ZendDate
     * @access  protected
     */
    protected $lastUpdate;

    /**
     * Technorati rank value for this weblog.
     *
     * Note. This property has no official documentation.
     *
     * @var     integer
     * @access  protected
     */
    protected $rank;

    /**
     * Blog latitude coordinate.
     *
     * Note. This property has no official documentation.
     *
     * @var     float
     * @access  protected
     */
    protected $lat;

    /**
     * Blog longitude coordinate.
     *
     * Note. This property has no official documentation.
     *
     * @var     float
     * @access  protected
     */
    protected $lon;

    /**
     * Whether the author who claimed this weblog has a photo.
     *
     * Note. This property has no official documentation.
     *
     * @var     bool
     * @access  protected
     * @see     Author::$thumbnailPicture
     */
    protected $hasPhoto = false;

    /**
     * An array of Author who claimed this blog
     *
     * @var     array
     * @access  protected
     */
    protected $authors = array();


    /**
     * Constructs a new object from DOM Element.
     *
     * @param  DomElement $dom the ReST fragment for this object
     */
    public function __construct(DomElement $dom)
    {
        $xpath = new \DOMXPath($dom->ownerDocument);

        $result = $xpath->query('./name/text()', $dom);
        if ($result->length == 1) $this->setName($result->item(0)->data);

        $result = $xpath->query('./url/text()', $dom);
        if ($result->length == 1) $this->setUrl($result->item(0)->data);

        $result = $xpath->query('./inboundblogs/text()', $dom);
        if ($result->length == 1) $this->setInboundBlogs($result->item(0)->data);

        $result = $xpath->query('./inboundlinks/text()', $dom);
        if ($result->length == 1) $this->setInboundLinks($result->item(0)->data);

        $result = $xpath->query('./lastupdate/text()', $dom);
        if ($result->length == 1) $this->setLastUpdate($result->item(0)->data);

        /* The following elements need more attention */

        $result = $xpath->query('./rssurl/text()', $dom);
        if ($result->length == 1) $this->setRssUrl($result->item(0)->data);

        $result = $xpath->query('./atomurl/text()', $dom);
        if ($result->length == 1) $this->setAtomUrl($result->item(0)->data);

        $result = $xpath->query('./author', $dom);
        if ($result->length >= 1) {
            foreach ($result as $author) {
                $this->authors[] = new Author($author);
            }
        }

        /**
         * The following are optional elements
         *
         * I can't find any official documentation about the following properties
         * however they are included in response DTD and/or test responses.
         */

        $result = $xpath->query('./rank/text()', $dom);
        if ($result->length == 1) $this->setRank($result->item(0)->data);

        $result = $xpath->query('./lat/text()', $dom);
        if ($result->length == 1) $this->setLat($result->item(0)->data);

        $result = $xpath->query('./lon/text()', $dom);
        if ($result->length == 1) $this->setLon($result->item(0)->data);

        $result = $xpath->query('./hasphoto/text()', $dom);
        if ($result->length == 1) $this->setHasPhoto($result->item(0)->data);
    }


    /**
     * Returns weblog name.
     *
     * @return  string  Weblog name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns weblog URL.
     *
     * @return  null|\Zend\Uri\Http object representing weblog base URL
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns number of unique blogs linking this blog.
     *
     * @return  integer the number of inbound blogs
     */
    public function getInboundBlogs()
    {
        return $this->inboundBlogs;
    }

    /**
     * Returns number of incoming links to this blog.
     *
     * @return  integer the number of inbound links
     */
    public function getInboundLinks()
    {
        return $this->inboundLinks;
    }

    /**
     * Returns weblog Rss URL.
     *
     * @return  null|\Zend\Uri\Http object representing the URL
     *          of the RSS feed for given blog
     */
    public function getRssUrl()
    {
        return $this->rssUrl;
    }

    /**
     * Returns weblog Atom URL.
     *
     * @return  null|\Zend\Uri\Http object representing the URL
     *          of the Atom feed for given blog
     */
    public function getAtomUrl()
    {
        return $this->atomUrl;
    }

    /**
     * Returns UNIX timestamp of the last weblog update.
     *
     * @return  integer UNIX timestamp of the last weblog update
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Returns weblog rank value.
     *
     * Note. This property is not documented.
     *
     * @return  integer weblog rank value
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Returns weblog latitude coordinate.
     *
     * Note. This property is not documented.
     *
     * @return  float   weblog latitude coordinate
     */
    public function getLat() {
        return $this->lat;
    }

    /**
     * Returns weblog longitude coordinate.
     *
     * Note. This property is not documented.
     *
     * @return  float   weblog longitude coordinate
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Returns whether the author who claimed this weblog has a photo.
     *
     * Note. This property is not documented.
     *
     * @return  bool    TRUE if the author who claimed this weblog has a photo,
     *                  FALSE otherwise.
     */
    public function hasPhoto()
    {
        return (bool) $this->hasPhoto;
    }

    /**
     * Returns the array of weblog authors.
     *
     * @return  array of Author authors
     */
    public function getAuthors()
    {
        return (array) $this->authors;
    }


    /**
     * Sets weblog name.
     *
     * @param   string $name
     * @return  Weblog $this instance
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * Sets weblog URL.
     *
     * @param   string|\Zend\Uri\Http $url
     * @return  void
     * @throws  Exception\RuntimeException if $input is an invalid URI
     *          (via Utils::normalizeUriHttp)
     */
    public function setUrl($url)
    {
        $this->url = Utils::normalizeUriHttp($url);
        return $this;
    }

    /**
     * Sets number of inbound blogs.
     *
     * @param   integer $number
     * @return  Weblog $this instance
     */
    public function setInboundBlogs($number)
    {
        $this->inboundBlogs = (int) $number;
        return $this;
    }

    /**
     * Sets number of Iinbound links.
     *
     * @param   integer $number
     * @return  Weblog $this instance
     */
    public function setInboundLinks($number)
    {
        $this->inboundLinks = (int) $number;
        return $this;
    }

    /**
     * Sets weblog Rss URL.
     *
     * @param   string|\Zend\Uri\Http $url
     * @return  Weblog $this instance
     * @throws  Exception\RuntimeException if $input is an invalid URI
     *          (via Utils::normalizeUriHttp)
     */
    public function setRssUrl($url)
    {
        $this->rssUrl = Utils::normalizeUriHttp($url);
        return $this;
    }

    /**
     * Sets weblog Atom URL.
     *
     * @param   string|\Zend\Uri\Http $url
     * @return  Weblog $this instance
     * @throws  Exception\RuntimeException if $input is an invalid URI
     *          (via Utils::normalizeUriHttp)
     */
    public function setAtomUrl($url)
    {
        $this->atomUrl = Utils::normalizeUriHttp($url);
        return $this;
    }

    /**
     * Sets weblog Last Update timestamp.
     *
     * $datetime can be any value supported by
     * Utils::normalizeDate().
     *
     * @param   mixed $datetime A string representing the last update date time
     *                          in a valid date time format
     * @return  Weblog $this instance
     * @throws  Exception\RuntimeException
     */
    public function setLastUpdate($datetime)
    {
        $this->lastUpdate = Utils::normalizeDate($datetime);
        return $this;
    }

    /**
     * Sets weblog Rank.
     *
     * @param   integer $rank
     * @return  Weblog $this instance
     */
    public function setRank($rank)
    {
        $this->rank = (int) $rank;
        return $this;
    }

    /**
     * Sets weblog latitude coordinate.
     *
     * @param   float $coordinate
     * @return  Weblog $this instance
     */
    public function setLat($coordinate)
    {
        $this->lat = (float) $coordinate;
        return $this;
    }

    /**
     * Sets weblog longitude coordinate.
     *
     * @param   float $coordinate
     * @return  Weblog $this instance
     */
    public function setLon($coordinate)
    {
        $this->lon = (float) $coordinate;
        return $this;
    }

    /**
     * Sets hasPhoto property.
     *
     * @param   bool $hasPhoto
     * @return  Weblog $this instance
     */
    public function setHasPhoto($hasPhoto)
    {
        $this->hasPhoto = (bool) $hasPhoto;
        return $this;
    }

}
