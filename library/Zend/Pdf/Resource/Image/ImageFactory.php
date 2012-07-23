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

use Zend\Pdf;
use Zend\Pdf\Exception;

/**
 * \Zend\Pdf\Resource\Image\ImageFactory
 *
 * Helps manage the diverse set of supported image file types.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Image
 * @todo       Use Zend_Mime not file extension for type determination.
 */
class ImageFactory
{
    public static function factory($filename)
    {
        if(!is_file($filename)) {
            throw new Exception\IOException("Cannot create image resource. File not found.");
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
                return new Tiff($filename);
                break;
            case 'png':
                return new Png($filename);
                break;
            case 'jpg':
                //Fall through to next case;
            case 'jpe':
                //Fall through to next case;
            case 'jpeg':
                return new Jpeg($filename);
                break;
            default:
                throw new Exception\InvalidArgumentException('Cannot create image resource. File extension not known or unsupported type.');
                break;
        }
    }
}
