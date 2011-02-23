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
 * @subpackage Zend_PDF_Font
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Cmap;
use Zend\Pdf\Exception;
use Zend\Pdf;

/**
 * Custom cmap type used for the Adobe Standard 14 PDF fonts.
 *
 * Just like {@link \Zend\Pdf\Cmap\ByteEncoding} except that the constructor
 * takes a predefined array of glyph numbers and can cover any Unicode character.
 *
 * @uses       \Zend\Pdf\Cmap\ByteEncoding
 * @uses       \Zend\Pdf\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Font
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
     * @throws \Zend\Pdf\Exception
     */
    public function __construct($cmapData)
    {
        if (! is_array($cmapData)) {
            throw new Exception\CorruptedFontException('Constructor parameter must be an array');
        }
        $this->_glyphIndexArray = $cmapData;
    }

}
