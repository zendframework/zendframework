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
 * CMYK color implementation
 *
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Color
 */
class Cmyk implements ColorInterface
{
    /**
     * Cyan level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var \Zend\Pdf\InternalType\NumericObject
     */
    private $_c;

    /**
     * Magenta level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var \Zend\Pdf\InternalType\NumericObject
     */
    private $_m;

    /**
     * Yellow level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var \Zend\Pdf\InternalType\NumericObject
     */
    private $_y;

    /**
     * Key (BlacK) level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var \Zend\Pdf\InternalType\NumericObject
     */
    private $_k;


    /**
     * Object constructor
     *
     * @param float $c
     * @param float $m
     * @param float $y
     * @param float $k
     */
    public function __construct($c, $m, $y, $k)
    {
        if ($c < 0) { $c = 0; }
        if ($c > 1) { $c = 1; }

        if ($m < 0) { $m = 0; }
        if ($m > 1) { $m = 1; }

        if ($y < 0) { $y = 0; }
        if ($y > 1) { $y = 1; }

        if ($k < 0) { $k = 0; }
        if ($k > 1) { $k = 1; }

        $this->_c = new InternalType\NumericObject($c);
        $this->_m = new InternalType\NumericObject($m);
        $this->_y = new InternalType\NumericObject($y);
        $this->_k = new InternalType\NumericObject($k);
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
        return $this->_c->toString() . ' '
             . $this->_m->toString() . ' '
             . $this->_y->toString() . ' '
             . $this->_k->toString() .     ($stroking? " K\n" : " k\n");
    }

    /**
     * Get color components (color space dependent)
     *
     * @return array
     */
    public function getComponents()
    {
        return array($this->_c->value, $this->_m->value, $this->_y->value, $this->_k->value);
    }
}
