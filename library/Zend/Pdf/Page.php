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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf;

/**
 * PDF Page
 *
 * @uses       \Zend\Pdf\PdfDocument
 * @uses       \Zend\Pdf\ObjectFactory
 * @uses       \Zend\Pdf\InternalType
 * @uses       \Zend\Pdf\Exception
 * @uses       \Zend\Pdf\Resource\Font
 * @package    Zend_PDF
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
use Zend\Pdf\Resource\Font;

use Zend\Acl;

use Zend\Pdf\Exception;

use Zend\Pdf\InternalType;

class Page
{
    /**** Class Constants ****/


    /* Page Sizes */

    /**
     * Size representing an A4 page in portrait (tall) orientation.
     */
    const SIZE_A4                = '595:842:';

    /**
     * Size representing an A4 page in landscape (wide) orientation.
     */
    const SIZE_A4_LANDSCAPE      = '842:595:';

    /**
     * Size representing a US Letter page in portrait (tall) orientation.
     */
    const SIZE_LETTER            = '612:792:';

    /**
     * Size representing a US Letter page in landscape (wide) orientation.
     */
    const SIZE_LETTER_LANDSCAPE  = '792:612:';


    /* Shape Drawing */

    /**
     * Stroke the path only. Do not fill.
     */
    const SHAPE_DRAW_STROKE      = 0;

    /**
     * Fill the path only. Do not stroke.
     */
    const SHAPE_DRAW_FILL        = 1;

    /**
     * Fill and stroke the path.
     */
    const SHAPE_DRAW_FILL_AND_STROKE = 2;


    /* Shape Filling Methods */

    /**
     * Fill the path using the non-zero winding rule.
     */
    const FILL_METHOD_NON_ZERO_WINDING = 0;

    /**
     * Fill the path using the even-odd rule.
     */
    const FILL_METHOD_EVEN_ODD        = 1;


    /* Line Dash Types */

    /**
     * Solid line dash.
     */
    const LINE_DASHING_SOLID = 0;



    /**
     * Page dictionary (refers to an inderect \Zend\Pdf\InternalType\DictionaryObject object).
     *
     * @var  \Zend\Pdf\InternalType\DictionaryObject
     *     | \Zend\Pdf\InternalType\IndirectObject
     *     | \Zend\Pdf\InternalType\IndirectObjectReference
     */
    protected $_pageDictionary;

    /**
     * PDF objects factory.
     *
     * @var \Zend\Pdf\ObjectFactory
     */
    protected $_objFactory = null;

    /**
     * Flag which signals, that page is created separately from any PDF document or
     * attached to anyone.
     *
     * @var boolean
     */
    protected $_attached;

    /**
     * Stream of the drawing instructions.
     *
     * @var string
     */
    protected $_contents = '';

    /**
     * Current style
     *
     * @var \Zend\Pdf\Style
     */
    protected $_style = null;

    /**
     * Counter for the "Save" operations
     *
     * @var integer
     */
    protected $_saveCount = 0;

    /**
     * Safe Graphics State semafore
     *
     * If it's false, than we can't be sure Graphics State is restored withing
     * context of previous contents stream (ex. drawing coordinate system may be rotated).
     * We should encompass existing content with save/restore GS operators
     *
     * @var boolean
     */
    protected $_safeGS;

    /**
     * Current font
     *
     * @var \Zend\Pdf\Resource\Font\AbstractFont
     */
    protected $_font = null;

    /**
     * Current font size
     *
     * @var float
     */
    protected $_fontSize;

    /**
     * Object constructor.
     * Constructor signatures:
     *
     * 1. Load PDF page from a parsed PDF file.
     *    Object factory is created by PDF parser.
     * ---------------------------------------------------------
     * new \Zend\Pdf\Page(\Zend\Pdf\InternalType\DictionaryObject $pageDict,
     *                    \Zend\Pdf\ObjectFactory $factory);
     * ---------------------------------------------------------
     *
     * 2. Make a copy of the PDF page.
     *    New page is created in the same context as source page. Object factory is shared.
     *    Thus it will be attached to the document, but need to be placed into Zend_Pdf::$pages array
     *    to be included into output.
     * ---------------------------------------------------------
     * new \Zend\Pdf\Page(\Zend\Pdf\Page $page);
     * ---------------------------------------------------------
     *
     * 3. Create new page with a specified pagesize.
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new \Zend\Pdf\Page(string $pagesize, \Zend\Pdf\ObjectFactory $factory = null);
     * ---------------------------------------------------------
     *
     * 4. Create new page with a specified pagesize (in default user space units).
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new \Zend\Pdf\Page(numeric $width, numeric $height,
     *                    \Zend\Pdf\ObjectFactory $factory = null);
     * ---------------------------------------------------------
     *
     *
     * @param mixed $param1
     * @param mixed $param2
     * @param mixed $param3
     * @throws \Zend\Pdf\Exception
     */
    public function __construct($param1, $param2 = null, $param3 = null)
    {
        if (($param1 instanceof InternalType\IndirectObjectReference ||
             $param1 instanceof InternalType\IndirectObject
            ) &&
            $param1->getType() == InternalType\AbstractTypeObject::TYPE_DICTIONARY &&
            $param2 instanceof ObjectFactory &&
            $param3 === null
           ) {
            switch ($param1->getType()) {
                case InternalType\AbstractTypeObject::TYPE_DICTIONARY:
                    $this->_pageDictionary = $param1;
                    $this->_objFactory     = $param2;
                    $this->_attached       = true;
                    $this->_safeGS         = false;
                    return;
                    break;

                case InternalType\AbstractTypeObject::TYPE_NULL:
                    $this->_objFactory = $param2;
                    $pageWidth = $pageHeight = 0;
                    break;

                default:
                    throw new Exception\CorruptedPdfException('Unrecognized object type.');
                    break;

            }
        } else if ($param1 instanceof Page && $param2 === null && $param3 === null) {
            // Clone existing page.
            // Let already existing content and resources to be shared between pages
            // We don't give existing content modification functionality, so we don't need "deep copy"
            $this->_objFactory = $param1->_objFactory;
            $this->_attached   = &$param1->_attached;
            $this->_safeGS     = false;

            $this->_pageDictionary = $this->_objFactory->newObject(new InternalType\DictionaryObject());

            foreach ($param1->_pageDictionary->getKeys() as $key) {
                if ($key == 'Contents') {
                    // Clone Contents property

                    $this->_pageDictionary->Contents = new InternalType\ArrayObject();

                    if ($param1->_pageDictionary->Contents->getType() != InternalType\AbstractTypeObject::TYPE_ARRAY) {
                        // Prepare array of content streams and add existing stream
                        $this->_pageDictionary->Contents->items[] = $param1->_pageDictionary->Contents;
                    } else {
                        // Clone array of the content streams
                        foreach ($param1->_pageDictionary->Contents->items as $srcContentStream) {
                            $this->_pageDictionary->Contents->items[] = $srcContentStream;
                        }
                    }
                } else {
                    $this->_pageDictionary->$key = $param1->_pageDictionary->$key;
                }
            }

            return;
        } else if (is_string($param1) &&
                   ($param2 === null || $param2 instanceof ObjectFactory) &&
                   $param3 === null) {
            if ($param2 !== null) {
                $this->_objFactory = $param2;
            } else {
                $this->_objFactory = ObjectFactory::createFactory(1);
            }
            $this->_attached   = false;
            $this->_safeGS     = true; /** New page created. That's users App responsibility to track GS changes */

            switch (strtolower($param1)) {
                case 'a4':
                    $param1 = self::SIZE_A4;
                    break;
                case 'a4-landscape':
                    $param1 = self::SIZE_A4_LANDSCAPE;
                    break;
                case 'letter':
                    $param1 = self::SIZE_LETTER;
                    break;
                case 'letter-landscape':
                    $param1 = self::SIZE_LETTER_LANDSCAPE;
                    break;
                default:
                    // should be in "x:y" or "x:y:" form
            }

            $pageDim = explode(':', $param1);
            if(count($pageDim) == 2  ||  count($pageDim) == 3) {
                $pageWidth  = $pageDim[0];
                $pageHeight = $pageDim[1];
            } else {
                /**
                 * @todo support of user defined pagesize notations, like:
                 *       "210x297mm", "595x842", "8.5x11in", "612x792"
                 */
                throw new Exception\Exception\InvalidArgumentException('Wrong pagesize notation.');
            }
            /**
             * @todo support of pagesize recalculation to "default user space units"
             */

        } else if (is_numeric($param1) && is_numeric($param2) &&
                   ($param3 === null || $param3 instanceof ObjectFactory)) {
            if ($param3 !== null) {
                $this->_objFactory = $param3;
            } else {
                $this->_objFactory = ObjectFactory::createFactory(1);
            }

            $this->_attached = false;
            $this->_safeGS   = true; /** New page created. That's users App responsibility to track GS changes */
            $pageWidth  = $param1;
            $pageHeight = $param2;

        } else {
            throw new Exception\BadMethodCallException('Unrecognized method signature, wrong number of arguments or wrong argument types.');
        }

        $this->_pageDictionary = $this->_objFactory->newObject(new InternalType\DictionaryObject());
        $this->_pageDictionary->Type         = new InternalType\NameObject('Page');
        $this->_pageDictionary->LastModified = new InternalType\StringObject(PdfDocument::pdfDate());
        $this->_pageDictionary->Resources    = new InternalType\DictionaryObject();
        $this->_pageDictionary->MediaBox     = new InternalType\ArrayObject();
        $this->_pageDictionary->MediaBox->items[] = new InternalType\NumericObject(0);
        $this->_pageDictionary->MediaBox->items[] = new InternalType\NumericObject(0);
        $this->_pageDictionary->MediaBox->items[] = new InternalType\NumericObject($pageWidth);
        $this->_pageDictionary->MediaBox->items[] = new InternalType\NumericObject($pageHeight);
        $this->_pageDictionary->Contents     = new InternalType\ArrayObject();
    }


    /**
     * Attach resource to the page
     *
     * @param string $type
     * @param \Zend\Pdf\Resource\AbstractResource $resource
     * @return string
     */
    protected function _attachResource($type, Resource\AbstractResource $resource)
    {
        // Check that Resources dictionary contains appropriate resource set
        if ($this->_pageDictionary->Resources->$type === null) {
            $this->_pageDictionary->Resources->touch();
            $this->_pageDictionary->Resources->$type = new InternalType\DictionaryObject();
        } else {
            $this->_pageDictionary->Resources->$type->touch();
        }

        // Check, that resource is already attached to resource set.
        $resObject = $resource->getResource();
        foreach ($this->_pageDictionary->Resources->$type->getKeys() as $ResID) {
            if ($this->_pageDictionary->Resources->$type->$ResID === $resObject) {
                return $ResID;
            }
        }

        $idCounter = 1;
        do {
            $newResName = $type[0] . $idCounter++;
        } while ($this->_pageDictionary->Resources->$type->$newResName !== null);

        $this->_pageDictionary->Resources->$type->$newResName = $resObject;
        $this->_objFactory->attach($resource->getFactory());

        return $newResName;
    }

    /**
     * Add procedureSet to the Page description
     *
     * @param string $procSetName
     */
    protected function _addProcSet($procSetName)
    {
        // Check that Resources dictionary contains ProcSet entry
        if ($this->_pageDictionary->Resources->ProcSet === null) {
            $this->_pageDictionary->Resources->touch();
            $this->_pageDictionary->Resources->ProcSet = new InternalType\ArrayObject();
        } else {
            $this->_pageDictionary->Resources->ProcSet->touch();
        }

        foreach ($this->_pageDictionary->Resources->ProcSet->items as $procSetEntry) {
            if ($procSetEntry->value == $procSetName) {
                // Procset is already included into a ProcSet array
                return;
            }
        }

        $this->_pageDictionary->Resources->ProcSet->items[] = new InternalType\NameObject($procSetName);
    }

    /**
     * Clone page, extract it and dependent objects from the current document,
     * so it can be used within other docs.
     */
    public function __clone()
    {
        $factory = ObjectFactory::createFactory(1);
        $processed = array();

        // Clone dictionary object.
        // Do it explicitly to prevent sharing page attributes between different
        // results of clonePage() operation (other resources are still shared)
        $dictionary = new InternalType\DictionaryObject();
        foreach ($this->_pageDictionary->getKeys() as $key) {
            $dictionary->$key = $this->_pageDictionary->$key->makeClone($factory,
                                                                        $processed,
                                                                        InternalType\AbstractTypeObject::CLONE_MODE_SKIP_PAGES);
        }

        $this->_pageDictionary = $factory->newObject($dictionary);
        $this->_objFactory     = $factory;
        $this->_attached       = false;
        $this->_style          = null;
        $this->_font           = null;
    }

    /**
     * Clone page, extract it and dependent objects from the current document,
     * so it can be used within other docs.
     *
     * @internal
     * @param \Zend\Pdf\ObjectFactory $factory
     * @param array $processed
     * @return \Zend\Pdf\Page
     */
    public function clonePage(ObjectFactory $factory, &$processed)
    {
        // Clone dictionary object.
        // Do it explicitly to prevent sharing page attributes between different
        // results of clonePage() operation (other resources are still shared)
        $dictionary = new InternalType\DictionaryObject();
        foreach ($this->_pageDictionary->getKeys() as $key) {
            $dictionary->$key = $this->_pageDictionary->$key->makeClone($factory,
                                                                        $processed,
                                                                        InternalType\AbstractTypeObject::CLONE_MODE_SKIP_PAGES);
        }

        $clonedPage = new Page($factory->newObject($dictionary), $factory);
        $clonedPage->_attached = false;

        return $clonedPage;
    }

    /**
     * Retrive PDF file reference to the page
     *
     * @internal
     * @return \Zend\Pdf\InternalType\DictionaryObject
     */
    public function getPageDictionary()
    {
        return $this->_pageDictionary;
    }

    /**
     * Dump current drawing instructions into the content stream.
     *
     * @todo Don't forget to close all current graphics operations (like path drawing)
     *
     * @throws \Zend\Pdf\Exception
     */
    public function flush()
    {
        if ($this->_saveCount != 0) {
            throw new Exception\LogicException('Saved graphics state is not restored');
        }

        if ($this->_contents == '') {
            return;
        }

        if ($this->_pageDictionary->Contents->getType() != InternalType\AbstractTypeObject::TYPE_ARRAY) {
            /**
             * It's a stream object.
             * Prepare Contents page attribute for update.
             */
            $this->_pageDictionary->touch();

            $currentPageContents = $this->_pageDictionary->Contents;
            $this->_pageDictionary->Contents = new InternalType\ArrayObject();
            $this->_pageDictionary->Contents->items[] = $currentPageContents;
        } else {
            $this->_pageDictionary->Contents->touch();
        }

        if ((!$this->_safeGS)  &&  (count($this->_pageDictionary->Contents->items) != 0)) {
            /**
             * Page already has some content which is not treated as safe.
             *
             * Add save/restore GS operators
             */
            $this->_addProcSet('PDF');

            $newContentsArray = new InternalType\ArrayObject();
            $newContentsArray->items[] = $this->_objFactory->newStreamObject(" q\n");
            foreach ($this->_pageDictionary->Contents->items as $contentStream) {
                $newContentsArray->items[] = $contentStream;
            }
            $newContentsArray->items[] = $this->_objFactory->newStreamObject(" Q\n");

            $this->_pageDictionary->touch();
            $this->_pageDictionary->Contents = $newContentsArray;

            $this->_safeGS = true;
        }

        $this->_pageDictionary->Contents->items[] =
                $this->_objFactory->newStreamObject($this->_contents);

        $this->_contents = '';
    }

    /**
     * Prepare page to be rendered into PDF.
     *
     * @todo Don't forget to close all current graphics operations (like path drawing)
     *
     * @param \Zend\Pdf\ObjectFactory $objFactory
     * @throws \Zend\Pdf\Exception
     */
    public function render(ObjectFactory $objFactory)
    {
        $this->flush();

        if ($objFactory === $this->_objFactory) {
            // Page is already attached to the document.
            return;
        }

        if ($this->_attached) {
            throw new Exception\LogicException('Page is attached to other document. Use clone $page to get it context free.');
        } else {
            $objFactory->attach($this->_objFactory);
        }
    }



    /**
     * Set fill color.
     *
     * @param \Zend\Pdf\Color $color
     * @return \Zend\Pdf\Page
     */
    public function setFillColor(Color $color)
    {
        $this->_addProcSet('PDF');
        $this->_contents .= $color->instructions(false);

        return $this;
    }

    /**
     * Set line color.
     *
     * @param \Zend\Pdf\Color $color
     * @return \Zend\Pdf\Page
     */
    public function setLineColor(Color $color)
    {
        $this->_addProcSet('PDF');
        $this->_contents .= $color->instructions(true);

        return $this;
    }

    /**
     * Set line width.
     *
     * @param float $width
     * @return \Zend\Pdf\Page
     */
    public function setLineWidth($width)
    {
        $this->_addProcSet('PDF');
        $widthObj = new InternalType\NumericObject($width);
        $this->_contents .= $widthObj->toString() . " w\n";

        return $this;
    }

    /**
     * Set line dashing pattern
     *
     * Pattern is an array of floats: array(on_length, off_length, on_length, off_length, ...)
     * Phase is shift from the beginning of line.
     *
     * @param array $pattern
     * @param array $phase
     * @return \Zend\Pdf\Page
     */
    public function setLineDashingPattern($pattern, $phase = 0)
    {
        $this->_addProcSet('PDF');

        if ($pattern === self::LINE_DASHING_SOLID) {
            $pattern = array();
            $phase   = 0;
        }

        $dashPattern  = new InternalType\ArrayObject();
        $phaseEleemnt = new InternalType\NumericObject($phase);

        foreach ($pattern as $dashItem) {
            $dashElement = new InternalType\NumericObject($dashItem);
            $dashPattern->items[] = $dashElement;
        }

        $this->_contents .= $dashPattern->toString() . ' '
                         . $phaseEleemnt->toString() . " d\n";

        return $this;
    }

    /**
     * Set current font.
     *
     * @param \Zend\Pdf\Resource\Font\AbstractFont $font
     * @param float $fontSize
     * @return \Zend\Pdf\Page
     */
    public function setFont(Resource\Font\AbstractFont $font, $fontSize)
    {
        $this->_addProcSet('Text');
        $fontName = $this->_attachResource('Font', $font);

        $this->_font     = $font;
        $this->_fontSize = $fontSize;

        $fontNameObj = new InternalType\NameObject($fontName);
        $fontSizeObj = new InternalType\NumericObject($fontSize);
        $this->_contents .= $fontNameObj->toString() . ' ' . $fontSizeObj->toString() . " Tf\n";

        return $this;
    }

    /**
     * Set the style to use for future drawing operations on this page
     *
     * @param \Zend\Pdf\Style $style
     * @return \Zend\Pdf\Page
     */
    public function setStyle(Style $style)
    {
        $this->_style = $style;

        $this->_addProcSet('Text');
        $this->_addProcSet('PDF');
        if ($style->getFont() !== null) {
            $this->setFont($style->getFont(), $style->getFontSize());
        }
        $this->_contents .= $style->instructions($this->_pageDictionary->Resources);

        return $this;
    }

    /**
     * Set the transparancy
     *
     * $alpha == 0  - transparent
     * $alpha == 1  - opaque
     *
     * Transparency modes, supported by PDF:
     * Normal (default), Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn, HardLight,
     * SoftLight, Difference, Exclusion
     *
     * @param float $alpha
     * @param string $mode
     * @throws \Zend\Pdf\Exception
     * @return \Zend\Pdf\Page
     */
    public function setAlpha($alpha, $mode = 'Normal')
    {
        if (!in_array($mode, array('Normal', 'Multiply', 'Screen', 'Overlay', 'Darken', 'Lighten', 'ColorDodge',
                                   'ColorBurn', 'HardLight', 'SoftLight', 'Difference', 'Exclusion'))) {
            throw new Exception\InvalidArgumentException('Unsupported transparency mode.');
        }
        if (!is_numeric($alpha)  ||  $alpha < 0  ||  $alpha > 1) {
            throw new Exception\InvalidArgumentException('Alpha value must be numeric between 0 (transparent) and 1 (opaque).');
        }

        $this->_addProcSet('Text');
        $this->_addProcSet('PDF');

        $resources = $this->_pageDictionary->Resources;

        // Check if Resources dictionary contains ExtGState entry
        if ($resources->ExtGState === null) {
            $resources->touch();
            $resources->ExtGState = new InternalType\DictionaryObject();
        } else {
            $resources->ExtGState->touch();
        }

        $idCounter = 1;
        do {
            $gStateName = 'GS' . $idCounter++;
        } while ($resources->ExtGState->$gStateName !== null);


        $gStateDictionary       = new InternalType\DictionaryObject();
        $gStateDictionary->Type = new InternalType\NameObject('ExtGState');
        $gStateDictionary->BM   = new InternalType\NameObject($mode);
        $gStateDictionary->CA   = new InternalType\NumericObject($alpha);
        $gStateDictionary->ca   = new InternalType\NumericObject($alpha);

        $resources->ExtGState->$gStateName = $this->_objFactory->newObject($gStateDictionary);

        $gStateNameObj = new InternalType\NameObject($gStateName);
        $this->_contents .= $gStateNameObj->toString() . " gs\n";

        return $this;
    }


    /**
     * Get current font.
     *
     * @return \Zend\Pdf\Resource\Font\AbstractFont $font
     */
    public function getFont()
    {
        return $this->_font;
    }

    /**
     * Extract resources attached to the page
     *
     * This method is not intended to be used in userland, but helps to optimize some document wide operations
     *
     * returns array of \Zend\Pdf\InternalType\DictionaryObject objects
     *
     * @internal
     * @return array
     */
    public function extractResources()
    {
        return $this->_pageDictionary->Resources;
    }

    /**
     * Extract fonts attached to the page
     *
     * returns array of \Zend\Pdf\Resource\Font\Extracted objects
     *
     * @return array
     * @throws \Zend\Pdf\Exception
     */
    public function extractFonts()
    {
        if ($this->_pageDictionary->Resources->Font === null) {
            // Page doesn't have any font attached
            // Return empty array
            return array();
        }

        $fontResources = $this->_pageDictionary->Resources->Font;

        $fontResourcesUnique = array();
        foreach ($fontResources->getKeys() as $fontResourceName) {
            $fontDictionary = $fontResources->$fontResourceName;

            if (! ($fontDictionary instanceof InternalType\IndirectObjectReference  ||
                   $fontDictionary instanceof InternalType\IndirectObject) ) {
                throw new Exception\CorruptedPdfException('Font dictionary has to be an indirect object or object reference.');
            }

            $fontResourcesUnique[spl_object_hash($fontDictionary->getObject())] = $fontDictionary;
        }

        $fonts = array();
        foreach ($fontResourcesUnique as $resourceId => $fontDictionary) {
            try {
                // Try to extract font
                $extractedFont = new Resource\Font\Extracted($fontDictionary);

                $fonts[$resourceId] = $extractedFont;
            } catch (Exception\NotImplementedException $e) {
                // Just skip unsupported font types.
                if ($e->getMessage() != Resource\Font\Font\Extracted::TYPE_NOT_SUPPORTED) {
                    throw $e;
                }
            }
        }

        return $fonts;
    }

    /**
     * Extract font attached to the page by specific font name
     *
     * $fontName should be specified in UTF-8 encoding
     *
     * @return \Zend\Pdf\Resource\Font\Extracted|null
     * @throws \Zend\Pdf\Exception
     */
    public function extractFont($fontName)
    {
        if ($this->_pageDictionary->Resources->Font === null) {
            // Page doesn't have any font attached
            return null;
        }

        $fontResources = $this->_pageDictionary->Resources->Font;

        $fontResourcesUnique = array();

        foreach ($fontResources->getKeys() as $fontResourceName) {
            $fontDictionary = $fontResources->$fontResourceName;

            if (! ($fontDictionary instanceof InternalType\IndirectObjectReference  ||
                   $fontDictionary instanceof InternalType\IndirectObject) ) {
                throw new Exception\CorruptedPdfException('Font dictionary has to be an indirect object or object reference.');
            }

            $resourceId = spl_object_hash($fontDictionary->getObject());
            if (isset($fontResourcesUnique[$resourceId])) {
                continue;
            } else {
                // Mark resource as processed
                $fontResourcesUnique[$resourceId] = 1;
            }

            if ($fontDictionary->BaseFont->value != $fontName) {
                continue;
            }

            try {
                // Try to extract font
                return new Resource\Font\Extracted($fontDictionary);
            } catch (Exception\NotImplementedException $e) {
                // Just skip unsupported font types.
                if ($e->getMessage() != Resource\Font\Font\Extracted::TYPE_NOT_SUPPORTED) {
                    throw $e;
                }

                // Continue searhing font with specified name
            }
        }

        return null;
    }

    /**
     * Get current font size
     *
     * @return float $fontSize
     */
    public function getFontSize()
    {
        return $this->_fontSize;
    }

    /**
     * Return the style, applied to the page.
     *
     * @return \Zend\Pdf\Style|null
     */
    public function getStyle()
    {
        return $this->_style;
    }


    /**
     * Save the graphics state of this page.
     * This takes a snapshot of the currently applied style, position, clipping area and
     * any rotation/translation/scaling that has been applied.
     *
     * @todo check for the open paths
     * @throws \Zend\Pdf\Exception
     * @return \Zend\Pdf\Page
     */
    public function saveGS()
    {
        $this->_saveCount++;

        $this->_addProcSet('PDF');
        $this->_contents .= " q\n";

        return $this;
    }

    /**
     * Restore the graphics state that was saved with the last call to saveGS().
     *
     * @throws \Zend\Pdf\Exception
     * @return \Zend\Pdf\Page
     */
    public function restoreGS()
    {
        if ($this->_saveCount-- <= 0) {
            throw new Exception\LogicException('Restoring graphics state which is not saved');
        }
        $this->_contents .= " Q\n";

        return $this;
    }


    /**
     * Intersect current clipping area with a circle
     *
     * @param float $x           X-coordinate for the middle of the circle
     * @param float $y           Y-coordinate for the middle of the circle
     * @param float $radius      Radius of the circle
     * @param float $startAngle  Starting angle of the circle in radians
     * @param float $endAngle    Ending angle of the circle in radians
     * @return \Zend\Pdf\Page    Fluid interface
     */
    public function clipCircle($x, $y, $radius, $startAngle = null, $endAngle = null)
    {
        $this->clipEllipse($x - $radius, $y - $radius,
                           $x + $radius, $y + $radius,
                           $startAngle, $endAngle);

        return $this;
    }

    /**
     * Intersect current clipping area with a ellipse
     *
     * Method signatures:
     * drawEllipse($x1, $y1, $x2, $y2);
     * drawEllipse($x1, $y1, $x2, $y2, $startAngle, $endAngle);
     *
     * @todo process special cases with $x2-$x1 == 0 or $y2-$y1 == 0
     *
     * @param float $x1          X-coordinate of left upper corner of the ellipse
     * @param float $y1          Y-coordinate of left upper corner of the ellipse
     * @param float $x2          X-coordinate of right lower corner of the ellipse
     * @param float $y2          Y-coordinate of right lower corner of the ellipse
     * @param float $startAngle  Starting angle of the ellipse in radians
     * @param float $endAngle    Ending angle of the ellipse in radians
     * @return \Zend\Pdf\Page    Fluid interface
     */
    public function clipEllipse($x1, $y1, $x2, $y2, $startAngle = null, $endAngle = null)
    {
        $this->_addProcSet('PDF');

        if ($x2 < $x1) {
            $temp = $x1;
            $x1   = $x2;
            $x2   = $temp;
        }
        if ($y2 < $y1) {
            $temp = $y1;
            $y1   = $y2;
            $y2   = $temp;
        }

        $x = ($x1 + $x2)/2.;
        $y = ($y1 + $y2)/2.;

        $xC = new InternalType\NumericObject($x);
        $yC = new InternalType\NumericObject($y);

        if ($startAngle !== null) {
            if ($startAngle != 0) { $startAngle = fmod($startAngle, M_PI*2); }
            if ($endAngle   != 0) { $endAngle   = fmod($endAngle,   M_PI*2); }

            if ($startAngle > $endAngle) {
                $endAngle += M_PI*2;
            }

            $clipPath    = $xC->toString() . ' ' . $yC->toString() . " m\n";
            $clipSectors = (int)ceil(($endAngle - $startAngle)/M_PI_4);
            $clipRadius  = max($x2 - $x1, $y2 - $y1);

            for($count = 0; $count <= $clipSectors; $count++) {
                $pAngle = $startAngle + ($endAngle - $startAngle)*$count/(float)$clipSectors;

                $pX = new InternalType\NumericObject($x + cos($pAngle)*$clipRadius);
                $pY = new InternalType\NumericObject($y + sin($pAngle)*$clipRadius);
                $clipPath .= $pX->toString() . ' ' . $pY->toString() . " l\n";
            }

            $this->_contents .= $clipPath . "h\nW\nn\n";
        }

        $xLeft  = new InternalType\NumericObject($x1);
        $xRight = new InternalType\NumericObject($x2);
        $yUp    = new InternalType\NumericObject($y2);
        $yDown  = new InternalType\NumericObject($y1);

        $xDelta  = 2*(M_SQRT2 - 1)*($x2 - $x1)/3.;
        $yDelta  = 2*(M_SQRT2 - 1)*($y2 - $y1)/3.;
        $xr = new InternalType\NumericObject($x + $xDelta);
        $xl = new InternalType\NumericObject($x - $xDelta);
        $yu = new InternalType\NumericObject($y + $yDelta);
        $yd = new InternalType\NumericObject($y - $yDelta);

        $this->_contents .= $xC->toString() . ' ' . $yUp->toString() . " m\n"
                         .  $xr->toString() . ' ' . $yUp->toString() . ' '
                         .    $xRight->toString() . ' ' . $yu->toString() . ' '
                         .      $xRight->toString() . ' ' . $yC->toString() . " c\n"
                         .  $xRight->toString() . ' ' . $yd->toString() . ' '
                         .    $xr->toString() . ' ' . $yDown->toString() . ' '
                         .      $xC->toString() . ' ' . $yDown->toString() . " c\n"
                         .  $xl->toString() . ' ' . $yDown->toString() . ' '
                         .    $xLeft->toString() . ' ' . $yd->toString() . ' '
                         .      $xLeft->toString() . ' ' . $yC->toString() . " c\n"
                         .  $xLeft->toString() . ' ' . $yu->toString() . ' '
                         .    $xl->toString() . ' ' . $yUp->toString() . ' '
                         .      $xC->toString() . ' ' . $yUp->toString() . " c\n"
                         .  "h\nW\nn\n";

        return $this;
    }


    /**
     * Intersect current clipping area with a polygon.
     *
     * @param array $x  - array of float (the X co-ordinates of the vertices)
     * @param array $y  - array of float (the Y co-ordinates of the vertices)
     * @param integer $fillMethod
     * @return \Zend\Pdf\Page
     */
    public function clipPolygon($x, $y, $fillMethod = self::FILL_METHOD_NON_ZERO_WINDING)
    {
        $this->_addProcSet('PDF');

        $firstPoint = true;
        foreach ($x as $id => $xVal) {
            $xObj = new InternalType\NumericObject($xVal);
            $yObj = new InternalType\NumericObject($y[$id]);

            if ($firstPoint) {
                $path = $xObj->toString() . ' ' . $yObj->toString() . " m\n";
                $firstPoint = false;
            } else {
                $path .= $xObj->toString() . ' ' . $yObj->toString() . " l\n";
            }
        }

        $this->_contents .= $path;

        if ($fillMethod == self::FILL_METHOD_NON_ZERO_WINDING) {
            $this->_contents .= " h\n W\nn\n";
        } else {
            // Even-Odd fill method.
            $this->_contents .= " h\n W*\nn\n";
        }

        return $this;
    }

    /**
     * Intersect current clipping area with a rectangle.
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @return \Zend\Pdf\Page
     */
    public function clipRectangle($x1, $y1, $x2, $y2)
    {
        $this->_addProcSet('PDF');

        $x1Obj      = new InternalType\NumericObject($x1);
        $y1Obj      = new InternalType\NumericObject($y1);
        $widthObj   = new InternalType\NumericObject($x2 - $x1);
        $height2Obj = new InternalType\NumericObject($y2 - $y1);

        $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . ' '
                         .      $widthObj->toString() . ' ' . $height2Obj->toString() . " re\n"
                         .  " W\nn\n";

        return $this;
    }

    /**
     * Draw a \Zend\Pdf\ContentStream at the specified position on the page
     *
     * @param ZPDFContentStream $cs
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @return \Zend\Pdf\Page
     */
    public function drawContentStream($cs, $x1, $y1, $x2, $y2)
    {
        /** @todo implementation */
        return $this;
    }

    /**
     * Draw a circle centered on x, y with a radius of radius.
     *
     * Method signatures:
     * drawCircle($x, $y, $radius);
     * drawCircle($x, $y, $radius, $fillType);
     * drawCircle($x, $y, $radius, $startAngle, $endAngle);
     * drawCircle($x, $y, $radius, $startAngle, $endAngle, $fillType);
     *
     *
     * It's not a really circle, because PDF supports only cubic Bezier curves.
     * But _very_ good approximation.
     * It differs from a real circle on a maximum 0.00026 radiuses
     * (at PI/8, 3*PI/8, 5*PI/8, 7*PI/8, 9*PI/8, 11*PI/8, 13*PI/8 and 15*PI/8 angles).
     * At 0, PI/4, PI/2, 3*PI/4, PI, 5*PI/4, 3*PI/2 and 7*PI/4 it's exactly a tangent to a circle.
     *
     * @param float $x
     * @param float $y
     * @param float $radius
     * @param mixed $param4
     * @param mixed $param5
     * @param mixed $param6
     * @return \Zend\Pdf\Page
     */
    public function  drawCircle($x, $y, $radius, $param4 = null, $param5 = null, $param6 = null)
    {
        $this->drawEllipse($x - $radius, $y - $radius,
                           $x + $radius, $y + $radius,
                           $param4, $param5, $param6);

        return $this;
    }

    /**
     * Draw an ellipse inside the specified rectangle.
     *
     * Method signatures:
     * drawEllipse($x1, $y1, $x2, $y2);
     * drawEllipse($x1, $y1, $x2, $y2, $fillType);
     * drawEllipse($x1, $y1, $x2, $y2, $startAngle, $endAngle);
     * drawEllipse($x1, $y1, $x2, $y2, $startAngle, $endAngle, $fillType);
     *
     * @todo process special cases with $x2-$x1 == 0 or $y2-$y1 == 0
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param mixed $param5
     * @param mixed $param6
     * @param mixed $param7
     * @return \Zend\Pdf\Page
     */
    public function drawEllipse($x1, $y1, $x2, $y2, $param5 = null, $param6 = null, $param7 = null)
    {
        if ($param5 === null) {
            // drawEllipse($x1, $y1, $x2, $y2);
            $startAngle = null;
            $fillType = self::SHAPE_DRAW_FILL_AND_STROKE;
        } else if ($param6 === null) {
            // drawEllipse($x1, $y1, $x2, $y2, $fillType);
            $startAngle = null;
            $fillType = $param5;
        } else {
            // drawEllipse($x1, $y1, $x2, $y2, $startAngle, $endAngle);
            // drawEllipse($x1, $y1, $x2, $y2, $startAngle, $endAngle, $fillType);
            $startAngle = $param5;
            $endAngle   = $param6;

            if ($param7 === null) {
                $fillType = self::SHAPE_DRAW_FILL_AND_STROKE;
            } else {
                $fillType = $param7;
            }
        }

        $this->_addProcSet('PDF');

        if ($x2 < $x1) {
            $temp = $x1;
            $x1   = $x2;
            $x2   = $temp;
        }
        if ($y2 < $y1) {
            $temp = $y1;
            $y1   = $y2;
            $y2   = $temp;
        }

        $x = ($x1 + $x2)/2.;
        $y = ($y1 + $y2)/2.;

        $xC = new InternalType\NumericObject($x);
        $yC = new InternalType\NumericObject($y);

        if ($startAngle !== null) {
            if ($startAngle != 0) { $startAngle = fmod($startAngle, M_PI*2); }
            if ($endAngle   != 0) { $endAngle   = fmod($endAngle,   M_PI*2); }

            if ($startAngle > $endAngle) {
                $endAngle += M_PI*2;
            }

            $clipPath    = $xC->toString() . ' ' . $yC->toString() . " m\n";
            $clipSectors = (int)ceil(($endAngle - $startAngle)/M_PI_4);
            $clipRadius  = max($x2 - $x1, $y2 - $y1);

            for($count = 0; $count <= $clipSectors; $count++) {
                $pAngle = $startAngle + ($endAngle - $startAngle)*$count/(float)$clipSectors;

                $pX = new InternalType\NumericObject($x + cos($pAngle)*$clipRadius);
                $pY = new InternalType\NumericObject($y + sin($pAngle)*$clipRadius);
                $clipPath .= $pX->toString() . ' ' . $pY->toString() . " l\n";
            }

            $this->_contents .= "q\n" . $clipPath . "h\nW\nn\n";
        }

        $xLeft  = new InternalType\NumericObject($x1);
        $xRight = new InternalType\NumericObject($x2);
        $yUp    = new InternalType\NumericObject($y2);
        $yDown  = new InternalType\NumericObject($y1);

        $xDelta  = 2*(M_SQRT2 - 1)*($x2 - $x1)/3.;
        $yDelta  = 2*(M_SQRT2 - 1)*($y2 - $y1)/3.;
        $xr = new InternalType\NumericObject($x + $xDelta);
        $xl = new InternalType\NumericObject($x - $xDelta);
        $yu = new InternalType\NumericObject($y + $yDelta);
        $yd = new InternalType\NumericObject($y - $yDelta);

        $this->_contents .= $xC->toString() . ' ' . $yUp->toString() . " m\n"
                         .  $xr->toString() . ' ' . $yUp->toString() . ' '
                         .    $xRight->toString() . ' ' . $yu->toString() . ' '
                         .      $xRight->toString() . ' ' . $yC->toString() . " c\n"
                         .  $xRight->toString() . ' ' . $yd->toString() . ' '
                         .    $xr->toString() . ' ' . $yDown->toString() . ' '
                         .      $xC->toString() . ' ' . $yDown->toString() . " c\n"
                         .  $xl->toString() . ' ' . $yDown->toString() . ' '
                         .    $xLeft->toString() . ' ' . $yd->toString() . ' '
                         .      $xLeft->toString() . ' ' . $yC->toString() . " c\n"
                         .  $xLeft->toString() . ' ' . $yu->toString() . ' '
                         .    $xl->toString() . ' ' . $yUp->toString() . ' '
                         .      $xC->toString() . ' ' . $yUp->toString() . " c\n";

        switch ($fillType) {
            case self::SHAPE_DRAW_FILL_AND_STROKE:
                $this->_contents .= " B*\n";
                break;
            case self::SHAPE_DRAW_FILL:
                $this->_contents .= " f*\n";
                break;
            case self::SHAPE_DRAW_STROKE:
                $this->_contents .= " S\n";
                break;
        }

        if ($startAngle !== null) {
            $this->_contents .= "Q\n";
        }

        return $this;
    }

    /**
     * Draw an image at the specified position on the page.
     *
     * @param \Zend\Pdf\Image $image
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @return \Zend\Pdf\Page
     */
    public function drawImage(Resource\Image\AbstractImage $image, $x1, $y1, $x2, $y2)
    {
        $this->_addProcSet('PDF');

        $imageName    = $this->_attachResource('XObject', $image);
        $imageNameObj = new InternalType\NameObject($imageName);

        $x1Obj     = new InternalType\NumericObject($x1);
        $y1Obj     = new InternalType\NumericObject($y1);
        $widthObj  = new InternalType\NumericObject($x2 - $x1);
        $heightObj = new InternalType\NumericObject($y2 - $y1);

        $this->_contents .= "q\n"
                         .  '1 0 0 1 ' . $x1Obj->toString() . ' ' . $y1Obj->toString() . " cm\n"
                         .  $widthObj->toString() . ' 0 0 ' . $heightObj->toString() . " 0 0 cm\n"
                         .  $imageNameObj->toString() . " Do\n"
                         .  "Q\n";

        return $this;
    }

    /**
     * Draw a LayoutBox at the specified position on the page.
     *
     * @param \Zend\Pdf\InternalType\LayoutBox $box
     * @param float $x
     * @param float $y
     * @return \Zend\Pdf\Page
     */
    public function drawLayoutBox($box, $x, $y)
    {
        /** @todo implementation */
        return $this;
    }

    /**
     * Draw a line from x1,y1 to x2,y2.
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @return \Zend\Pdf\Page
     */
    public function drawLine($x1, $y1, $x2, $y2)
    {
        $this->_addProcSet('PDF');

        $x1Obj = new InternalType\NumericObject($x1);
        $y1Obj = new InternalType\NumericObject($y1);
        $x2Obj = new InternalType\NumericObject($x2);
        $y2Obj = new InternalType\NumericObject($y2);

        $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . " m\n"
                         .  $x2Obj->toString() . ' ' . $y2Obj->toString() . " l\n S\n";

        return $this;
    }

    /**
     * Draw a polygon.
     *
     * If $fillType is \Zend\Pdf\Page::SHAPE_DRAW_FILL_AND_STROKE or
     * \Zend\Pdf\Page::SHAPE_DRAW_FILL, then polygon is automatically closed.
     * See detailed description of these methods in a PDF documentation
     * (section 4.4.2 Path painting Operators, Filling)
     *
     * @param array $x  - array of float (the X co-ordinates of the vertices)
     * @param array $y  - array of float (the Y co-ordinates of the vertices)
     * @param integer $fillType
     * @param integer $fillMethod
     * @return \Zend\Pdf\Page
     */
    public function drawPolygon($x, $y,
                                $fillType = self::SHAPE_DRAW_FILL_AND_STROKE,
                                $fillMethod = self::FILL_METHOD_NON_ZERO_WINDING)
    {
        $this->_addProcSet('PDF');

        $firstPoint = true;
        foreach ($x as $id => $xVal) {
            $xObj = new InternalType\NumericObject($xVal);
            $yObj = new InternalType\NumericObject($y[$id]);

            if ($firstPoint) {
                $path = $xObj->toString() . ' ' . $yObj->toString() . " m\n";
                $firstPoint = false;
            } else {
                $path .= $xObj->toString() . ' ' . $yObj->toString() . " l\n";
            }
        }

        $this->_contents .= $path;

        switch ($fillType) {
            case self::SHAPE_DRAW_FILL_AND_STROKE:
                if ($fillMethod == self::FILL_METHOD_NON_ZERO_WINDING) {
                    $this->_contents .= " b\n";
                } else {
                    // Even-Odd fill method.
                    $this->_contents .= " b*\n";
                }
                break;
            case self::SHAPE_DRAW_FILL:
                if ($fillMethod == self::FILL_METHOD_NON_ZERO_WINDING) {
                    $this->_contents .= " h\n f\n";
                } else {
                    // Even-Odd fill method.
                    $this->_contents .= " h\n f*\n";
                }
                break;
            case self::SHAPE_DRAW_STROKE:
                $this->_contents .= " S\n";
                break;
        }

        return $this;
    }

    /**
     * Draw a rectangle.
     *
     * Fill types:
     * \Zend\Pdf\Page::SHAPE_DRAW_FILL_AND_STROKE - fill rectangle and stroke (default)
     * \Zend\Pdf\Page::SHAPE_DRAW_STROKE      - stroke rectangle
     * \Zend\Pdf\Page::SHAPE_DRAW_FILL        - fill rectangle
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param integer $fillType
     * @return \Zend\Pdf\Page
     */
    public function drawRectangle($x1, $y1, $x2, $y2, $fillType = self::SHAPE_DRAW_FILL_AND_STROKE)
    {
        $this->_addProcSet('PDF');

        $x1Obj      = new InternalType\NumericObject($x1);
        $y1Obj      = new InternalType\NumericObject($y1);
        $widthObj   = new InternalType\NumericObject($x2 - $x1);
        $height2Obj = new InternalType\NumericObject($y2 - $y1);

        $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . ' '
                             .  $widthObj->toString() . ' ' . $height2Obj->toString() . " re\n";

        switch ($fillType) {
            case self::SHAPE_DRAW_FILL_AND_STROKE:
                $this->_contents .= " B*\n";
                break;
            case self::SHAPE_DRAW_FILL:
                $this->_contents .= " f*\n";
                break;
            case self::SHAPE_DRAW_STROKE:
                $this->_contents .= " S\n";
                break;
        }

        return $this;
    }

    /**
     * Draw a rounded rectangle.
     *
     * Fill types:
     * \Zend\Pdf\Page::SHAPE_DRAW_FILL_AND_STROKE - fill rectangle and stroke (default)
     * \Zend\Pdf\Page::SHAPE_DRAW_STROKE      - stroke rectangle
     * \Zend\Pdf\Page::SHAPE_DRAW_FILL        - fill rectangle
     *
     * radius is an integer representing radius of the four corners, or an array
     * of four integers representing the radius starting at top left, going
     * clockwise
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param integer|array $radius
     * @param integer $fillType
     * @return \Zend\Pdf\Page
     */
    public function drawRoundedRectangle($x1, $y1, $x2, $y2, $radius,
                                         $fillType = self::SHAPE_DRAW_FILL_AND_STROKE)
    {

        $this->_addProcSet('PDF');

        if(!is_array($radius)) {
            $radius = array($radius, $radius, $radius, $radius);
        } else {
            for ($i = 0; $i < 4; $i++) {
                if(!isset($radius[$i])) {
                    $radius[$i] = 0;
                }
            }
        }

        $topLeftX      = $x1;
        $topLeftY      = $y2;
        $topRightX     = $x2;
        $topRightY     = $y2;
        $bottomRightX  = $x2;
        $bottomRightY  = $y1;
        $bottomLeftX   = $x1;
        $bottomLeftY   = $y1;

        //draw top side
        $x1Obj = new InternalType\NumericObject($topLeftX + $radius[0]);
        $y1Obj = new InternalType\NumericObject($topLeftY);
        $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . " m\n";
        $x1Obj = new InternalType\NumericObject($topRightX - $radius[1]);
        $y1Obj = new InternalType\NumericObject($topRightY);
        $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . " l\n";

        //draw top right corner if needed
        if ($radius[1] != 0) {
            $x1Obj = new InternalType\NumericObject($topRightX);
            $y1Obj = new InternalType\NumericObject($topRightY);
            $x2Obj = new InternalType\NumericObject($topRightX);
            $y2Obj = new InternalType\NumericObject($topRightY);
            $x3Obj = new InternalType\NumericObject($topRightX);
            $y3Obj = new InternalType\NumericObject($topRightY - $radius[1]);
            $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . ' '
                              . $x2Obj->toString() . ' ' . $y2Obj->toString() . ' '
                              . $x3Obj->toString() . ' ' . $y3Obj->toString() . ' '
                              . " c\n";
        }

        //draw right side
        $x1Obj = new InternalType\NumericObject($bottomRightX);
        $y1Obj = new InternalType\NumericObject($bottomRightY + $radius[2]);
        $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . " l\n";

        //draw bottom right corner if needed
        if ($radius[2] != 0) {
            $x1Obj = new InternalType\NumericObject($bottomRightX);
            $y1Obj = new InternalType\NumericObject($bottomRightY);
            $x2Obj = new InternalType\NumericObject($bottomRightX);
            $y2Obj = new InternalType\NumericObject($bottomRightY);
            $x3Obj = new InternalType\NumericObject($bottomRightX - $radius[2]);
            $y3Obj = new InternalType\NumericObject($bottomRightY);
            $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . ' '
                              . $x2Obj->toString() . ' ' . $y2Obj->toString() . ' '
                              . $x3Obj->toString() . ' ' . $y3Obj->toString() . ' '
                              . " c\n";
        }

        //draw bottom side
        $x1Obj = new InternalType\NumericObject($bottomLeftX + $radius[3]);
        $y1Obj = new InternalType\NumericObject($bottomLeftY);
        $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . " l\n";

        //draw bottom left corner if needed
        if ($radius[3] != 0) {
            $x1Obj = new InternalType\NumericObject($bottomLeftX);
            $y1Obj = new InternalType\NumericObject($bottomLeftY);
            $x2Obj = new InternalType\NumericObject($bottomLeftX);
            $y2Obj = new InternalType\NumericObject($bottomLeftY);
            $x3Obj = new InternalType\NumericObject($bottomLeftX);
            $y3Obj = new InternalType\NumericObject($bottomLeftY + $radius[3]);
            $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . ' '
                              . $x2Obj->toString() . ' ' . $y2Obj->toString() . ' '
                              . $x3Obj->toString() . ' ' . $y3Obj->toString() . ' '
                              . " c\n";
        }

        //draw left side
        $x1Obj = new InternalType\NumericObject($topLeftX);
        $y1Obj = new InternalType\NumericObject($topLeftY - $radius[0]);
        $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . " l\n";

        //draw top left corner if needed
        if ($radius[0] != 0) {
            $x1Obj = new InternalType\NumericObject($topLeftX);
            $y1Obj = new InternalType\NumericObject($topLeftY);
            $x2Obj = new InternalType\NumericObject($topLeftX);
            $y2Obj = new InternalType\NumericObject($topLeftY);
            $x3Obj = new InternalType\NumericObject($topLeftX + $radius[0]);
            $y3Obj = new InternalType\NumericObject($topLeftY);
            $this->_contents .= $x1Obj->toString() . ' ' . $y1Obj->toString() . ' '
                              . $x2Obj->toString() . ' ' . $y2Obj->toString() . ' '
                              . $x3Obj->toString() . ' ' . $y3Obj->toString() . ' '
                              . " c\n";
        }

        switch ($fillType) {
            case self::SHAPE_DRAW_FILL_AND_STROKE:
                $this->_contents .= " B*\n";
                break;
            case self::SHAPE_DRAW_FILL:
                $this->_contents .= " f*\n";
                break;
            case self::SHAPE_DRAW_STROKE:
                $this->_contents .= " S\n";
                break;
        }

        return $this;
    }

    /**
     * Draw a line of text at the specified position.
     *
     * @param string $text
     * @param float $x
     * @param float $y
     * @param string $charEncoding (optional) Character encoding of source text.
     *   Defaults to current locale.
     * @throws \Zend\Pdf\Exception
     * @return \Zend\Pdf\Page
     */
    public function drawText($text, $x, $y, $charEncoding = '')
    {
        if ($this->_font === null) {
            throw new Exception\LogicException('Font has not been set');
        }

        $this->_addProcSet('Text');

        $textObj = new InternalType\StringObject($this->_font->encodeString($text, $charEncoding));
        $xObj    = new InternalType\NumericObject($x);
        $yObj    = new InternalType\NumericObject($y);

        $this->_contents .= "BT\n"
                         .  $xObj->toString() . ' ' . $yObj->toString() . " Td\n"
                         .  $textObj->toString() . " Tj\n"
                         .  "ET\n";

        return $this;
    }

    /**
     *
     * @param \Zend\Pdf\Annotation\AbstractAnnotation $annotation
     * @return \Zend\Pdf\Page
     */
    public function attachAnnotation(Annotation\AbstractAnnotation $annotation)
    {
        $annotationDictionary = $annotation->getResource();
        if (!$annotationDictionary instanceof InternalType\IndirectObject  &&
            !$annotationDictionary instanceof InternalType\IndirectObjectReference) {
            $annotationDictionary = $this->_objFactory->newObject($annotationDictionary);
        }

        if ($this->_pageDictionary->Annots === null) {
            $this->_pageDictionary->touch();
            $this->_pageDictionary->Annots = new InternalType\ArrayObject();
        } else {
            $this->_pageDictionary->Annots->touch();
        }

        $this->_pageDictionary->Annots->items[] = $annotationDictionary;

        $annotationDictionary->touch();
        $annotationDictionary->P = $this->_pageDictionary;

        return $this;
    }

    /**
     * Return the height of this page in points.
     *
     * @return float
     */
    public function getHeight()
    {
        return $this->_pageDictionary->MediaBox->items[3]->value -
               $this->_pageDictionary->MediaBox->items[1]->value;
    }

    /**
     * Return the width of this page in points.
     *
     * @return float
     */
    public function getWidth()
    {
        return $this->_pageDictionary->MediaBox->items[2]->value -
               $this->_pageDictionary->MediaBox->items[0]->value;
    }

     /**
     * Close the path by drawing a straight line back to it's beginning.
     *
     * @throws \Zend\Pdf\Exception
     * @return \Zend\Pdf\Page
     */
    public function pathClose()
    {
        /** @todo implementation */
        return $this;
    }

    /**
     * Continue the open path in a straight line to the specified position.
     *
     * @param float $x  - the X co-ordinate to move to
     * @param float $y  - the Y co-ordinate to move to
     * @return \Zend\Pdf\Page
     */
    public function pathLine($x, $y)
    {
        /** @todo implementation */
        return $this;
    }

    /**
     * Start a new path at the specified position. If a path has already been started,
     * move the cursor without drawing a line.
     *
     * @param float $x  - the X co-ordinate to move to
     * @param float $y  - the Y co-ordinate to move to
     * @return \Zend\Pdf\Page
     */
    public function pathMove($x, $y)
    {
        /** @todo implementation */
        return $this;
    }

    /**
     * Writes the raw data to the page's content stream.
     *
     * Be sure to consult the PDF reference to ensure your syntax is correct. No
     * attempt is made to ensure the validity of the stream data.
     *
     * @param string $data
     * @param string $procSet (optional) Name of ProcSet to add.
     * @return \Zend\Pdf\Page
     */
    public function rawWrite($data, $procSet = null)
    {
        if (! empty($procSet)) {
            $this->_addProcSet($procSet);
        }
        $this->_contents .= $data;

        return $this;
    }

    /**
     * Rotate the page
     *
     * @param float $x        X coordinate of the rotation point
     * @param float $y        Y coordinate of the rotation point
     * @param float $angle    Angle of rotation in radians
     * @return \Zend\Pdf\Page Fluid Interface
     */
    public function rotate($x, $y, $angle)
    {
        $cos  = new InternalType\NumericObject(cos($angle));
        $sin  = new InternalType\NumericObject(sin($angle));
        $mSin = new InternalType\NumericObject(-$sin->value);

        $xObj = new InternalType\NumericObject($x);
        $yObj = new InternalType\NumericObject($y);

        $mXObj = new InternalType\NumericObject(-$x);
        $mYObj = new InternalType\NumericObject(-$y);


        $this->_addProcSet('PDF');
        $this->_contents .= '1 0 0 1 ' . $xObj->toString() . ' ' . $yObj->toString() . " cm\n"
                         .  $cos->toString() . ' ' . $sin->toString() . ' ' . $mSin->toString() . ' ' . $cos->toString() . " 0 0 cm\n"
                         .  '1 0 0 1 ' . $mXObj->toString() . ' ' . $mYObj->toString() . " cm\n";

        return $this;
    }

    /**
     * Scale coordination system.
     *
     * @param float $xScale - X dimention scale factor
     * @param float $yScale - Y dimention scale factor
     * @return \Zend\Pdf\Page
     */
    public function scale($xScale, $yScale)
    {
        $xScaleObj = new InternalType\NumericObject($xScale);
        $yScaleObj = new InternalType\NumericObject($yScale);

        $this->_addProcSet('PDF');
        $this->_contents .= $xScaleObj->toString() . ' 0 0 ' . $yScaleObj->toString() . " 0 0 cm\n";

        return $this;
    }

    /**
     * Translate coordination system.
     *
     * @param float $xShift - X coordinate shift
     * @param float $yShift - Y coordinate shift
     * @return \Zend\Pdf\Page
     */
    public function translate($xShift, $yShift)
    {
        $xShiftObj = new InternalType\NumericObject($xShift);
        $yShiftObj = new InternalType\NumericObject($yShift);

        $this->_addProcSet('PDF');
        $this->_contents .= '1 0 0 1 ' . $xShiftObj->toString() . ' ' . $yShiftObj->toString() . " cm\n";

        return $this;
    }

    /**
     * Translate coordination system.
     *
     * @param float $x  - the X co-ordinate of axis skew point
     * @param float $y  - the Y co-ordinate of axis skew point
     * @param float $xAngle - X axis skew angle
     * @param float $yAngle - Y axis skew angle
     * @return \Zend\Pdf\Page
     */
    public function skew($x, $y, $xAngle, $yAngle)
    {
        $tanXObj = new InternalType\NumericObject(tan($xAngle));
        $tanYObj = new InternalType\NumericObject(-tan($yAngle));

        $xObj = new InternalType\NumericObject($x);
        $yObj = new InternalType\NumericObject($y);

        $mXObj = new InternalType\NumericObject(-$x);
        $mYObj = new InternalType\NumericObject(-$y);

        $this->_addProcSet('PDF');
        $this->_contents .= '1 0 0 1 ' . $xObj->toString() . ' ' . $yObj->toString() . " cm\n"
                         .  '1 ' . $tanXObj->toString() . ' ' . $tanYObj->toString() . " 1 0 0 cm\n"
                         .  '1 0 0 1 ' . $mXObj->toString() . ' ' . $mYObj->toString() . " cm\n";

        return $this;
    }
}

