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
 * @subpackage SlideShare
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Service\SlideShare;

/**
 * The Zend_Service_SlideShare_SlideShow class represents a slide show on the
 * slideshare.net servers.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage SlideShare
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SlideShow
{

    /**
     * Status constant mapping for web service
     *
     */
    const STATUS_QUEUED = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_READY = 2;
    const STATUS_FAILED = 3;

    /**
     * The HTML code to embed the slide show in a web page
     *
     * @var string the HTML to embed the slide show
     */
    protected $_embedCode;

    /**
     * The URI for the thumbnail representation of the slide show
     *
     * @var string The URI of a thumbnail image
     */
    protected $_thumbnailUrl;

    /**
     * The small URI for the thumbnail representation of the slide show
     *
     * @var string The small URI of a thumbnail image
     */
    protected $_thumbnailSmallUrl;

    /**
     * The title of the slide show
     *
     * @var string The slide show title
     */
    protected $_title;

    /**
     * The Description of the slide show
     *
     * @var string The slide show description
     */
    protected $_description;

    /**
     * The status of the silde show on the server
     *
     * @var int The Slide show status code
     */
    protected $_status;

    /**
     * The Description of the slide show status code
     *
     * @var string The status description
     */
    protected $_statusDescription;

    /**
     * The Permanent link for the slide show
     *
     * @var string the Permalink for the slide show
     */
    protected $_permalink;

    /**
     * The number of views this slide show has received
     *
     * @var int the number of views
     */
    protected $_numViews;

    /**
     * The number of downloads this slide show has received
     *
     * @var int the number of downloads
     */
    protected $_numDownloads;

    /**
     * The number of comments this slide show has received
     *
     * @var int the number of comments
     */
    protected $_numComments;

    /**
     * The number of favorites this slide show has received
     *
     * @var int the number of favorites
     */
    protected $_numFavorites;

    /**
     * The number of slides this slide show has received
     *
     * @var int the number of slides
     */
    protected $_numSlides;

    /**
     * The ID of the slide show on the server
     *
     * @var int the Slide show ID number on the server
     */
    protected $_slideShowId;

    /**
     * A slide show filename on the local filesystem (when uploading)
     *
     * @var string the local filesystem path & file of the slide show to upload
     */
    protected $_slideShowFilename;

    /**
     * An array of tags associated with the slide show
     *
     * @var array An array of tags associated with the slide show
     */
    protected $_tags = array();

    /**
     * An array of related alideshow ids associated with the slide show
     *
     * @var array An array of related alideshow ids associated with the slide show
     */
    protected $_relatedSlideshowIds = array();

    /**
     * The location of the slide show
     *
     * @var string the Location
     */
    protected $_location;

    /**
     * The username of the owner
     *
     * @var string the username
     */
    protected $_username;

    /**
     * The created time
     *
     * @var string the created time
     */
    protected $_created;

    /**
     * The updated time
     *
     * @var string the updated time
     */
    protected $_updated;

    /**
     * The language
     *
     * @var string the language
     */
    protected $_language;

    /**
     * The format
     *
     * @var string the format
     */
    protected $_format;

    /**
     * Download possible
     *
     * @var bool Is download possible
     */
    protected $_download;

    /**
     * Download URL
     *
     * @var string Download URL
     */
    protected $_downloadUrl;

    /**
     * Retrieves the location of the slide show
     *
     * @return string the Location
     */
    public function getLocation()
    {
        return $this->_location;
    }

    /**
     * Sets the location of the slide show
     *
     * @param string $loc The location to use
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setLocation($loc)
    {
        $this->_location = (string)$loc;
        return $this;
    }

    /**
     * Adds a tag to the slide show
     *
     * @param string $tag The tag to add
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function addTag($tag)
    {
        $this->_tags[] = (string)$tag;
        return $this;
    }

    /**
     * Sets the tags for the slide show
     *
     * @param array $tags An array of tags to set
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setTags(Array $tags)
    {
        $this->_tags = $tags;
        return $this;
    }

    /**
     * Gets all of the tags associated with the slide show
     *
     * @return array An array of tags for the slide show
     */
    public function getTags()
    {
        return $this->_tags;
    }

    /**
     * Adds a related slideshow id to the slide show
     *
     * @param int $id The related slideshow id to add
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function addRelatedSlideshowId($id)
    {
        $this->_relatedSlideshowIds[] = (int)$id;
        return $this;
    }

    /**
     * Sets the related slideshow ids for the slide show
     *
     * @param array $ids An array of related slideshow ids to set
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setRelatedSlideshowIds(Array $ids)
    {
        $this->_relatedSlideshowIds = $ids;
        return $this;
    }

    /**
     * Gets all of the related slideshow ids associated with the slide show
     *
     * @return array An array of related slideshow ids for the slide show
     */
    public function getRelatedSlideshowIds()
    {
        return $this->_relatedSlideshowIds;
    }

    /**
     * Sets the filename on the local filesystem of the slide show
     * (for uploading a new slide show)
     *
     * @param string $file The full path & filename to the slide show
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setFilename($file)
    {
        $this->_slideShowFilename = (string)$file;
        return $this;
    }

    /**
     * Retrieves the filename on the local filesystem of the slide show
     * which will be uploaded
     *
     * @return string The full path & filename to the slide show
     */
    public function getFilename()
    {
        return $this->_slideShowFilename;
    }

    /**
     * Sets the ID for the slide show
     *
     * @param int $id The slide show ID
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setId($id)
    {
        $this->_slideShowId = (string)$id;
        return $this;
    }

    /**
     * Gets the ID for the slide show
     *
     * @return int The slide show ID
     */
    public function getId()
    {
        return $this->_slideShowId;
    }

    /**
     * Sets the HTML embed code for the slide show
     *
     * @param string $code The HTML embed code
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setEmbedCode($code)
    {
        $this->_embedCode = (string)$code;
        return $this;
    }

    /**
     * Retrieves the HTML embed code for the slide show
     *
     * @return string the HTML embed code
     */
    public function getEmbedCode()
    {
        return $this->_embedCode;
    }

    /**
     * Sets the Thumbnail URI for the slide show
     *
     * @param string $url The URI for the thumbnail image
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setThumbnailUrl($url)
    {
        $this->_thumbnailUrl = (string) $url;
        return $this;
    }

    /**
     * Retrieves the Thumbnail URi for the slide show
     *
     * @return string The URI for the thumbnail image
     */
    public function getThumbnailUrl()
    {
        return $this->_thumbnailUrl;
    }

    /**
     * Sets the Thumbnail Small URI for the slide show
     *
     * @param string $url The Small URI for the thumbnail image
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setThumbnailSmallUrl($url)
    {
        $this->_thumbnailSmallUrl = (string) $url;
        return $this;
    }

    /**
     * Retrieves the Thumbnail Small URI for the slide show
     *
     * @return string The Small URI for the thumbnail image
     */
    public function getThumbnailSmallUrl()
    {
        return $this->_thumbnailSmallUrl;
    }

    /**
     * Sets the title for the Slide show
     *
     * @param string $title The slide show title
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setTitle($title)
    {
        $this->_title = (string)$title;
        return $this;
    }

    /**
     * Retrieves the Slide show title
     *
     * @return string the Slide show title
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Sets the description for the Slide show
     *
     * @param string $desc The description of the slide show
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setDescription($desc)
    {
        $this->_description = (string)$desc;
        return $this;
    }

    /**
     * Gets the description of the slide show
     *
     * @return string The slide show description
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * Sets the numeric status of the slide show on the server
     *
     * @param int $status The numeric status on the server
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setStatus($status)
    {
        $this->_status = (int)$status;
        return $this;
    }

    /**
     * Gets the numeric status of the slide show on the server
     *
     * @return int A Zend_Service_SlideShare_SlideShow Status constant
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Sets the textual description of the status of the slide show on the server
     *
     * @param string $desc The textual description of the status of the slide show
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setStatusDescription($desc)
    {
        $this->_statusDescription = (string)$desc;
        return $this;
    }

    /**
     * Gets the textual description of the status of the slide show on the server
     *
     * @return string the textual description of the service
     */
    public function getStatusDescription()
    {
        return $this->_statusDescription;
    }

    /**
     * Sets the permanent link of the slide show
     *
     * @param string $url The permanent URL for the slide show
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setPermaLink($url)
    {
        $this->_permalink = (string)$url;
        return $this;
    }

    /**
     * Gets the permanent link of the slide show
     *
     * @return string the permanent URL for the slide show
     */
    public function getPermaLink()
    {
        return $this->_permalink;
    }

    /**
     * Sets the number of views the slide show has received
     *
     * @param int $views The number of views
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setNumViews($views)
    {
        $this->_numViews = (int)$views;
        return $this;
    }

    /**
     * Gets the number of views the slide show has received
     *
     * @return int The number of views
     */
    public function getNumViews()
    {
        return $this->_numViews;
    }

    /**
     * Sets the number of downloads the slide show has received
     *
     * @param int $downloads The number of downloads
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setNumDownloads($downloads)
    {
        $this->_numDownloads = (int)$downloads;
        return $this;
    }

    /**
     * Gets the number of downloads the slide show has received
     *
     * @return int The number of downloads
     */
    public function getNumDownloads()
    {
        return $this->_numDownloads;
    }

    /**
     * Sets the number of comments the slide show has received
     *
     * @param int $comments The number of comments
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setNumComments($comments)
    {
        $this->_numComments = (int)$comments;
        return $this;
    }

    /**
     * Gets the number of comments the slide show has received
     *
     * @return int The number of comments
     */
    public function getNumComments()
    {
        return $this->_numComments;
    }

    /**
     * Sets the number of favorites the slide show has received
     *
     * @param int $favorites The number of favorites
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setNumFavorites($favorites)
    {
        $this->_numFavorites = (int)$favorites;
        return $this;
    }

    /**
     * Gets the number of favorites the slide show has received
     *
     * @return int The number of favorites
     */
    public function getNumFavorites()
    {
        return $this->_numFavorites;
    }

    /**
     * Sets the number of slides the slide show has received
     *
     * @param int $slides The number of slides
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setNumSlides($slides)
    {
        $this->_numSlides = (int)$slides;
        return $this;
    }

    /**
     * Gets the number of slides the slide show has received
     *
     * @return int The number of slides
     */
    public function getNumSlides()
    {
        return $this->_numSlides;
    }

    /**
     * Sets the username of the slideshow owner
     *
     * @param string $username The username
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setUsername($username)
    {
        $this->_username = (string)$username;
        return $this;
    }

    /**
     * Gets the username of the owner
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Sets the created time
     *
     * @param string $time The created time
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setCreated($time)
    {
        $this->_created = (string)$time;
        return $this;
    }

    /**
     * Gets the created time
     *
     * @return string The created time
     */
    public function getCreated()
    {
        return $this->_created;
    }

    /**
     * Sets the updated time
     *
     * @param string $time The updated time
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setUpdated($time)
    {
        $this->_updated = (string)$time;
        return $this;
    }

    /**
     * Gets the updated time
     *
     * @return string The updated time
     */
    public function getUpdated()
    {
        return $this->_updated;
    }

    /**
     * Sets the language
     *
     * @param string $language The language
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setLanguage($language)
    {
        $this->_language = (string)$language;
        return $this;
    }

    /**
     * Gets the language
     *
     * @return string The language
     */
    public function getLanguage()
    {
        return $this->_language;
    }

    /**
     * Sets the format
     *
     * @param string $format The format
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setFormat($format)
    {
        $this->_format = (string)$format;
        return $this;
    }

    /**
     * Gets the format
     *
     * @return string The format
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * Sets if download is possible or not
     *
     * @param bool $downloadPossible
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setDownload($downloadPossible)
    {
        $this->_download = (bool)$downloadPossible;
        return $this;
    }

    /**
     * Gets if download is possible
     *
     * @return bool if download is possible
     */
    public function getDownload()
    {
        return $this->_download;
    }

    /**
     * Sets the download URL
     *
     * @param string $downloadUrl The download URL
     * @return Zend_Service_SlideShare_SlideShow
     */
    public function setDownloadUrl($downloadUrl)
    {
        $this->_downloadUrl = (string)$downloadUrl;
        return $this;
    }

    /**
     * Gets the download URL
     *
     * @return string The download URL
     */
    public function getDownloadUrl()
    {
        return $this->_downloadUrl;
    }



}
