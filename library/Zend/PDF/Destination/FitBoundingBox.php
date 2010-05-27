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
 * \Zend\PDF\Destination\FitBoundingBox explicit detination
 *
 * Destination array: [page /FitB]
 *
 * (PDF 1.1) Display the page designated by page, with its contents magnified
 * just enough to fit its bounding box entirely within the window both horizontally
 * and vertically. If the required horizontal and vertical magnification
 * factors are different, use the smaller of the two, centering the bounding box
 * within the window in the other dimension.
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
class FitBoundingBox extends Explicit
{
    /**
     * Create destination object
     *
     * @param \Zend\PDF\Page|integer $page  Page object or page number
     * @return \Zend\PDF\Destination\FitBoundingBox
     * @throws \Zend\PDF\Exception
     */
    public static function create($page)
    {
        $destinationArray = new InternalType\ArrayObject();

        if ($page instanceof PDF\Page) {
            $destinationArray->items[] = $page->getPageDictionary();
        } else if (is_integer($page)) {
            $destinationArray->items[] = new InternalType\NumericObject($page);
        } else {
            throw new PDF\Exception('Page entry must be a Zend_PDF_Page object or a page number.');
        }

        $destinationArray->items[] = new InternalType\NameObject('FitB');

        return new self($destinationArray);
    }
}
