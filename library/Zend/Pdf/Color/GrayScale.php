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
 * @subpackage Zend_PDF_Color
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Color;

use Zend\Pdf\Color,
    Zend\Pdf\InternalType;

/**
 * GrayScale color implementation
 *
 * @uses       \Zend\Pdf\Color
 * @uses       \Zend\Pdf\InternalType\NumericObject
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Color
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GrayScale implements Color
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

