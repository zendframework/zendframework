<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\Color;

/**
 * PDF provides a powerfull facilities for specifying the colors of graphics objects.
 * This class encapsulates color behaviour.
 *
 * Some colors interact with PDF document (create additional objects in a PDF),
 * others don't do it. That is defined in a subclasses.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Color
 */
interface ColorInterface
{
    /**
     * Instructions, which can be directly inserted into content stream
     * to switch color.
     * Color set instructions differ for stroking and nonstroking operations.
     *
     * @param boolean $stroking
     * @return string
     */
    public function instructions($stroking);

    /**
     * Get color components (color space dependent)
     *
     * @return array
     */
    public function getComponents();
}
