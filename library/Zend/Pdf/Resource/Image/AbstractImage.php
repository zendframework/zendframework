<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\Resource\Image;

use Zend\Pdf\InternalType;
use Zend\Pdf\Resource;

/**
 * Image abstraction.
 *
 * Class is named not in accordance to the name convention.
 * It's "end-user" class, but its ancestor is not.
 * Thus part of the common class name is removed.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Image
 */
abstract class AbstractImage extends Resource\AbstractResource
{
    /**
     * Object constructor.
     */
    public function __construct()
    {
        parent::__construct('');

        $this->_resource->dictionary->Type    = new InternalType\NameObject('XObject');
        $this->_resource->dictionary->Subtype = new InternalType\NameObject('Image');
    }
    /**
     * get the height in pixels of the image
     *
     * @return integer
     */
    abstract public function getPixelHeight();

    /**
     * get the width in pixels of the image
     *
     * @return integer
     */
    abstract public function getPixelWidth();

    /**
     * gets an associative array of information about an image
     *
     * @return array
     */
    abstract public function getProperties();
}

