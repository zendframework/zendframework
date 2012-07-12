<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Flickr;

use DOMElement;
use DOMXPath;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Flickr
 */
class Result
{
    /**
     * The photo's Flickr ID.
     *
     * @var string
     */
    public $id;

    /**
     * The photo owner's NSID.
     *
     * @var string
     */
    public $owner;

    /**
     * A key used in URI construction.
     *
     * @var string
     */
    public $secret;

    /**
     * The server name to use for URI construction.
     *
     * @var string
     */
    public $server;

    /**
     * The photo's title.
     *
     * @var string
     */
    public $title;

    /**
     * Whether the photo is public.
     *
     * @var string
     */
    public $isPublic;

    /**
     * Whether the photo is visible to you because you are a friend of the owner.
     *
     * @var string
     */
    public $isFriend;

    /**
     * Whether the photo is visible to you because you are family of the owner.
     *
     * @var string
     */
    public $isFamily;

    /**
     * The license the photo is available under.
     *
     * @var string
     */
    public $license;

    /**
     * The date the photo was uploaded.
     *
     * @var string
     */
    public $dateUpload;

    /**
     * The date the photo was taken.
     *
     * @var string
     */
    public $dateTaken;

    /**
     * The screen name of the owner.
     *
     * @var string
     */
    public $ownerName;

    /**
     * The server used in assembling icon URLs.
     *
     * @var string
     */
    public $iconServer;

    /**
     * A 75x75 pixel square thumbnail of the image.
     *
     * @var Image
     */
    public $square;

    /**
     * A 100 pixel thumbnail of the image.
     *
     * @var Image
     */
    public $thumbnail;

    /**
     * A 240 pixel version of the image.
     *
     * @var Image
     */
    public $small;

    /**
     * A 500 pixel version of the image.
     *
     * @var Image
     */
    public $medium;

    /**
     * A 640 pixel version of the image.
     *
     * @var Image
     */
    public $large;

    /**
     * The original image.
     *
     * @var Image
     */
    public $original;

    /**
     * Original Flickr object.
     *
     * @var Flickr
     */
    protected $flickr;

    /**
     * Parse the Flickr Result
     *
     * @param  DOMElement $image
     * @param  Flickr     $flickr Original Flickr object with which the request was made
     */
    public function __construct(DOMElement $image, Flickr $flickr)
    {
        $xpath = new DOMXPath($image->ownerDocument);

        foreach ($xpath->query('./@*', $image) as $property) {
            $this->{$property->name} = (string)$property->value;
        }

        $this->flickr = $flickr;

        foreach ($this->flickr->getImageDetails($this->id) as $k => $v) {
            $this->$k = $v;
        }
    }
}
