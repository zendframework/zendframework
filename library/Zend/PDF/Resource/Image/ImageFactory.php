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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\Resource\Image;
use Zend\PDF;

/**
 * Zend_PDF_ImageFactory
 *
 * Helps manage the diverse set of supported image file types.
 *
 * @uses       \Zend\PDF\Exception
 * @uses       \Zend\PDF\Resource\Image\JPEG
 * @uses       \Zend\PDF\Resource\Image\PNG
 * @uses       \Zend\PDF\Resource\Image\TIFF
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Image
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @todo       Use Zend_Mime not file extension for type determination.
 */
class ImageFactory
{
    public static function factory($filename) {
        if(!is_file($filename)) {
            throw new PDF\Exception("Cannot create image resource. File not found.");
        }
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        /*
         * There are plans to use Zend_Mime and not file extension. In the mean time, if you need to
         * use an alternate file extension just spin up the right processor directly.
         */
        switch (strtolower($extension)) {
            case 'tif':
                //Fall through to next case;
            case 'tiff':
                return new TIFF($filename);
                break;
            case 'png':
                return new PNG($filename);
                break;
            case 'jpg':
                //Fall through to next case;
            case 'jpe':
                //Fall through to next case;
            case 'jpeg':
                return new JPEG($filename);
                break;
            default:
                throw new PDF\Exception("Cannot create image resource. File extension not known or unsupported type.");
                break;
        }
    }
}

