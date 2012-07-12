<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\Cmap;

use Zend\Pdf;
use Zend\Pdf\Exception;

/**
 * Custom cmap type used for the Adobe Standard 14 PDF fonts.
 *
 * Just like {@link \Zend\Pdf\Cmap\ByteEncoding} except that the constructor
 * takes a predefined array of glyph numbers and can cover any Unicode character.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Font
 */
class StaticByteEncoding extends ByteEncoding
{
    /**** Public Interface ****/


    /* Object Lifecycle */

    /**
     * Object constructor
     *
     * @param array $cmapData Array whose keys are Unicode character codes and
     *   values are glyph numbers.
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function __construct($cmapData)
    {
        if (! is_array($cmapData)) {
            throw new Exception\CorruptedFontException('Constructor parameter must be an array');
        }
        $this->_glyphIndexArray = $cmapData;
    }

}
