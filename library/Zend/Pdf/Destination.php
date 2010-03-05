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
 * @package    Zend_Pdf
 * @subpackage Destination
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Abstract PDF destination representation class
 *
 * @uses       Zend_Pdf_Destination_Fit
 * @uses       Zend_Pdf_Destination_FitBoundingBox
 * @uses       Zend_Pdf_Destination_FitBoundingBoxHorizontally
 * @uses       Zend_Pdf_Destination_FitBoundingBoxVertically
 * @uses       Zend_Pdf_Destination_FitHorizontally
 * @uses       Zend_Pdf_Destination_FitRectangle
 * @uses       Zend_Pdf_Destination_FitVertically
 * @uses       Zend_Pdf_Destination_Named
 * @uses       Zend_Pdf_Destination_Unknown
 * @uses       Zend_Pdf_Destination_Zoom
 * @uses       Zend_Pdf_Element
 * @uses       Zend_Pdf_Exception
 * @uses       Zend_Pdf_Target
 * @package    Zend_Pdf
 * @subpackage Destination
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Pdf_Destination extends Zend_Pdf_Target
{
    /**
     * Load Destination object from a specified resource
     *
     * @internal
     * @param $destinationArray
     * @return Zend_Pdf_Destination
     */
    public static function load(Zend_Pdf_Element $resource)
    {
        if ($resource->getType() == Zend_Pdf_Element::TYPE_NAME  ||  $resource->getType() == Zend_Pdf_Element::TYPE_STRING) {
            return new Zend_Pdf_Destination_Named($resource);
        }

        if ($resource->getType() != Zend_Pdf_Element::TYPE_ARRAY) {
            throw new Zend_Pdf_Exception('An explicit destination must be a direct or an indirect array object.');
        }
        if (count($resource->items) < 2) {
            throw new Zend_Pdf_Exception('An explicit destination array must contain at least two elements.');
        }

        switch ($resource->items[1]->value) {
            case 'XYZ':
                return new Zend_Pdf_Destination_Zoom($resource);
                break;

            case 'Fit':
                return new Zend_Pdf_Destination_Fit($resource);
                break;

            case 'FitH':
                return new Zend_Pdf_Destination_FitHorizontally($resource);
                break;

            case 'FitV':
                return new Zend_Pdf_Destination_FitVertically($resource);
                break;

            case 'FitR':
                return new Zend_Pdf_Destination_FitRectangle($resource);
                break;

            case 'FitB':
                return new Zend_Pdf_Destination_FitBoundingBox($resource);
                break;

            case 'FitBH':
                return new Zend_Pdf_Destination_FitBoundingBoxHorizontally($resource);
                break;

            case 'FitBV':
                return new Zend_Pdf_Destination_FitBoundingBoxVertically($resource);
                break;

            default:
                return new Zend_Pdf_Destination_Unknown($resource);
                break;
        }
    }
}
