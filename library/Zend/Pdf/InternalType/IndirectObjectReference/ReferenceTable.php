<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\InternalType\IndirectObjectReference;

use Zend\Pdf;
use Zend\Pdf\Exception;

/**
 * PDF file reference table
 *
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class ReferenceTable
{
    /**
     * Parent reference table
     *
     * @var \Zend\Pdf\InternalType\IndirectObjectReference\ReferenceTable
     */
    private $_parent;

    /**
     * Free entries
     * 'reference' => next free object number
     *
     * @var array
     */
    private $_free;

    /**
     * Generation numbers for free objects.
     * Array: objNum => nextGeneration
     *
     * @var array
     */
    private $_generations;

    /**
     * In use entries
     * 'reference' => offset
     *
     * @var array
     */
    private $_inuse;

    /**
     * Generation numbers for free objects.
     * Array: objNum => objGeneration
     *
     * @var array
     */
    private $_usedObjects;



    /**
     * Object constructor
     */
    public function  __construct()
    {
        $this->_parent = null;
        $this->_free   = array();  $this->_generations = array();
        $this->_inuse  = array();  $this->_usedObjects = array();
    }


    /**
     * Add reference to the reference table
     *
     * @param string $ref
     * @param integer $offset
     * @param boolean $inuse
     */
    public function addReference($ref, $offset, $inuse = true)
    {
        $refElements = explode(' ', $ref);
        if (!is_numeric($refElements[0]) || !is_numeric($refElements[1]) || $refElements[2] != 'R') {
            throw new Exception\InvalidArgumentException("Incorrect reference: '$ref'");
        }
        $objNum = (int)$refElements[0];
        $genNum = (int)$refElements[1];

        if ($inuse) {
            $this->_inuse[$ref]          = $offset;
            $this->_usedObjects[$objNum] = $objNum;
        } else {
            $this->_free[$ref]           = $offset;
            $this->_generations[$objNum] = $genNum;
        }
    }


    /**
     * Set parent reference table
     *
     * @param \Zend\Pdf\InternalType\IndirectObjectReference\ReferenceTable $parent
     */
    public function setParent(self $parent)
    {
        $this->_parent = $parent;
    }


    /**
     * Get object offset
     *
     * @param string $ref
     * @return integer
     */
    public function getOffset($ref)
    {
        if (isset($this->_inuse[$ref])) {
            return $this->_inuse[$ref];
        }

        if (isset($this->_free[$ref])) {
            return null;
        }

        if (isset($this->_parent)) {
            return $this->_parent->getOffset($ref);
        }

        return null;
    }


    /**
     * Get next object from a list of free objects.
     *
     * @param string $ref
     * @return integer
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function getNextFree($ref)
    {
        if (isset($this->_inuse[$ref])) {
            throw new Exception\CorruptedPdfException('Object is not free');
        }

        if (isset($this->_free[$ref])) {
            return $this->_free[$ref];
        }

        if (isset($this->_parent)) {
            return $this->_parent->getNextFree($ref);
        }

        throw new Exception\CorruptedPdfException('Object not found.');
    }


    /**
     * Get next generation number for free object
     *
     * @param integer $objNum
     * @return unknown
     */
    public function getNewGeneration($objNum)
    {
        if (isset($this->_usedObjects[$objNum])) {
            throw new Exception\CorruptedPdfException('Object is not free');
        }

        if (isset($this->_generations[$objNum])) {
            return $this->_generations[$objNum];
        }

        if (isset($this->_parent)) {
            return $this->_parent->getNewGeneration($objNum);
        }

        throw new Exception\CorruptedPdfException('Object not found.');
    }
}
