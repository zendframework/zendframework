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
 * @subpackage Destination
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Pdf_ElementFactory */
require_once 'Zend/Pdf/ElementFactory.php';

/** Zend_Pdf_Page */
require_once 'Zend/Pdf/Page.php';

/** Zend_Pdf_Target */
require_once 'Zend/Pdf/Target.php';


/**
 * Abstract PDF explicit destination representation class
 *
 * @package    Zend_Pdf
 * @subpackage Destination
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Pdf_Destination extends Zend_Pdf_Target
{
	/**
	 * Destination description array
	 *
	 * @var Zend_Pdf_Element_Array
	 */
	protected $_destinationArray;

	/**
	 * True if it's a remote destination
	 *
	 * @var boolean
	 */
	protected $_isRemote;

	/**
	 * Destination object constructor
	 *
	 * @param Zend_Pdf_Element $destinationArray
	 * @throws Zend_Pdf_Exception
	 */
	public function __construct(Zend_Pdf_Element $destinationArray)
	{
        if ($destinationArray->getType() != Zend_Pdf_Element::TYPE_ARRAY) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Zend_Pdf_Exception('$destinationArray mast be direct or indirect array object.');
        }

        $this->_destinationArray = $destinationArray;

        if (count($this->_destinationArray->items) == 0) {
        	require_once 'Zend/Pdf/Exception.php';
        	throw new Zend_Pdf_Exception('Destination array must contain a page reference.');
        }
        if (count($this->_destinationArray->items) == 1) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Zend_Pdf_Exception('Destination array must contain a destination type name.');
        }

        switch ($this->_destinationArray->items[0]->getType()) {
        	case Zend_Pdf_Element::TYPE_NUMERIC:
        		$this->_isRemote = true;
        		break;

            case Zend_Pdf_Element::TYPE_DICTIONARY:
            	$this->_isRemote = false;
                break;

            default:
            	require_once 'Zend/Pdf/Exception.php';
                throw new Zend_Pdf_Exception('Destination target must be a page number or page dictionary object.');
            	break;
        }
	}


    public static function load(Zend_Pdf_Element $destinationArray)
    {
        if ($destinationArray->getType() != Zend_Pdf_Element::TYPE_ARRAY) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Zend_Pdf_Exception('$destinationArray mast be direct or indirect array object.');
        }
        if (count($destinationArray->items) == 0) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Zend_Pdf_Exception('Destination array must contain a page reference.');
        }
        if (count($destinationArray->items) == 1) {
            require_once 'Zend/Pdf/Exception.php';
            throw new Zend_Pdf_Exception('Destination array must contain a destination type name.');
        }

        switch ($destinationArray->items[1]->value) {
        	case 'XYZ':
        		require_once 'Zend/Pdf/Destination/Zoom.php';
                return new Zend_Pdf_Destination_Zoom($destinationArray);
        		break;

            case 'Fit':
                require_once 'Zend/Pdf/Destination/Fit.php';
                return new Zend_Pdf_Destination_Fit($destinationArray);
                break;

            case 'FitH':
                require_once 'Zend/Pdf/Destination/FitHorizontally.php';
                return new Zend_Pdf_Destination_FitHorizontally($destinationArray);
                break;

            case 'FitV':
                require_once 'Zend/Pdf/Destination/FitVertically.php';
                return new Zend_Pdf_Destination_FitVertically($destinationArray);
                break;

            case 'FitR':
                require_once 'Zend/Pdf/Destination/FitRectangle.php';
                return new Zend_Pdf_Destination_FitRectangle($destinationArray);
                break;

            case 'FitB':
                require_once 'Zend/Pdf/Destination/FitBoundingBox.php';
                return new Zend_Pdf_Destination_FitBoundingBox($destinationArray);
                break;

            case 'FitBH':
                require_once 'Zend/Pdf/Destination/FitBoundingBoxHorizontally.php';
                return new Zend_Pdf_Destination_FitBoundingBoxHorizontally($destinationArray);
                break;

            case 'FitBV':
                require_once 'Zend/Pdf/Destination/FitBoundingBoxVertically.php';
                return new Zend_Pdf_Destination_FitBoundingBoxVertically($destinationArray);
                break;

            default:
                require_once 'Zend/Pdf/Destination/Unknown.php';
                return new Zend_Pdf_Destination_Unknown($destinationArray);
                break;
        }
    }

    /**
     * Returns true if it's a remote destination
     *
     * @return boolean
     */
    public function isRemote()
    {
    	return $this->_isRemote;
    }

    /**
     * Returns destination target
     *
     * Returns page number for remote destinations and
     * page dictionary object reference otherwise
     *
     * @internal
     * @return integer|Zend_Pdf_Element_Dictionary
     */
    public function getTarget()
    {
        return $this->_destinationArray->items[0];
    }

    /**
     * Get resource
     *
     * @internal
     * @return Zend_Pdf_Element
     */
    public function getResource()
    {
        return $this->_destinationArray;
    }
}
