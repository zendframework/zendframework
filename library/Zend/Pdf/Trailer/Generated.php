<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\Trailer;

use Zend\Pdf;
use Zend\Pdf\InternalType;

/**
 * PDF file trailer generator (used for just created PDF)
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class Generated extends AbstractTrailer
{
    /**
     * Object constructor
     *
     * @param \Zend\Pdf\InternalType\DictionaryObject $dict
     */
    public function __construct(InternalType\DictionaryObject $dict)
    {
        parent::__construct($dict);
    }

    /**
     * Get length of source PDF
     *
     * @return string
     */
    public function getPDFLength()
    {
        return strlen(Pdf\PdfDocument::PDF_HEADER);
    }

    /**
     * Get PDF String
     *
     * @return string
     */
    public function getPDFString()
    {
        return Pdf\PdfDocument::PDF_HEADER;
    }

    /**
     * Get header of free objects list
     * Returns object number of last free object
     *
     * @return integer
     */
    public function getLastFreeObject()
    {
        return 0;
    }
}
