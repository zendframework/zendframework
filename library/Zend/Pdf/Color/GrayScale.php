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

use Zend\Pdf\InternalType;

/**
 * GrayScale color implementation
 *
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Color
 */
class GrayScale implements ColorInterface
{
    /**
     * GrayLevel.
     * 0.0 (black) - 1.0 (white)
     *
     * @var \Zend\Pdf\InternalType\NumericObject
     */
    private $_grayLevel;

    /**
     * Object constructor
     *
     * @param float $grayLevel
     */
    public function __construct($grayLevel)
    {
        if ($grayLevel < 0) { $grayLevel = 0; }
        if ($grayLevel > 1) { $grayLevel = 1; }

        $this->_grayLevel = new InternalType\NumericObject($grayLevel);
    }

    /**
     * Instructions, which can be directly inserted into content stream
     * to switch color.
     * Color set instructions differ for stroking and nonstroking operations.
     *
     * @param boolean $stroking
     * @return string
     */
    public function instructions($stroking)
    {
        return $this->_grayLevel->toString() . ($stroking? " G\n" : " g\n");
    }

    /**
     * Get color components (color space dependent)
     *
     * @return array
     */
    public function getComponents()
    {
        return array($this->_grayLevel->value);
    }
}
