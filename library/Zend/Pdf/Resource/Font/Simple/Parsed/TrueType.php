<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\Resource\Font\Simple\Parsed;

use Zend\Pdf;
use Zend\Pdf\BinaryParser\Font\OpenType as OpenTypeFontParser;
use Zend\Pdf\InternalType;
use Zend\Pdf\Resource\Font as FontResource;

/**
 * TrueType fonts implementation
 *
 * Font objects should be normally be obtained from the factory methods
 * {@link \Zend\Pdf\Font::fontWithName} and {@link \Zend\Pdf\Font::fontWithPath}.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Fonts
 */
class TrueType extends AbstractParsed
{
    /**
     * Object constructor
     *
     * @param \Zend\Pdf\BinaryParser\Font\OpenType\TrueType $fontParser Font parser
     *   object containing parsed TrueType file.
     * @param integer $embeddingOptions Options for font embedding.
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function __construct(OpenTypeFontParser\TrueType $fontParser, $embeddingOptions)
    {
        parent::__construct($fontParser, $embeddingOptions);

        $this->_fontType = Pdf\Font::TYPE_TRUETYPE;

        $this->_resource->Subtype  = new InternalType\NameObject('TrueType');

        $fontDescriptor = FontResource\FontDescriptor::factory($this, $fontParser, $embeddingOptions);
        $this->_resource->FontDescriptor = $this->_objectFactory->newObject($fontDescriptor);
    }
}
