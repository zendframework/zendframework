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
namespace Zend\Pdf\Trailer;
use Zend\Pdf;
use Zend\Pdf\InternalType;

/**
 * PDF file trailer generator (used for just created PDF)
 *
 * @uses       \Zend\Pdf\PdfDocument
 * @uses       \Zend\Pdf\Trailer\AbstractTrailer
 * @uses       \Zend\Pdf\InternalType\DirctionaryObject
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
