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
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\InternalType\IndirectObjectReference;
use Zend\Pdf\PdfParser;

/**
 * PDF reference object context
 * Reference context is defined by PDF parser and PDF Refernce table
 *
 * @uses       Zend\Pdf\PdfParser\DataParser
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

