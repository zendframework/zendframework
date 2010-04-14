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
 * @subpackage Zend_PDF_Fonts
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\Resource\Font\Simple\Parsed;
use Zend\PDF;
use Zend\PDF\InternalType;
use Zend\PDF\Resource\Font as FontResource;
use Zend\PDF\BinaryParser\Font\OpenType as OpenTypeFontParser;

/**
 * TrueType fonts implementation
 *
 * Font objects should be normally be obtained from the factory methods
 * {@link \Zend\PDF\Font::fontWithName} and {@link \Zend\PDF\Font::fontWithPath}.
 *
 * @uses       \Zend\PDF\InternalType
 * @uses       \Zend\PDF\Font
 * @uses       \Zend\PDF\Resource\Font\FontDescriptor
 * @uses       \Zend\PDF\Resource\Font\Simple\Parsed\AbstractParsed
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Fonts
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TrueType extends AbstractParsed
{
    /**
     * Object constructor
     *
     * @param \Zend\PDF\BinaryParser\Font\OpenType\TrueType $fontParser Font parser
     *   object containing parsed TrueType file.
     * @param integer $embeddingOptions Options for font embedding.
     * @throws \Zend\PDF\Exception
     */
    public function __construct(OpenTypeFontParser\TrueType $fontParser, $embeddingOptions)
    {
        parent::__construct($fontParser, $embeddingOptions);

        $this->_fontType = PDF\Font::TYPE_TRUETYPE;

        $this->_resource->Subtype  = new InternalType\NameObject('TrueType');

        $fontDescriptor = FontResource\FontDescriptor::factory($this, $fontParser, $embeddingOptions);
        $this->_resource->FontDescriptor = $this->_objectFactory->newObject($fontDescriptor);
    }
}
