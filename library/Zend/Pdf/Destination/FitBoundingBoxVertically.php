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
 * \Zend\Pdf\Destination\FitBoundingBoxVertically explicit detination
 *
 * Destination array: [page /FitBV left]
 *
 * (PDF 1.1) Display the page designated by page, with the horizontal coordinate
 * left positioned at the left edge of the window and the contents of the page
 * magnified just enough to fit the entire height of its bounding box within the
 * window.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Destination
 */
class FitBoundingBoxVertically extends AbstractExplicitDestination
{
    /**
     * Create destination object
     *
     * @param \Zend\Pdf\Page|integer $page  Page object or page number
     * @param float $left  Left edge of displayed page
     * @return \Zend\Pdf\Destination\FitBoundingBoxVertically
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public static function create($page, $left)
    {
        $destinationArray = new InternalType\ArrayObject();

        if ($page instanceof Pdf\Page) {
            $destinationArray->items[] = $page->getPageDictionary();
        } elseif (is_integer($page)) {
            $destinationArray->items[] = new InternalType\NumericObject($page);
        } else {
            throw new Exception\InvalidArgumentException('$page parametr must be a \Zend\Pdf\Page object or a page number.');
        }

        $destinationArray->items[] = new InternalType\NameObject('FitBV');
        $destinationArray->items[] = new InternalType\NumericObject($left);

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
     * @return \Zend\Pdf\Action\FitBoundingBoxVertically
     */
    public function setLeftEdge($left)
    {
        $this->_destinationArray->items[2] = new InternalType\NumericObject($left);
        return $this;
    }
}
