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
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Image
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
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
 * @uses       \Zend\Pdf\InternalType\NameObject
 * @uses       \Zend\Pdf\Resource\AbstractResource
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Image
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

