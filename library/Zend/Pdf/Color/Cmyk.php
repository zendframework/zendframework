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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Pdf_Color */
require_once 'Zend/Pdf/Color.php';

/** Zend_Pdf_Element_Numeric */
require_once 'Zend/Pdf/Element/Numeric.php';

/**
 * CMYK color implementation
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf_Color_Cmyk extends Zend_Pdf_Color
{
    /**
     * Cyan level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var Zend_Pdf_Element_Numeric
     */
    private $_c;

    /**
     * Magenta level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var Zend_Pdf_Element_Numeric
     */
    private $_m;

    /**
     * Yellow level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var Zend_Pdf_Element_Numeric
     */
    private $_y;

    /**
     * Key (BlacK) level.
     * 0.0 (zero concentration) - 1.0 (maximum concentration)
     *
     * @var Zend_Pdf_Element_Numeric
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
        $this->_c = new Zend_Pdf_Element_Numeric($c);
        $this->_m = new Zend_Pdf_Element_Numeric($m);
        $this->_y = new Zend_Pdf_Element_Numeric($y);
        $this->_k = new Zend_Pdf_Element_Numeric($k);

        if ($this->_c->value < 0) { $this->_c->value = 0; }
        if ($this->_c->value > 1) { $this->_c->value = 1; }

        if ($this->_m->value < 0) { $this->_m->value = 0; }
        if ($this->_m->value > 1) { $this->_m->value = 1; }

        if ($this->_y->value < 0) { $this->_y->value = 0; }
        if ($this->_y->value > 1) { $this->_y->value = 1; }

        if ($this->_k->value < 0) { $this->_k->value = 0; }
        if ($this->_k->value > 1) { $this->_k->value = 1; }
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
}

