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
 * @subpackage Zend_PDF_Destination
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\Destination;
use Zend\PDF\InternalType;
use Zend\PDF;

/**
 * Zend_PDF_Destination_FitRectangle explicit detination
 *
 * Destination array: [page /FitR left bottom right top]
 *
 * Display the page designated by page, with its contents magnified just enough
 * to fit the rectangle specified by the coordinates left, bottom, right, and top
 * entirely within the window both horizontally and vertically. If the required
 * horizontal and vertical magnification factors are different, use the smaller of
 * the two, centering the rectangle within the window in the other dimension.
 *
 * @uses       \Zend\PDF\Destination\Explicit
 * @uses       \Zend\PDF\InternalType\ArrayObject
 * @uses       \Zend\PDF\InternalType\NameObject
 * @uses       \Zend\PDF\InternalType\NumericObject
 * @uses       \Zend\PDF\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Destination
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FitRectangle extends Explicit
{
    /**
     * Create destination object
     *
     * @param \Zend\PDF\Page|integer $page  Page object or page number
     * @param float $left    Left edge of displayed page
     * @param float $bottom  Bottom edge of displayed page
     * @param float $right   Right edge of displayed page
     * @param float $top     Top edge of displayed page
     * @return \Zend\PDF\Destination\FitRectangle
     * @throws \Zend\PDF\Exception
     */
    public static function create($page, $left, $bottom, $right, $top)
    {
        $destinationArray = new InternalType\ArrayObject();

        if ($page instanceof PDF\Page) {
            $destinationArray->items[] = $page->getPageDictionary();
        } else if (is_integer($page)) {
            $destinationArray->items[] = new InternalType\NumericObject($page);
        } else {
            throw new PDF\Exception('Page entry must be a Zend_PDF_Page object or a page number.');
        }

        $destinationArray->items[] = new InternalType\NameObject('FitR');
        $destinationArray->items[] = new InternalType\NumericObject($left);
        $destinationArray->items[] = new InternalType\NumericObject($bottom);
        $destinationArray->items[] = new InternalType\NumericObject($right);
        $destinationArray->items[] = new InternalType\NumericObject($top);

        return new self($destinationArray);
    }

    /**
     * Get left edge of the displayed page
     *
     * @return float
     */
    public function getLeftEdge()
    {
        return $this->_destinationArray->items[2]->value;
    }

    /**
     * Set left edge of the displayed page
     *
     * @param float $left
     * @return \Zend\PDF\Destination\FitRectangle
     */
    public function setLeftEdge($left)
    {
        $this->_destinationArray->items[2] = new InternalType\NumericObject($left);
        return $this;
    }

    /**
     * Get bottom edge of the displayed page
     *
     * @return float
     */
    public function getBottomEdge()
    {
        return $this->_destinationArray->items[3]->value;
    }

    /**
     * Set bottom edge of the displayed page
     *
     * @param float $bottom
     * @return \Zend\PDF\Destination\FitRectangle
     */
    public function setBottomEdge($bottom)
    {
        $this->_destinationArray->items[3] = new InternalType\NumericObject($bottom);
        return $this;
    }

    /**
     * Get right edge of the displayed page
     *
     * @return float
     */
    public function getRightEdge()
    {
        return $this->_destinationArray->items[4]->value;
    }

    /**
     * Set right edge of the displayed page
     *
     * @param float $right
     * @return \Zend\PDF\Destination\FitRectangle
     */
    public function setRightEdge($right)
    {
        $this->_destinationArray->items[4] = new InternalType\NumericObject($right);
        return $this;
    }

    /**
     * Get top edge of the displayed page
     *
     * @return float
     */
    public function getTopEdge()
    {
        return $this->_destinationArray->items[5]->value;
    }

    /**
     * Set top edge of the displayed page
     *
     * @param float $top
     * @return \Zend\PDF\Destination\FitRectangle
     */
    public function setTopEdge($top)
    {
        $this->_destinationArray->items[5] = new InternalType\NumericObject($top);
        return $this;
    }
}
