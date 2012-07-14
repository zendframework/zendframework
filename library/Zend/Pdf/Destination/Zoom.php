<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\Destination;

use Zend\Pdf;
use Zend\Pdf\Exception;
use Zend\Pdf\InternalType;

/**
 * \Zend\Pdf\Destination\Zoom explicit detination
 *
 * Destination array: [page /XYZ left top zoom]
 *
 * Display the page designated by page, with the coordinates (left, top) positioned
 * at the upper-left corner of the window and the contents of the page
 * magnified by the factor zoom. A null value for any of the parameters left, top,
 * or zoom specifies that the current value of that parameter is to be retained unchanged.
 * A zoom value of 0 has the same meaning as a null value.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Destination
 */
class Zoom extends AbstractExplicitDestination
{
    /**
     * Create destination object
     *
     * @param \Zend\Pdf\Page|integer $page  Page object or page number
     * @param float $left  Left edge of displayed page
     * @param float $top   Top edge of displayed page
     * @param float $zoom  Zoom factor
     * @return \Zend\Pdf\Destination\Zoom
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public static function create($page, $left = null, $top = null, $zoom = null)
    {
        $destinationArray = new InternalType\ArrayObject();

        if ($page instanceof Pdf\Page) {
            $destinationArray->items[] = $page->getPageDictionary();
        } elseif (is_integer($page)) {
            $destinationArray->items[] = new InternalType\NumericObject($page);
        } else {
            throw new Exception\InvalidArgumentException('$page parametr must be a \Zend\Pdf\Page object or a page number.');
        }

        $destinationArray->items[] = new InternalType\NameObject('XYZ');

        if ($left === null) {
            $destinationArray->items[] = new InternalType\NullObject();
        } else {
            $destinationArray->items[] = new InternalType\NumericObject($left);
        }

        if ($top === null) {
            $destinationArray->items[] = new InternalType\NullObject();
        } else {
            $destinationArray->items[] = new InternalType\NumericObject($top);
        }

        if ($zoom === null) {
            $destinationArray->items[] = new InternalType\NullObject();
        } else {
            $destinationArray->items[] = new InternalType\NumericObject($zoom);
        }

        return new self($destinationArray);
    }

    /**
     * Get left edge of the displayed page (null means viewer application 'current value')
     *
     * @return float
     */
    public function getLeftEdge()
    {
        return $this->_destinationArray->items[2]->value;
    }

    /**
     * Set left edge of the displayed page (null means viewer application 'current value')
     *
     * @param float $left
     * @return \Zend\Pdf\Action\Zoom
     */
    public function setLeftEdge($left)
    {
        if ($left === null) {
            $this->_destinationArray->items[2] = new InternalType\NullObject();
        } else {
            $this->_destinationArray->items[2] = new InternalType\NumericObject($left);
        }

        return $this;
    }

    /**
     * Get top edge of the displayed page (null means viewer application 'current value')
     *
     * @return float
     */
    public function getTopEdge()
    {
        return $this->_destinationArray->items[3]->value;
    }

    /**
     * Set top edge of the displayed page (null means viewer application 'current viewer')
     *
     * @param float $top
     * @return \Zend\Pdf\Action\Zoom
     */
    public function setTopEdge($top)
    {
        if ($top === null) {
            $this->_destinationArray->items[3] = new InternalType\NullObject();
        } else {
            $this->_destinationArray->items[3] = new InternalType\NumericObject($top);
        }

        return $this;
    }

    /**
     * Get ZoomFactor of the displayed page (null or 0 means viewer application 'current value')
     *
     * @return float
     */
    public function getZoomFactor()
    {
        return $this->_destinationArray->items[4]->value;
    }

    /**
     * Set ZoomFactor of the displayed page (null or 0 means viewer application 'current viewer')
     *
     * @param float $zoom
     * @return \Zend\Pdf\Action\Zoom
     */
    public function setZoomFactor($zoom)
    {
        if ($zoom === null) {
            $this->_destinationArray->items[4] = new InternalType\NullObject();
        } else {
            $this->_destinationArray->items[4] = new InternalType\NumericObject($zoom);
        }

        return $this;
    }
}
