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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Destination;
use Zend\Pdf\Exception;
use Zend\Pdf\InternalType;
use Zend\Pdf;

/**
 * \Zend\Pdf\Destination\FitVertically explicit detination
 *
 * Destination array: [page /FitV left]
 *
 * Display the page designated by page, with the horizontal coordinate left positioned
 * at the left edge of the window and the contents of the page magnified
 * just enough to fit the entire height of the page within the window.
 *
 * @uses       \Zend\Pdf\Destination\Explicit
 * @uses       \Zend\Pdf\InternalType\ArrayObject
 * @uses       \Zend\Pdf\InternalType\NameObject
 * @uses       \Zend\Pdf\InternalType\NumericObject
 * @uses       \Zend\Pdf\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Destination
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FitVertically extends Explicit
{
    /**
     * Create destination object
     *
     * @param \Zend\Pdf\Page|integer $page  Page object or page number
     * @param float $left  Left edge of displayed page
     * @return \Zend\Pdf\Destination\FitVertically
     * @throws \Zend\Pdf\Exception
     */
    public static function create($page, $left)
    {
        $destinationArray = new InternalType\ArrayObject();

        if ($page instanceof Pdf\Page) {
            $destinationArray->items[] = $page->getPageDictionary();
        } else if (is_integer($page)) {
            $destinationArray->items[] = new InternalType\NumericObject($page);
        } else {
            throw new Exception\InvalidArgumentException('$page parametr must be a \Zend\Pdf\Page object or a page number.');
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
     * @return \Zend\Pdf\Action\FitVertically
     */
    public function setLeftEdge($left)
    {
        $this->_destinationArray->items[2] = new InternalType\NumericObject($left);

        return $this;
    }
}
