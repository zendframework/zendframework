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

use Zend\Pdf\PdfParser;

/**
 * PDF reference object context
 * Reference context is defined by PDF parser and PDF Refernce table
 *
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class Context
{
    /**
     * PDF parser object.
     *
     * @var \Zend\Pdf\PdfParser\DataParser
     */
    private $_stringParser;

    /**
     * Reference table
     *
     * @var \Zend\Pdf\InternalType\IndirectObjectReference\ReferenceTable
     */
    private $_refTable;

    /**
     * Object constructor
     *
     * @param \Zend\Pdf\PdfParser\DataParser $parser
     * @param \Zend\Pdf\InternalType\IndirectObjectReference\ReferenceTable $refTable
     */
    public function __construct(PdfParser\DataParser $parser, ReferenceTable $refTable)
    {
        $this->_stringParser = $parser;
        $this->_refTable     = $refTable;
    }


    /**
     * Context parser
     *
     * @return \Zend\Pdf\PdfParser\DataParser
     */
    public function getParser()
    {
        return $this->_stringParser;
    }


    /**
     * Context reference table
     *
     * @return \Zend\Pdf\InternalType\IndirectObjectReference\ReferenceTable
     */
    public function getRefTable()
    {
        return $this->_refTable;
    }
}
