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
 * \Zend\PDF\Destination\FitVertically explicit detination
 *
 * Destination array: [page /FitV left]
 *
 * Display the page designated by page, with the horizontal coordinate left positioned
 * at the left edge of the window and the contents of the page magnified
 * just enough to fit the entire height of the page within the window.
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
class FitVertically extends Explicit
{
    /**
     * Create destination object
     *
     * @param \Zend\PDF\Page|integer $page  Page object or page number
     * @param float $left  Left edge of displayed page
     * @return \Zend\PDF\Destination\FitVertically
     * @throws \Zend\PDF\Exception
     */
    public static function create($page, $left)
    {
        $destinationArray = new InternalType\ArrayObject();

        if ($page instanceof PDF\Page) {
            $destinationArray->items[] = $page->getPageDictionary();
        } else if (is_integer($page)) {
            $destinationArray->items[] = new InternalType\NumericObject($page);
        } else {
            throw new PDF\Exception('Page entry must be a Zend_PDF_Page object or page number.');
        }

        $destinationArray->items[] = new InternalType\NameObject('FitV');
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
     * @return Zend_PDF_Action_FitVertically
     */
    public function setLeftEdge($left)
    {
        $this->_destinationArray->items[2] = new InternalType\NumericObject($left);

        return $this;
    }
}
