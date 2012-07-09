<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\Resource\Font\CidFont;

use Zend\Pdf;
use Zend\Pdf\BinaryParser\Font\OpenType as OpenTypeFontParser;
use Zend\Pdf\InternalType;
use Zend\Pdf\Resource\Font as FontResource;

/**
 * Type 2 CIDFonts implementation
 *
 * For Type 2, the CIDFont program is actually a TrueType font program, which has
 * no native notion of CIDs. In a TrueType font program, glyph descriptions are
 * identified by glyph index values. Glyph indices are internal to the font and are not
 * defined consistently from one font to another. Instead, a TrueType font program
 * contains a 'cmap' table that provides mappings directly from character codes to
 * glyph indices for one or more predefined encodings.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Fonts
 */
class TrueType extends AbstractCidFont
{
    /**
     * Object constructor
     *
     * @todo Joing this class with \Zend\Pdf\Resource\Font\Simple\Parsed\TrueType
     *
     * @param \Zend\Pdf\BinaryParser\Font\OpenType\TrueType $fontParser Font parser
     *   object containing parsed TrueType file.
     * @param integer $embeddingOptions Options for font embedding.
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function __construct(OpenTypeFontParser\TrueType $fontParser, $embeddingOptions)
    {
        parent::__construct($fontParser, $embeddingOptions);

        $this->_fontType = Pdf\Font::TYPE_CIDFONT_TYPE_2;

        $this->_resource->Subtype  = new InternalType\NameObject('CIDFontType2');

        $fontDescriptor = FontResource\FontDescriptor::factory($this, $fontParser, $embeddingOptions);
        $this->_resource->FontDescriptor = $this->_objectFactory->newObject($fontDescriptor);

        /* Prepare CIDToGIDMap */
        // Initialize 128K string of null characters (65536 2 byte integers)
        $cidToGidMapData = str_repeat("\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00", 8192);
        // Fill the index
        $charGlyphs = $this->_cmap->getCoveredCharactersGlyphs();
        foreach ($charGlyphs as $charCode => $glyph) {
            $cidToGidMapData[$charCode*2    ] = chr($glyph >> 8);
            $cidToGidMapData[$charCode*2 + 1] = chr($glyph & 0xFF);
        }
        // Store CIDToGIDMap within compressed stream object
        $cidToGidMap = $this->_objectFactory->newStreamObject($cidToGidMapData);
        $cidToGidMap->dictionary->Filter = new InternalType\NameObject('FlateDecode');
        $this->_resource->CIDToGIDMap = $cidToGidMap;
    }

}
