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

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Flickr
 */
class Image
{
    /**
     * The URI of the image
     *
     * @var string
     */
    public $uri;

    /**
     * The URI for linking to the photo on Flickr
     *
     * @var string
     */
    public $clickUri;

    /**
     * The height of the image in pixels
     *
     * @var string
     */
    public $height;

    /**
     * The width of the image in pixels
     *
     * @var string
     */
    public $width;

    /**
     * Parse given Flickr Image element
     *
     * @param DOMElement $image
     */
    public function __construct(DOMElement $image)
    {
        $this->uri      = (string)$image->getAttribute('source');
        $this->clickUri = (string)$image->getAttribute('url');
        $this->height   = (int)$image->getAttribute('height');
        $this->width    = (int)$image->getAttribute('width');
    }
}
