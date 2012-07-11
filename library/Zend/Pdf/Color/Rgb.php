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
 * RGB color implementation
 *
 * @category   Zend
 * @subpackage Zend_PDF_Color
 */
class Rgb implements ColorInterface
{
    /**
     * Red level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var \Zend\Pdf\InternalType\NumericObject
     */
    private $_r;

    /**
     * Green level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var \Zend\Pdf\InternalType\NumericObject
     */
    private $_g;

    /**
     * Blue level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var \Zend\Pdf\InternalType\NumericObject
     */
    private $_b;


    /**
     * Object constructor
     *
     * @param float $r
     * @param float $g
     * @param float $b
     */
    public function __construct($r, $g, $b)
    {
        /** Clamp values to legal limits. */
        if ($r < 0) { $r = 0; }
        if ($r > 1) { $r = 1; }

        if ($g < 0) { $g = 0; }
        if ($g > 1) { $g = 1; }

        if ($b < 0) { $b = 0; }
        if ($b > 1) { $b = 1; }

        $this->_r = new InternalType\NumericObject($r);
        $this->_g = new InternalType\NumericObject($g);
        $this->_b = new InternalType\NumericObject($b);
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
        return $this->_r->toString() . ' '
             . $this->_g->toString() . ' '
             . $this->_b->toString() .     ($stroking? " RG\n" : " rg\n");
    }

    /**
     * Get color components (color space dependent)
     *
     * @return array
     */
    public function getComponents()
    {
        return array($this->_r->value, $this->_g->value, $this->_b->value);
    }
}
