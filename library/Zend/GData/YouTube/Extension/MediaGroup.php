<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\YouTube\Extension;

/**
 * This class represents the media:group element of Media RSS.
 * It allows the grouping of media:content elements that are
 * different representations of the same content.  When it exists,
 * it is a child of an Entry (Atom) or Item (RSS).
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 */
class MediaGroup extends \Zend\GData\Media\Extension\MediaGroup
{

    protected $_rootElement = 'group';
    protected $_rootNamespace = 'media';

    /**
     * @var Duration
     */
    protected $_duration = null;

    /**
     * @var Private
     */
    protected $_private = null;

    /**
     * @var VideoId
     */
    protected $_videoid = null;

    /**
     * @var MediaRating
     */
    protected $_mediarating = null;

    /**
     * @var MediaCredit
     */
    protected $_mediacredit = null;

    /**
     * @var Uploaded
     */
    protected $_uploaded = null;

    public function __construct($element = null)
    {
        $this->registerAllNamespaces(\Zend\GData\YouTube::$namespaces);
        parent::__construct($element);
    }

    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_duration !== null) {
            $element->appendChild(
                $this->_duration->getDOM($element->ownerDocument));
        }
        if ($this->_private !== null) {
            $element->appendChild(
                $this->_private->getDOM($element->ownerDocument));
        }
        if ($this->_videoid != null) {
            $element->appendChild(
                $this->_videoid->getDOM($element->ownerDocument));
        }
        if ($this->_uploaded != null) {
            $element->appendChild(
                $this->_uploaded->getDOM($element->ownerDocument));
        }
        if ($this->_mediacredit != null) {
            $element->appendChild(
                $this->_mediacredit->getDOM($element->ownerDocument));
        }
        if ($this->_mediarating != null) {
            $element->appendChild(
                $this->_mediarating->getDOM($element->ownerDocument));
        }
        return $element;
    }

    /**
     * Creates individual Entry objects of the appropriate type and
     * stores them in the $_entry array based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
            case $this->lookupNamespace('media') . ':' . 'content':
                $content = new MediaContent();
                $content->transferFromDOM($child);
                $this->_content[] = $content;
                break;
            case $this->lookupNamespace('media') . ':' . 'rating':
                $mediarating = new MediaRating();
                $mediarating->transferFromDOM($child);
                $this->_mediarating = $mediarating;
                break;
            case $this->lookupNamespace('media') . ':' . 'credit':
                $mediacredit = new MediaCredit();
                $mediacredit->transferFromDOM($child);
                $this->_mediacredit = $mediacredit;
                break;
            case $this->lookupNamespace('yt') . ':' . 'duration':
                $duration = new Duration();
                $duration->transferFromDOM($child);
                $this->_duration = $duration;
                break;
            case $this->lookupNamespace('yt') . ':' . 'private':
                $private = new PrivateExtension();
                $private->transferFromDOM($child);
                $this->_private = $private;
                break;
            case $this->lookupNamespace('yt') . ':' . 'videoid':
                $videoid = new VideoId();
                $videoid ->transferFromDOM($child);
                $this->_videoid = $videoid;
                break;
            case $this->lookupNamespace('yt') . ':' . 'uploaded':
                $uploaded = new Uploaded();
                $uploaded ->transferFromDOM($child);
                $this->_uploaded = $uploaded;
                break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Returns the duration value of this element
     *
     * @return Duration
     */
    public function getDuration()
    {
        return $this->_duration;
    }

    /**
     * Sets the duration value of this element
     *
     * @param Duration $value The duration value
     * @return MediaGroup Provides a fluent
     *         interface
     */
    public function setDuration($value)
    {
        $this->_duration = $value;
        return $this;
    }

    /**
     * Returns the videoid value of this element
     *
     * @return VideoId
     */
    public function getVideoId()
    {
        return $this->_videoid;
    }

    /**
     * Sets the videoid value of this element
     *
     * @param VideoId $value The video id value
     * @return MediaGroup Provides a fluent
     *         interface
     */
    public function setVideoId($value)
    {
        $this->_videoid = $value;
        return $this;
    }

    /**
     * Returns the yt:uploaded element
     *
     * @return Uploaded
     */
    public function getUploaded()
    {
        return $this->_uploaded;
    }

    /**
     * Sets the yt:uploaded element
     *
     * @param Uploaded $value The uploaded value
     * @return MediaGroup Provides a fluent
     *         interface
     */
    public function setUploaded($value)
    {
        $this->_uploaded = $value;
        return $this;
    }

    /**
     * Returns the private value of this element
     *
     * @return Private
     */
    public function getPrivate()
    {
        return $this->_private;
    }

    /**
     * Sets the private value of this element
     *
     * @param Private $value The private value
     * @return MediaGroup Provides a fluent
     *         interface
     */
    public function setPrivate($value)
    {
        $this->_private = $value;
        return $this;
    }

    /**
     * Returns the rating value of this element
     *
     * @return MediaRating
     */
    public function getMediaRating()
    {
        return $this->_mediarating;
    }

    /**
     * Sets the media:rating value of this element
     *
     * @param MediaRating $value The rating element
     * @return MediaGroup Provides a fluent
     *         interface
     */
    public function setMediaRating($value)
    {
        $this->_mediarating = $value;
        return $this;
    }

    /**
     * Returns the media:credit value of this element
     *
     * @return MediaCredit
     */
    public function getMediaCredit()
    {
        return $this->_mediacredit;
    }

    /**
     * Sets the media:credit value of this element
     *
     * @param MediaCredit $value The credit element
     * @return MediaGroup Provides a fluent
     *         interface
     */
    public function setMediaCredit($value)
    {
        $this->_mediacredit = $value;
        return $this;
    }
}
