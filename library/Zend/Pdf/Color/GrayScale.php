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
 * GrayScale color implementation
 *
 * @category   Zend
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf_Color_GrayScale extends Zend_Pdf_Color
{
    /**
     * GrayLevel.
     * 0.0 (black) - 1.0 (white)
     *
     * @var Zend_Pdf_Element_Numeric
     */
    private $_grayLevel;

    /**
     * Object constructor
     *
     * @param float $grayLevel
     */
    public function __construct($grayLevel)
    {
        $this->_grayLevel = new Zend_Pdf_Element_Numeric($grayLevel);

        if ($this->_grayLevel->value < 0) {
            $this->_grayLevel->value = 0;
        }

        if ($this->_grayLevel->value > 1) {
            $this->_grayLevel->value = 1;
        }
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
}

