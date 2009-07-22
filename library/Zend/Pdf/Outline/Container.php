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
 * @subpackage Actions
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Iteratable outlines container
 *
 * @todo Implement an ability to associate an outline item with a structure element (PDF 1.3 feature)
 *
 * @package    Zend_Pdf
 * @subpackage Outlines
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Pdf_Outline_Container implements RecursiveIterator, Countable
{
    /**
     * Array of outlines (array of Zend_Pdf_Outline objects)
     *
     * @var array
     */
    protected $_outlines = array();


    /**
     * Object constructor
     *
     * @param array $outlines
     */
    public function __construct(array $outlines)
    {
    	$this->_outlines = $outlines;
    }

	////////////////////////////////////////////////////////////////////////
	//  RecursiveIterator interface methods
	//////////////

	/**
     * Returns the child outline.
     *
     * @return Zend_Pdf_Outline|null
     */
    public function current()
    {
        return current($this->_outlines);
    }

    /**
     * Returns current iterator key
     *
     * @return integer
     */
    public function key()
    {
        return key($this->_outlines);
    }

    /**
     * Go to next child
     */
    public function next()
    {
        return next($this->_outlines);
    }

    /**
     * Rewind children
     */
    public function rewind()
    {
        return reset($this->_outlines);
    }

    /**
     * Check if current position is valid
     *
     * @return boolean
     */
    public function valid()
    {
        return current($this->_outlines) !== false;
    }

	/**
     * Returns the child outline.
     *
     * @return Zend_Pdf_Outline|null
     */
    public function getChildren()
    {
        return current($this->_outlines);
    }

    /**
     * Implements RecursiveIterator interface.
     *
     * @return bool  whether container has any pages
     */
    public function hasChildren()
    {
        return count($this->_outlines) > 0;
    }


    ////////////////////////////////////////////////////////////////////////
    //  Countable interface methods
    //////////////

    /**
     * count()
     *
     * @return int
     */
    public function count()
    {
        return count($this->_outlines);
    }
}
