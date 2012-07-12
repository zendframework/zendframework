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
 * \Zend\Pdf\Destination\FitBoundingBox explicit detination
 *
 * Destination array: [page /FitB]
 *
 * (PDF 1.1) Display the page designated by page, with its contents magnified
 * just enough to fit its bounding box entirely within the window both horizontally
 * and vertically. If the required horizontal and vertical magnification
 * factors are different, use the smaller of the two, centering the bounding box
 * within the window in the other dimension.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Destination
 */
class FitBoundingBox extends AbstractExplicitDestination
{
    /**
     * Create destination object
     *
     * @param \Zend\Pdf\Page|integer $page  Page object or page number
     * @return \Zend\Pdf\Destination\FitBoundingBox
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public static function create($page)
    {
        $destinationArray = new InternalType\ArrayObject();

        if ($page instanceof Pdf\Page) {
            $destinationArray->items[] = $page->getPageDictionary();
        } elseif (is_integer($page)) {
            $destinationArray->items[] = new InternalType\NumericObject($page);
        } else {
            throw new Exception\InvalidArgumentException('$page parametr must be a \Zend\Pdf\Page object or a page number.');
        }

        $destinationArray->items[] = new InternalType\NameObject('FitB');

        return new self($destinationArray);
    }
}
