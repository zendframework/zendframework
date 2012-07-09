<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\BinaryParser\Font\OpenType;

use Zend\Pdf;
use Zend\Pdf\Exception;

/**
 * Parses an OpenType font file containing TrueType outlines.
 *
 * @package    Zend_PDF
 * @subpackage FileParser
 */
class TrueType extends AbstractOpenType
{
    /**** Public Interface ****/


    /* Concrete Class Implementation */

    /**
     * Verifies that the font file actually contains TrueType outlines.
     *
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function screen()
    {
        if ($this->_isScreened) {
            return;
        }

        parent::screen();

        switch ($this->_readScalerType()) {
            case 0x00010000:    // version 1.0 - Windows TrueType signature
                break;

            case 0x74727565:    // 'true' - Macintosh TrueType signature
                break;

            default:
                throw new Exception\UnrecognizedFontException('Not a TrueType font file');
        }

        $this->fontType = Pdf\Font::TYPE_TRUETYPE;
        $this->_isScreened = true;
    }

    /**
     * Reads and parses the TrueType font data from the file on disk.
     */
    public function parse()
    {
        if ($this->_isParsed) {
            return;
        }

        parent::parse();

        /* There is nothing additional to parse for TrueType fonts at this time.
         */

        $this->_isParsed = true;
    }
}
