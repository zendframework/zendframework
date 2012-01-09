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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Service\SlideShare;

/**
 * The SlideShow class represents a slide show on the slideshare.net servers.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage SlideShare
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
    protected $embedCode;

    /**
     * The URI for the thumbnail representation of the slide show
     *
     * @var string The URI of a thumbnail image
     */
    protected $thumbnailUrl;

    /**
     * The small URI for the thumbnail representation of the slide show
     *
     * @var string The small URI of a thumbnail image
     */
    protected $thumbnailSmallUrl;

    /**
     * The title of the slide show
     *
     * @var string The slide show title
     */
    protected $title;

    /**
     * The Description of the slide show
     *
     * @var string The slide show description
     */
    protected $description;

    /**
     * The status of the silde show on the server
     *
     * @var int The Slide show status code
     */
    protected $status;

    /**
     * The Description of the slide show status code
     *
     * @var string The status description
     */
    protected $statusDescription;

    /**
     * The Permanent link for the slide show
     *
     * @var string the Permalink for the slide show
     */
    protected $permalink;

    /**
     * The number of views this slide show has received
     *
     * @var int the number of views
     */
    protected $numViews;

    /**
     * The number of downloads this slide show has received
     *
     * @var int the number of downloads
     */
    protected $numDownloads;

    /**
     * The number of comments this slide show has received
     *
     * @var int the number of comments
     */
    protected $numComments;

    /**
     * The number of favorites this slide show has received
     *
     * @var int the number of favorites
     */
    protected $numFavorites;

    /**
     * The number of slides this slide show has received
     *
     * @var int the number of slides
     */
    protected $numSlides;

    /**
     * The ID of the slide show on the server
     *
     * @var int the Slide show ID number on the server
     */
    protected $slideShowId;

    /**
     * A slide show filename on the local filesystem (when uploading)
     *
     * @var string the local filesystem path & file of the slide show to upload
     */
    protected $slideShowFilename;

    /**
     * An array of tags associated with the slide show
     *
     * @var array An array of tags associated with the slide show
     */
    protected $tags = array();

    /**
     * An array of related alideshow ids associated with the slide show
     *
     * @var array An array of related alideshow ids associated with the slide show
     */
    protected $relatedSlideshowIds = array();

    /**
     * The location of the slide show
     *
     * @var string the Location
     */
    protected $location;

    /**
     * The username of the owner
     *
     * @var string the username
     */
    protected $username;

    /**
     * The created time
     *
     * @var string the created time
     */
    protected $created;

    /**
     * The updated time
     *
     * @var string the updated time
     */
    protected $updated;

    /**
     * The language
     *
     * @var string the language
     */
    protected $language;

    /**
     * The format
     *
     * @var string the format
     */
    protected $format;

    /**
     * Download possible
     *
     * @var bool Is download possible
     */
    protected $download;

    /**
     * Download URL
     *
     * @var string Download URL
     */
    protected $downloadUrl;

    /**
     * Retrieves the location of the slide show
     *
     * @return string the Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Sets the location of the slide show
     *
     * @param string $loc The location to use
     * @return SlideShow
     */
    public function setLocation($loc)
    {
        $this->location = (string) $loc;
        return $this;
    }

    /**
     * Adds a tag to the slide show
     *
     * @param string $tag The tag to add
     * @return SlideShow
     */
    public function addTag($tag)
    {
        $this->tags[] = (string) $tag;
        return $this;
    }

    /**
     * Sets the tags for the slide show
     *
     * @param array $tags An array of tags to set
     * @return SlideShow
     */
    public function setTags(Array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Gets all of the tags associated with the slide show
     *
     * @return array An array of tags for the slide show
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Adds a related slideshow id to the slide show
     *
     * @param int $id The related slideshow id to add
     * @return SlideShow
     */
    public function addRelatedSlideshowId($id)
    {
        $this->relatedSlideshowIds[] = (int) $id;
        return $this;
    }

    /**
     * Sets the related slideshow ids for the slide show
     *
     * @param array $ids An array of related slideshow ids to set
     * @return SlideShow
     */
    public function setRelatedSlideshowIds(Array $ids)
    {
        $this->relatedSlideshowIds = $ids;
        return $this;
    }

    /**
     * Gets all of the related slideshow ids associated with the slide show
     *
     * @return array An array of related slideshow ids for the slide show
     */
    public function getRelatedSlideshowIds()
    {
        return $this->relatedSlideshowIds;
    }

    /**
     * Sets the filename on the local filesystem of the slide show
     * (for uploading a new slide show)
     *
     * @param string $file The full path & filename to the slide show
     * @return SlideShow
     */
    public function setFilename($file)
    {
        $this->slideShowFilename = (string) $file;
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
        return $this->slideShowFilename;
    }

    /**
     * Sets the ID for the slide show
     *
     * @param int $id The slide show ID
     * @return SlideShow
     */
    public function setId($id)
    {
        $this->slideShowId = (string) $id;
        return $this;
    }

    /**
     * Gets the ID for the slide show
     *
     * @return int The slide show ID
     */
    public function getId()
    {
        return $this->slideShowId;
    }

    /**
     * Sets the HTML embed code for the slide show
     *
     * @param string $code The HTML embed code
     * @return SlideShow
     */
    public function setEmbedCode($code)
    {
        $this->embedCode = (string) $code;
        return $this;
    }

    /**
     * Retrieves the HTML embed code for the slide show
     *
     * @return string the HTML embed code
     */
    public function getEmbedCode()
    {
        return $this->embedCode;
    }

    /**
     * Sets the Thumbnail URI for the slide show
     *
     * @param string $url The URI for the thumbnail image
     * @return SlideShow
     */
    public function setThumbnailUrl($url)
    {
        $this->thumbnailUrl = (string) $url;
        return $this;
    }

    /**
     * Retrieves the Thumbnail URi for the slide show
     *
     * @return string The URI for the thumbnail image
     */
    public function getThumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    /**
     * Sets the Thumbnail Small URI for the slide show
     *
     * @param string $url The Small URI for the thumbnail image
     * @return SlideShow
     */
    public function setThumbnailSmallUrl($url)
    {
        $this->thumbnailSmallUrl = (string) $url;
        return $this;
    }

    /**
     * Retrieves the Thumbnail Small URI for the slide show
     *
     * @return string The Small URI for the thumbnail image
     */
    public function getThumbnailSmallUrl()
    {
        return $this->thumbnailSmallUrl;
    }

    /**
     * Sets the title for the Slide show
     *
     * @param string $title The slide show title
     * @return SlideShow
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
        return $this;
    }

    /**
     * Retrieves the Slide show title
     *
     * @return string the Slide show title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the description for the Slide show
     *
     * @param string $desc The description of the slide show
     * @return SlideShow
     */
    public function setDescription($desc)
    {
        $this->description = (string) $desc;
        return $this;
    }

    /**
     * Gets the description of the slide show
     *
     * @return string The slide show description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the numeric status of the slide show on the server
     *
     * @param int $status The numeric status on the server
     * @return SlideShow
     */
    public function setStatus($status)
    {
        $this->status = (int) $status;
        return $this;
    }

    /**
     * Gets the numeric status of the slide show on the server
     *
     * @return int A SlideShow Status constant
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the textual description of the status of the slide show on the server
     *
     * @param string $desc The textual description of the status of the slide show
     * @return SlideShow
     */
    public function setStatusDescription($desc)
    {
        $this->statusDescription = (string) $desc;
        return $this;
    }

    /**
     * Gets the textual description of the status of the slide show on the server
     *
     * @return string the textual description of the service
     */
    public function getStatusDescription()
    {
        return $this->statusDescription;
    }

    /**
     * Sets the permanent link of the slide show
     *
     * @param string $url The permanent URL for the slide show
     * @return SlideShow
     */
    public function setPermaLink($url)
    {
        $this->permalink = (string) $url;
        return $this;
    }

    /**
     * Gets the permanent link of the slide show
     *
     * @return string the permanent URL for the slide show
     */
    public function getPermaLink()
    {
        return $this->permalink;
    }

    /**
     * Sets the number of views the slide show has received
     *
     * @param int $views The number of views
     * @return SlideShow
     */
    public function setNumViews($views)
    {
        $this->numViews = (int) $views;
        return $this;
    }

    /**
     * Gets the number of views the slide show has received
     *
     * @return int The number of views
     */
    public function getNumViews()
    {
        return $this->numViews;
    }

    /**
     * Sets the number of downloads the slide show has received
     *
     * @param int $downloads The number of downloads
     * @return SlideShow
     */
    public function setNumDownloads($downloads)
    {
        $this->numDownloads = (int) $downloads;
        return $this;
    }

    /**
     * Gets the number of downloads the slide show has received
     *
     * @return int The number of downloads
     */
    public function getNumDownloads()
    {
        return $this->numDownloads;
    }

    /**
     * Sets the number of comments the slide show has received
     *
     * @param int $comments The number of comments
     * @return SlideShow
     */
    public function setNumComments($comments)
    {
        $this->numComments = (int) $comments;
        return $this;
    }

    /**
     * Gets the number of comments the slide show has received
     *
     * @return int The number of comments
     */
    public function getNumComments()
    {
        return $this->numComments;
    }

    /**
     * Sets the number of favorites the slide show has received
     *
     * @param int $favorites The number of favorites
     * @return SlideShow
     */
    public function setNumFavorites($favorites)
    {
        $this->numFavorites = (int) $favorites;
        return $this;
    }

    /**
     * Gets the number of favorites the slide show has received
     *
     * @return int The number of favorites
     */
    public function getNumFavorites()
    {
        return $this->numFavorites;
    }

    /**
     * Sets the number of slides the slide show has received
     *
     * @param int $slides The number of slides
     * @return SlideShow
     */
    public function setNumSlides($slides)
    {
        $this->numSlides = (int) $slides;
        return $this;
    }

    /**
     * Gets the number of slides the slide show has received
     *
     * @return int The number of slides
     */
    public function getNumSlides()
    {
        return $this->numSlides;
    }

    /**
     * Sets the username of the slideshow owner
     *
     * @param string $username The username
     * @return SlideShow
     */
    public function setUsername($username)
    {
        $this->username = (string) $username;
        return $this;
    }

    /**
     * Gets the username of the owner
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the created time
     *
     * @param string $time The created time
     * @return SlideShow
     */
    public function setCreated($time)
    {
        $this->created = (string) $time;
        return $this;
    }

    /**
     * Gets the created time
     *
     * @return string The created time
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Sets the updated time
     *
     * @param string $time The updated time
     * @return SlideShow
     */
    public function setUpdated($time)
    {
        $this->updated = (string) $time;
        return $this;
    }

    /**
     * Gets the updated time
     *
     * @return string The updated time
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Sets the language
     *
     * @param string $language The language
     * @return SlideShow
     */
    public function setLanguage($language)
    {
        $this->language = (string) $language;
        return $this;
    }

    /**
     * Gets the language
     *
     * @return string The language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the format
     *
     * @param string $format The format
     * @return SlideShow
     */
    public function setFormat($format)
    {
        $this->format = (string) $format;
        return $this;
    }

    /**
     * Gets the format
     *
     * @return string The format
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Sets if download is possible or not
     *
     * @param bool $downloadPossible
     * @return SlideShow
     */
    public function setDownload($downloadPossible)
    {
        $this->download = (bool) $downloadPossible;
        return $this;
    }

    /**
     * Gets if download is possible
     *
     * @return bool if download is possible
     */
    public function getDownload()
    {
        return $this->download;
    }

    /**
     * Sets the download URL
     *
     * @param string $downloadUrl The download URL
     * @return SlideShow
     */
    public function setDownloadUrl($downloadUrl)
    {
        $this->downloadUrl = (string) $downloadUrl;
        return $this;
    }

    /**
     * Gets the download URL
     *
     * @return string The download URL
     */
    public function getDownloadUrl()
    {
        return $this->downloadUrl;
    }



}
