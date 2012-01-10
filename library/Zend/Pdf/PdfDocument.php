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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf;
use Zend\Pdf\Exception;

use Zend\Memory;

/**
 * General entity which describes PDF document.
 * It implements document abstraction with a document level operations.
 *
 * Class is used to create new PDF document or load existing document.
 * See details in a class constructor description
 *
 * Class agregates document level properties and entities (pages, bookmarks,
 * document level actions, attachments, form object, etc)
 *
 * @uses       \Zend\Memory\MemoryManager
 * @uses       \Zend\Pdf\Color
 * @uses       \Zend\Pdf\Exception
 * @uses       \Zend\Pdf\Font
 * @uses       \Zend\Pdf\Image
 * @uses       \Zend\Pdf\InternalStructure
 * @uses       \Zend\Pdf\InternalType
 * @uses       \Zend\Pdf\ObjectFactory
 * @uses       \Zend\Pdf\Outline
 * @uses       \Zend\Pdf\Page
 * @uses       \Zend\Pdf\PdfParser\StructureParser
 * @uses       \Zend\Pdf\Resource\Font\Extracted
 * @uses       \Zend\Pdf\Style
 * @uses       \Zend\Pdf\Trailer
 * @uses       \Zend\Pdf\Util
 * @category   Zend
 * @package    Zend_PDF
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PdfDocument
{
    /**** Class Constants ****/

    /**
     * Version number of generated PDF documents.
     */
    const PDF_VERSION = '1.4';

    /**
     * PDF file header.
     */
    const PDF_HEADER  = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";



    /**
     * Pages collection
     *
     * @todo implement it as a class, which supports ArrayAccess and Iterator interfaces,
     *       to provide incremental parsing and pages tree updating.
     *       That will give good performance and memory (PDF size) benefits.
     *
     * @var array   - array of \Zend\Pdf\Page object
     */
    public $pages = array();

    /**
     * Document properties
     *
     * It's an associative array with PDF meta information, values may
     * be string, boolean or float.
     * Returned array could be used directly to access, add, modify or remove
     * document properties.
     *
     * Standard document properties: Title (must be set for PDF/X documents), Author,
     * Subject, Keywords (comma separated list), Creator (the name of the application,
     * that created document, if it was converted from other format), Trapped (must be
     * true, false or null, can not be null for PDF/X documents)
     *
     * @var array
     */
    public $properties = array();

    /**
     * Original properties set.
     *
     * Used for tracking properties changes
     *
     * @var array
     */
    protected $_originalProperties = array();

    /**
     * Document level javascript
     *
     * @var string
     */
    protected $_javaScript = null;

    /**
     * Document named destinations or "GoTo..." actions, used to refer
     * document parts from outside PDF
     *
     * @var array   - array of \Zend\Pdf\InternalStructure\NavigationTarget objects
     */
    protected $_namedTargets = array();

    /**
     * Document outlines
     *
     * @var array - array of \Zend\Pdf\Outline\AbstractOutline objects
     */
    public $outlines = array();

    /**
     * Original document outlines list
     * Used to track outlines update
     *
     * @var array - array of \Zend\Pdf\Outline\AbstractOutline objects
     */
    protected $_originalOutlines = array();

    /**
     * Original document outlines open elements count
     * Used to track outlines update
     *
     * @var integer
     */
    protected $_originalOpenOutlinesCount = 0;

    /**
     * PDF trailer (last or just created)
     *
     * @var \Zend\Pdf\Trailer\AbstractTrailer
     */
    protected $_trailer = null;

    /**
     * PDF objects factory.
     *
     * @var \Zend\Pdf\ObjectFactory
     */
    protected $_objFactory = null;

    /**
     * Memory manager for stream objects
     *
     * @var \Zend\Memory\MemoryManager|null
     */
    protected static $_memoryManager = null;

    /**
     * PDF file parser.
     * It's not used, but has to be destroyed only with Zend_PDF object
     *
     * @var \Zend\Pdf\PdfParser\StructureParser
     */
    protected $_parser;


    /**
     * List of inheritable attributesfor pages tree
     *
     * @var array
     */
    protected static $_inheritableAttributes = array('Resources', 'MediaBox', 'CropBox', 'Rotate');

    /**
     * Request used memory manager
     *
     * @return Zend\Memory\MemoryManager
     */
    static public function getMemoryManager()
    {
        if (self::$_memoryManager === null) {
            self::$_memoryManager = new Memory\MemoryManager();
        }

        return self::$_memoryManager;
    }

    /**
     * Set user defined memory manager
     *
     * @param Zend\Memory\MemoryManager $memoryManager
     */
    static public function setMemoryManager(Memory\MemoryManager $memoryManager)
    {
        self::$_memoryManager = $memoryManager;
    }


    /**
     * Create new PDF document from a $source string
     *
     * @param string $source
     * @param integer $revision
     * @return \Zend\Pdf\PdfDocument
     */
    public static function parse(&$source = null, $revision = null)
    {
        return new self($source, $revision);
    }

    /**
     * Load PDF document from a file
     *
     * @param string $source
     * @param integer $revision
     * @return \Zend\Pdf\PdfDocument
     */
    public static function load($source = null, $revision = null)
    {
        return new self($source, $revision, true);
    }

    /**
     * Render PDF document and save it.
     *
     * If $updateOnly is true, then it only appends new section to the end of file.
     *
     * @param string $filename
     * @param boolean $updateOnly
     * @throws \Zend\Pdf\Exception
     */
    public function save($filename, $updateOnly = false)
    {
        if (($file = @fopen($filename, $updateOnly ? 'ab':'wb')) === false ) {
            throw new Exception\IOException( "Can not open '$filename' file for writing." );
        }

        $this->render($updateOnly, $file);

        fclose($file);
    }

    /**
     * Creates or loads PDF document.
     *
     * If $source is null, then it creates a new document.
     *
     * If $source is a string and $load is false, then it loads document
     * from a binary string.
     *
     * If $source is a string and $load is true, then it loads document
     * from a file.

     * $revision used to roll back document to specified version
     * (0 - currtent version, 1 - previous version, 2 - ...)
     *
     * @param string  $source - PDF file to load
     * @param integer $revision
     * @throws \Zend\Pdf\Exception
     * @return \Zend\Pdf\PdfDocument
     */
    public function __construct($source = null, $revision = null, $load = false)
    {
        $this->_objFactory = ObjectFactory::createFactory(1);

        if ($source !== null) {
            $this->_parser           = new PdfParser\StructureParser($source, $this->_objFactory, $load);
            $this->_pdfHeaderVersion = $this->_parser->getPDFVersion();
            $this->_trailer          = $this->_parser->getTrailer();
            if ($this->_trailer->Encrypt !== null) {
                throw new Exception\NotImplementedException('Encrypted document modification is not supported');
            }
            if ($revision !== null) {
                $this->rollback($revision);
            } else {
                $this->_loadPages($this->_trailer->Root->Pages);
            }

            $this->_loadNamedDestinations($this->_trailer->Root, $this->_parser->getPDFVersion());
            $this->_loadOutlines($this->_trailer->Root);

            if ($this->_trailer->Info !== null) {
                $this->properties = $this->_trailer->Info->toPhp();

                if (isset($this->properties['Trapped'])) {
                    switch ($this->properties['Trapped']) {
                        case 'True':
                            $this->properties['Trapped'] = true;
                            break;

                        case 'False':
                            $this->properties['Trapped'] = false;
                            break;

                        case 'Unknown':
                            $this->properties['Trapped'] = null;
                            break;

                        default:
                            // Wrong property value
                            // Do nothing
                            break;
                    }
                }

                $this->_originalProperties = $this->properties;
            }
        } else {
            $this->_pdfHeaderVersion = self::PDF_VERSION;

            $trailerDictionary = new InternalType\DictionaryObject();

            /**
             * Document id
             */
            $docId = md5(uniqid(rand(), true));   // 32 byte (128 bit) identifier
            $docIdLow  = substr($docId,  0, 16);  // first 16 bytes
            $docIdHigh = substr($docId, 16, 16);  // second 16 bytes

            $trailerDictionary->ID = new InternalType\ArrayObject();
            $trailerDictionary->ID->items[] = new InternalType\BinaryStringObject($docIdLow);
            $trailerDictionary->ID->items[] = new InternalType\BinaryStringObject($docIdHigh);

            $trailerDictionary->Size = new InternalType\NumericObject(0);

            $this->_trailer = new Trailer\Generated($trailerDictionary);

            /**
             * Document catalog indirect object.
             */
            $docCatalog = $this->_objFactory->newObject(new InternalType\DictionaryObject());
            $docCatalog->Type     = new InternalType\NameObject('Catalog');
            $docCatalog->Version  = new InternalType\NameObject(self::PDF_VERSION);
            $this->_trailer->Root = $docCatalog;

            /**
             * Pages container
             */
            $docPages = $this->_objFactory->newObject(new InternalType\DictionaryObject());
            $docPages->Type  = new InternalType\NameObject('Pages');
            $docPages->Kids  = new InternalType\ArrayObject();
            $docPages->Count = new InternalType\NumericObject(0);
            $docCatalog->Pages = $docPages;
        }
    }

    /**
     * Retrive number of revisions.
     *
     * @return integer
     */
    public function revisions()
    {
        $revisions = 1;
        $currentTrailer = $this->_trailer;

        while ($currentTrailer->getPrev() !== null && $currentTrailer->getPrev()->Root !== null ) {
            $revisions++;
            $currentTrailer = $currentTrailer->getPrev();
        }

        return $revisions++;
    }

    /**
     * Rollback document $steps number of revisions.
     * This method must be invoked before any changes, applied to the document.
     * Otherwise behavior is undefined.
     *
     * @param integer $steps
     */
    public function rollback($steps)
    {
        for ($count = 0; $count < $steps; $count++) {
            if ($this->_trailer->getPrev() !== null && $this->_trailer->getPrev()->Root !== null) {
                $this->_trailer = $this->_trailer->getPrev();
            } else {
                break;
            }
        }
        $this->_objFactory->setObjectCount($this->_trailer->Size->value);

        // Mark content as modified to force new trailer generation at render time
        $this->_trailer->Root->touch();

        $this->pages = array();
        $this->_loadPages($this->_trailer->Root->Pages);
    }


    /**
     * Load pages recursively
     *
     * @param \Zend\Pdf\InternalType\IndirectObjectReference $pages
     * @param array|null $attributes
     */
    protected function _loadPages(InternalType\IndirectObjectReference $pages, $attributes = array())
    {
        if ($pages->getType() != InternalType\AbstractTypeObject::TYPE_DICTIONARY) {
            throw new Exception\CorruptedPdfException('Wrong argument');
        }

        foreach ($pages->getKeys() as $property) {
            if (in_array($property, self::$_inheritableAttributes)) {
                $attributes[$property] = $pages->$property;
                $pages->$property = null;
            }
        }


        foreach ($pages->Kids->items as $child) {
            if ($child->Type->value == 'Pages') {
                $this->_loadPages($child, $attributes);
            } else if ($child->Type->value == 'Page') {
                foreach (self::$_inheritableAttributes as $property) {
                    if ($child->$property === null && array_key_exists($property, $attributes)) {
                        /**
                         * Important note.
                         * If any attribute or dependant object is an indirect object, then it's still
                         * shared between pages.
                         */
                        if ($attributes[$property] instanceof InternalType\IndirectObject  ||
                            $attributes[$property] instanceof InternalType\IndirectObjectReference) {
                            $child->$property = $attributes[$property];
                        } else {
                            $child->$property = $this->_objFactory->newObject($attributes[$property]);
                        }
                    }
                }

                $this->pages[] = new Page($child, $this->_objFactory);
            }
        }
    }

    /**
     * Load named destinations recursively
     *
     * @param \Zend\Pdf\InternalType\IndirectObjectReference $root Document catalog entry
     * @param string $pdfHeaderVersion
     * @throws \Zend\Pdf\Exception
     */
    protected function _loadNamedDestinations(InternalType\IndirectObjectReference $root, $pdfHeaderVersion)
    {
        if ($root->Version !== null  &&  version_compare($root->Version->value, $pdfHeaderVersion, '>')) {
            $versionIs_1_2_plus = version_compare($root->Version->value,    '1.1', '>');
        } else {
            $versionIs_1_2_plus = version_compare($pdfHeaderVersion, '1.1', '>');
        }

        if ($versionIs_1_2_plus) {
            // PDF version is 1.2+
            // Look for Destinations structure at Name dictionary
            if ($root->Names !== null  &&  $root->Names->Dests !== null) {
                foreach (new InternalStructure\NameTree($root->Names->Dests) as $name => $destination) {
                    $this->_namedTargets[$name] = InternalStructure\NavigationTarget::load($destination);
                }
            }
        } else {
            // PDF version is 1.1 (or earlier)
            // Look for Destinations sructure at Dest entry of document catalog
            if ($root->Dests !== null) {
                if ($root->Dests->getType() != InternalType\AbstractTypeObject::TYPE_DICTIONARY) {
                    throw new Exception\CorruptedPdfException('Document catalog Dests entry must be a dictionary.');
                }

                foreach ($root->Dests->getKeys() as $destKey) {
                    $this->_namedTargets[$destKey] = InternalStructure\NavigationTarget::load($root->Dests->$destKey);
                }
            }
        }
    }

    /**
     * Load outlines recursively
     *
     * @param \Zend\Pdf\InternalType\IndirectObjectReference $root Document catalog entry
     */
    protected function _loadOutlines(InternalType\IndirectObjectReference $root)
    {
        if ($root->Outlines === null) {
            return;
        }

        if ($root->Outlines->getType() != InternalType\AbstractTypeObject::TYPE_DICTIONARY) {
            throw new Exception\CorruptedPdfException('Document catalog Outlines entry must be a dictionary.');
        }

        if ($root->Outlines->Type !== null  &&  $root->Outlines->Type->value != 'Outlines') {
            throw new Exception\CorruptedPdfException('Outlines Type entry must be an \'Outlines\' string.');
        }

        if ($root->Outlines->First === null) {
            return;
        }

        $outlineDictionary = $root->Outlines->First;
        $processedDictionaries = new \SplObjectStorage();
        while ($outlineDictionary !== null  &&  !$processedDictionaries->contains($outlineDictionary)) {
            $processedDictionaries->attach($outlineDictionary);

            $this->outlines[] = new Outline\Loaded($outlineDictionary);

            $outlineDictionary = $outlineDictionary->Next;
        }

        $this->_originalOutlines = $this->outlines;

        if ($root->Outlines->Count !== null) {
            $this->_originalOpenOutlinesCount = $root->Outlines->Count->value;
        }
    }

    /**
     * Organize pages to the pages tree structure.
     *
     * @todo atomatically attach page to the document, if it's not done yet.
     * @todo check, that page is attached to the current document
     *
     * @todo Dump pages as a balanced tree instead of a plain set.
     */
    protected function _dumpPages()
    {
        $root = $this->_trailer->Root;
        $pagesContainer = $root->Pages;

        $pagesContainer->touch();
        $pagesContainer->Kids->items = array();

        foreach ($this->pages as $page ) {
            $page->render($this->_objFactory);

            $pageDictionary = $page->getPageDictionary();
            $pageDictionary->touch();
            $pageDictionary->Parent = $pagesContainer;

            $pagesContainer->Kids->items[] = $pageDictionary;
        }

        $this->_refreshPagesHash();

        $pagesContainer->Count->touch();
        $pagesContainer->Count->value = count($this->pages);


        // Refresh named destinations list
        foreach ($this->_namedTargets as $name => $namedTarget) {
            if ($namedTarget instanceof Destination\Explicit) {
                // Named target is an explicit destination
                if ($this->resolveDestination($namedTarget, false) === null) {
                    unset($this->_namedTargets[$name]);
                }
            } else if ($namedTarget instanceof Action\AbstractAction) {
                // Named target is an action
                if ($this->_cleanUpAction($namedTarget, false) === null) {
                    // Action is a GoTo action with an unresolved destination
                    unset($this->_namedTargets[$name]);
                }
            } else {
                throw new Exception\RuntimeException('Wrong type of named targed (\'' . get_class($namedTarget) . '\').');
            }
        }

        // Refresh outlines
        $iterator = new \RecursiveIteratorIterator(new Util\RecursivelyIteratableObjectsContainer($this->outlines), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $outline) {
            $target = $outline->getTarget();

            if ($target !== null) {
                if ($target instanceof Destination\AbstractDestination) {
                    // Outline target is a destination
                    if ($this->resolveDestination($target, false) === null) {
                        $outline->setTarget(null);
                    }
                } else if ($target instanceof Action\AbstractAction) {
                    // Outline target is an action
                    if ($this->_cleanUpAction($target, false) === null) {
                        // Action is a GoTo action with an unresolved destination
                        $outline->setTarget(null);
                    }
                } else {
                    throw new Exception\RuntimeException('Wrong outline target.');
                }
            }
        }

        $openAction = $this->getOpenAction();
        if ($openAction !== null) {
            if ($openAction instanceof Action\AbstractAction) {
                // OpenAction is an action
                if ($this->_cleanUpAction($openAction, false) === null) {
                    // Action is a GoTo action with an unresolved destination
                    $this->setOpenAction(null);
                }
            } else if ($openAction instanceof Destination\AbstractDestination) {
                // OpenAction target is a destination
                if ($this->resolveDestination($openAction, false) === null) {
                    $this->setOpenAction(null);
                }
            } else {
                throw new Exception\RuntimeException('OpenAction has to be either PDF Action or Destination.');
            }
        }
    }

    /**
     * Dump named destinations
     *
     * @todo Create a balanced tree instead of plain structure.
     */
    protected function _dumpNamedDestinations()
    {
        ksort($this->_namedTargets, SORT_STRING);

        $destArrayItems = array();
        foreach ($this->_namedTargets as $name => $destination) {
            $destArrayItems[] = new InternalType\StringObject($name);

            if ($destination instanceof InternalStructure\NavigationTarget) {
                $destArrayItems[] = $destination->getResource();
            } else {
                throw new Exception\RuntimeException('PDF named destinations must be a \Zend\Pdf\InternalStructure\NavigationTarget object.');
            }
        }
        $destArray = $this->_objFactory->newObject(new InternalType\ArrayObject($destArrayItems));

        $DestTree = $this->_objFactory->newObject(new InternalType\DictionaryObject());
        $DestTree->Names = $destArray;

        $root = $this->_trailer->Root;

        if ($root->Names === null) {
            $root->touch();
            $root->Names = $this->_objFactory->newObject(new InternalType\DictionaryObject());
        } else {
            $root->Names->touch();
        }
        $root->Names->Dests = $DestTree;
    }

    /**
     * Dump outlines recursively
     */
    protected function _dumpOutlines()
    {
        $root = $this->_trailer->Root;

        if ($root->Outlines === null) {
            if (count($this->outlines) == 0) {
                return;
            } else {
                $root->Outlines = $this->_objFactory->newObject(new InternalType\DictionaryObject());
                $root->Outlines->Type = new InternalType\NameObject('Outlines');
                $updateOutlinesNavigation = true;
            }
        } else {
            $updateOutlinesNavigation = false;
            if (count($this->_originalOutlines) != count($this->outlines)) {
                // If original and current outlines arrays have different size then outlines list was updated
                $updateOutlinesNavigation = true;
            } else if ( !(array_keys($this->_originalOutlines) === array_keys($this->outlines)) ) {
                // If original and current outlines arrays have different keys (with a glance to an order) then outlines list was updated
                $updateOutlinesNavigation = true;
            } else {
                foreach ($this->outlines as $key => $outline) {
                    if ($this->_originalOutlines[$key] !== $outline) {
                        $updateOutlinesNavigation = true;
                    }
                }
            }
        }

        $lastOutline = null;
        $openOutlinesCount = 0;
        if ($updateOutlinesNavigation) {
            $root->Outlines->touch();
            $root->Outlines->First = null;

            foreach ($this->outlines as $outline) {
                if ($lastOutline === null) {
                    // First pass. Update Outlines dictionary First entry using corresponding value
                    $lastOutline = $outline->dumpOutline($this->_objFactory, $updateOutlinesNavigation, $root->Outlines);
                    $root->Outlines->First = $lastOutline;
                } else {
                    // Update previous outline dictionary Next entry (Prev is updated within dumpOutline() method)
                    $currentOutlineDictionary = $outline->dumpOutline($this->_objFactory, $updateOutlinesNavigation, $root->Outlines, $lastOutline);
                    $lastOutline->Next = $currentOutlineDictionary;
                    $lastOutline       = $currentOutlineDictionary;
                }
                $openOutlinesCount += $outline->openOutlinesCount();
            }

            $root->Outlines->Last  = $lastOutline;
        } else {
            foreach ($this->outlines as $outline) {
                $lastOutline = $outline->dumpOutline($this->_objFactory, $updateOutlinesNavigation, $root->Outlines, $lastOutline);
                $openOutlinesCount += $outline->openOutlinesCount();
            }
        }

        if ($openOutlinesCount != $this->_originalOpenOutlinesCount) {
            $root->Outlines->touch;
            $root->Outlines->Count = new InternalType\NumericObject($openOutlinesCount);
        }
    }

    /**
     * Create page object, attached to the PDF document.
     * Method signatures:
     *
     * 1. Create new page with a specified pagesize.
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new \Zend\Pdf\Page(string $pagesize);
     * ---------------------------------------------------------
     *
     * 2. Create new page with a specified pagesize (in default user space units).
     *    If $factory is null then it will be created and page must be attached to the document to be
     *    included into output.
     * ---------------------------------------------------------
     * new \Zend\Pdf\Page(numeric $width, numeric $height);
     * ---------------------------------------------------------
     *
     * @param mixed $param1
     * @param mixed $param2
     * @return \Zend\Pdf\Page
     */
    public function newPage($param1, $param2 = null)
    {
        if ($param2 === null) {
            return new Page($param1, $this->_objFactory);
        } else {
            return new Page($param1, $param2, $this->_objFactory);
        }
    }

    /**
     * Return the document-level Metadata
     * or null Metadata stream is not presented
     *
     * @return string
     */
    public function getMetadata()
    {
        if ($this->_trailer->Root->Metadata !== null) {
            return $this->_trailer->Root->Metadata->value;
        } else {
            return null;
        }
    }

    /**
     * Sets the document-level Metadata (mast be valid XMP document)
     *
     * @param string $metadata
     */
    public function setMetadata($metadata)
    {
        $metadataObject = $this->_objFactory->newStreamObject($metadata);
        $metadataObject->dictionary->Type    = new InternalType\NameObject('Metadata');
        $metadataObject->dictionary->Subtype = new InternalType\NameObject('XML');

        $this->_trailer->Root->Metadata = $metadataObject;
        $this->_trailer->Root->touch();
    }

    /**
     * Return the document-level JavaScript
     * or null if there is no JavaScript for this document
     *
     * @return string
     */
    public function getJavaScript()
    {
        return $this->_javaScript;
    }

    /**
     * Get open Action
     * Returns \Zend\Pdf\InternalStructure\NavigationTarget
     * (\Zend\Pdf\Destination\AbstractDestination or \Zend\Pdf\Action\AbstractAction object)
     *
     * @return \Zend\Pdf\InternalStructure\NavigationTarget
     */
    public function getOpenAction()
    {
        if ($this->_trailer->Root->OpenAction !== null) {
            return InternalStructure\NavigationTarget::load($this->_trailer->Root->OpenAction);
        } else {
            return null;
        }
    }

    /**
     * Set open Action which is actually \Zend\Pdf\Destination\AbstractDestination or
     * \Zend\Pdf\Action\AbstractAction object
     *
     * @param \Zend\Pdf\InternalStructure\NavigationTarget $openAction
     * @returns Zend_PDF
     */
    public function setOpenAction(InternalStructure\NavigationTarget $openAction = null)
    {
        $root = $this->_trailer->Root;
        $root->touch();

        if ($openAction === null) {
            $root->OpenAction = null;
        } else {
            $root->OpenAction = $openAction->getResource();

            if ($openAction instanceof Action\AbstractAction)  {
                $openAction->dumpAction($this->_objFactory);
            }
        }

        return $this;
    }

    /**
     * Return an associative array containing all the named destinations (or GoTo actions) in the PDF.
     * Named targets can be used to reference from outside
     * the PDF, ex: 'http://www.something.com/mydocument.pdf#MyAction'
     *
     * @return array
     */
    public function getNamedDestinations()
    {
        return $this->_namedTargets;
    }

    /**
     * Return specified named destination
     *
     * @param string $name
     * @return \Zend\Pdf\Destination\Explicit|\Zend\Pdf\Action\GoToAction
     */
    public function getNamedDestination($name)
    {
        if (isset($this->_namedTargets[$name])) {
            return $this->_namedTargets[$name];
        } else {
            return null;
        }
    }

    /**
     * Set specified named destination
     *
     * @param string $name
     * @param \Zend\Pdf\Destination\Explicit|\Zend\Pdf\Action\GoToAction $target
     */
    public function setNamedDestination($name, $destination = null)
    {
        if ($destination !== null  &&
            !$destination instanceof Action\GoToAction  &&
            !$destination instanceof Destination\Explicit) {
            throw new Exception\InvalidArgumentException('PDF named destination must refer an explicit destination or a GoTo PDF action.');
        }

        if ($destination !== null) {
           $this->_namedTargets[$name] = $destination;
        } else {
            unset($this->_namedTargets[$name]);
        }
    }

    /**
     * Pages collection hash:
     * <page dictionary object hash id> => \Zend\Pdf\Page
     *
     * @var SplObjectStorage
     */
    protected $_pageReferences = null;

    /**
     * Pages collection hash:
     * <page number> => \Zend\Pdf\Page
     *
     * @var array
     */
    protected $_pageNumbers = null;

    /**
     * Refresh page collection hashes
     *
     * @return \Zend\Pdf\PdfDocument
     */
    protected function _refreshPagesHash()
    {
        $this->_pageReferences = array();
        $this->_pageNumbers    = array();
        $count = 1;
        foreach ($this->pages as $page) {
            $pageDictionaryHashId = spl_object_hash($page->getPageDictionary()->getObject());
            $this->_pageReferences[$pageDictionaryHashId] = $page;
            $this->_pageNumbers[$count++]                 = $page;
        }

        return $this;
    }

    /**
     * Resolve destination.
     *
     * Returns \Zend\Pdf\Page page object or null if destination is not found within PDF document.
     *
     * @param \Zend\Pdf\Destination\AbstractDestination $destination  Destination to resolve
     * @param boolean $refreshPagesHash  Refresh page collection hashes before processing
     * @return \Zend\Pdf\Page|null
     * @throws \Zend\Pdf\Exception
     */
    public function resolveDestination(Destination\AbstractDestination $destination, $refreshPageCollectionHashes = true)
    {
        if ($this->_pageReferences === null  ||  $refreshPageCollectionHashes) {
            $this->_refreshPagesHash();
        }

        if ($destination instanceof Destination\Named) {
            if (!isset($this->_namedTargets[$destination->getName()])) {
                return null;
            }
            $destination = $this->getNamedDestination($destination->getName());

            if ($destination instanceof Action\AbstractAction) {
                if (!$destination instanceof Action\GoToAction) {
                    return null;
                }
                $destination = $destination->getDestination();
            }

            if (!$destination instanceof Destination\Explicit) {
                throw new Exception\CorruptedPdfException('Named destination target has to be an explicit destination.');
            }
        }

        // Named target is an explicit destination
        $pageElement = $destination->getResource()->items[0];

        if ($pageElement->getType() == InternalType\AbstractTypeObject::TYPE_NUMERIC) {
            // Page reference is a PDF number
            if (!isset($this->_pageNumbers[$pageElement->value])) {
                return null;
            }

            return $this->_pageNumbers[$pageElement->value];
        }

        // Page reference is a PDF page dictionary reference
        $pageDictionaryHashId = spl_object_hash($pageElement->getObject());
        if (!isset($this->_pageReferences[$pageDictionaryHashId])) {
            return null;
        }
        return $this->_pageReferences[$pageDictionaryHashId];
    }

    /**
     * Walk through action and its chained actions tree and remove nodes
     * if they are GoTo actions with an unresolved target.
     *
     * Returns null if root node is deleted or updated action overwise.
     *
     * @todo Give appropriate name and make method public
     *
     * @param \Zend\Pdf\Action\AbstractAction $action
     * @param boolean $refreshPagesHash  Refresh page collection hashes before processing
     * @return \Zend\Pdf\Action\AbstractAction|null
     */
    protected function _cleanUpAction(Action\AbstractAction $action, $refreshPageCollectionHashes = true)
    {
        if ($this->_pageReferences === null  ||  $refreshPageCollectionHashes) {
            $this->_refreshPagesHash();
        }

        // Named target is an action
        if ($action instanceof Action\GoToAction  &&
            $this->resolveDestination($action->getDestination(), false) === null) {
            // Action itself is a GoTo action with an unresolved destination
            return null;
        }

        // Walk through child actions
        $iterator = new \RecursiveIteratorIterator($action, \RecursiveIteratorIterator::SELF_FIRST);

        $actionsToClean        = array();
        $deletionCandidateKeys = array();
        foreach ($iterator as $chainedAction) {
            if ($chainedAction instanceof Action\GoToAction  &&
                $this->resolveDestination($chainedAction->getDestination(), false) === null) {
                // Some child action is a GoTo action with an unresolved destination
                // Mark it as a candidate for deletion
                $actionsToClean[]        = $iterator->getSubIterator();
                $deletionCandidateKeys[] = $iterator->getSubIterator()->key();
            }
        }
        foreach ($actionsToClean as $id => $action) {
            unset($action->next[$deletionCandidateKeys[$id]]);
        }

        return $action;
    }

    /**
     * Extract fonts attached to the document
     *
     * returns array of \Zend\Pdf\Resource\Font\Extracted objects
     *
     * @return array
     * @throws \Zend\Pdf\Exception
     */
    public function extractFonts()
    {
        $fontResourcesUnique = array();
        foreach ($this->pages as $page) {
            $pageResources = $page->extractResources();

            if ($pageResources->Font === null) {
                // Page doesn't contain have any font reference
                continue;
            }

            $fontResources = $pageResources->Font;

            foreach ($fontResources->getKeys() as $fontResourceName) {
                $fontDictionary = $fontResources->$fontResourceName;

                if (! ($fontDictionary instanceof InternalType\IndirectObjectReference  ||
                       $fontDictionary instanceof InternalType\IndirectObject) ) {
                    throw new Exception\CorruptedPdfException('Font dictionary has to be an indirect object or object reference.');
                }

                $fontResourcesUnique[spl_object_hash($fontDictionary->getObject())] = $fontDictionary;
            }
        }

        $fonts = array();
        foreach ($fontResourcesUnique as $resourceId => $fontDictionary) {
            try {
                // Try to extract font
                $extractedFont = new Resource\Font\Extracted($fontDictionary);

                $fonts[$resourceId] = $extractedFont;
            } catch (Exception\CorruptedPdfException $e) {
                if ($e->getMessage() != 'Unsupported font type.') {
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
        $fontResourcesUnique = array();
        foreach ($this->pages as $page) {
            $pageResources = $page->extractResources();

            if ($pageResources->Font === null) {
                // Page doesn't contain have any font reference
                continue;
            }

            $fontResources = $pageResources->Font;

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
                } catch (Exception\CorruptedPdfException $e) {
                    if ($e->getMessage() != 'Unsupported font type.') {
                        throw $e;
                    }
                    // Continue searhing
                }
            }
        }

        return null;
    }

    /**
     * Render the completed PDF to a string.
     * If $newSegmentOnly is true, then only appended part of PDF is returned.
     *
     * @param boolean $newSegmentOnly
     * @param resource $outputStream
     * @return string
     * @throws \Zend\Pdf\Exception
     */
    public function render($newSegmentOnly = false, $outputStream = null)
    {
        // Save document properties if necessary
        if ($this->properties != $this->_originalProperties) {
            $docInfo = $this->_objFactory->newObject(new InternalType\DictionaryObject());

            foreach ($this->properties as $key => $value) {
                switch ($key) {
                    case 'Trapped':
                        switch ($value) {
                            case true:
                                $docInfo->$key = new InternalType\NameObject('True');
                                break;

                            case false:
                                $docInfo->$key = new InternalType\NameObject('False');
                                break;

                            case null:
                                $docInfo->$key = new InternalType\NameObject('Unknown');
                                break;

                            default:
                                throw new Exception\LogicException('Wrong Trapped document property vale: \'' . $value . '\'. Only true, false and null values are allowed.');
                                break;
                        }

                    case 'CreationDate':
                        // break intentionally omitted
                    case 'ModDate':
                        $docInfo->$key = new InternalType\StringObject((string)$value);
                        break;

                    case 'Title':
                        // break intentionally omitted
                    case 'Author':
                        // break intentionally omitted
                    case 'Subject':
                        // break intentionally omitted
                    case 'Keywords':
                        // break intentionally omitted
                    case 'Creator':
                        // break intentionally omitted
                    case 'Producer':
                        if (extension_loaded('mbstring') === true) {
                            $detected = mb_detect_encoding($value);
                            if ($detected !== 'ASCII') {
                                $value = chr(254) . chr(255) . mb_convert_encoding($value, 'UTF-16', $detected);
                            }
                        }
                        $docInfo->$key = new InternalType\StringObject((string)$value);
                        break;

                    default:
                        // Set property using PDF type based on PHP type
                        $docInfo->$key = InternalType\AbstractTypeObject::phpToPDF($value);
                        break;
                }
            }

            $this->_trailer->Info = $docInfo;
        }

        $this->_dumpPages();
        $this->_dumpNamedDestinations();
        $this->_dumpOutlines();

        // Check, that PDF file was modified
        // File is always modified by _dumpPages() now, but future implementations may eliminate this.
        if (!$this->_objFactory->isModified()) {
            if ($newSegmentOnly) {
                // Do nothing, return
                return '';
            }

            if ($outputStream === null) {
                return $this->_trailer->getPDFString();
            } else {
                $pdfData = $this->_trailer->getPDFString();
                while ( strlen($pdfData) > 0 && ($byteCount = fwrite($outputStream, $pdfData)) != false ) {
                    $pdfData = substr($pdfData, $byteCount);
                }

                return '';
            }
        }

        // offset (from a start of PDF file) of new PDF file segment
        $offset = $this->_trailer->getPDFLength();
        // Last Object number in a list of free objects
        $lastFreeObject = $this->_trailer->getLastFreeObject();

        // Array of cross-reference table subsections
        $xrefTable = array();
        // Object numbers of first objects in each subsection
        $xrefSectionStartNums = array();

        // Last cross-reference table subsection
        $xrefSection = array();
        // Dummy initialization of the first element (specail case - header of linked list of free objects).
        $xrefSection[] = 0;
        $xrefSectionStartNums[] = 0;
        // Object number of last processed PDF object.
        // Used to manage cross-reference subsections.
        // Initialized by zero (specail case - header of linked list of free objects).
        $lastObjNum = 0;

        if ($outputStream !== null) {
            if (!$newSegmentOnly) {
                $pdfData = $this->_trailer->getPDFString();
                while ( strlen($pdfData) > 0 && ($byteCount = fwrite($outputStream, $pdfData)) != false ) {
                    $pdfData = substr($pdfData, $byteCount);
                }
            }
        } else {
            $pdfSegmentBlocks = ($newSegmentOnly) ? array() : array($this->_trailer->getPDFString());
        }

        // Iterate objects to create new reference table
        foreach ($this->_objFactory->listModifiedObjects() as $updateInfo) {
            $objNum = $updateInfo->getObjNum();

            if ($objNum - $lastObjNum != 1) {
                // Save cross-reference table subsection and start new one
                $xrefTable[] = $xrefSection;
                $xrefSection = array();
                $xrefSectionStartNums[] = $objNum;
            }

            if ($updateInfo->isFree()) {
                // Free object cross-reference table entry
                $xrefSection[]  = sprintf("%010d %05d f \n", $lastFreeObject, $updateInfo->getGenNum());
                $lastFreeObject = $objNum;
            } else {
                // In-use object cross-reference table entry
                $xrefSection[]  = sprintf("%010d %05d n \n", $offset, $updateInfo->getGenNum());

                $pdfBlock = $updateInfo->getObjectDump();
                $offset += strlen($pdfBlock);

                if ($outputStream === null) {
                    $pdfSegmentBlocks[] = $pdfBlock;
                } else {
                    while ( strlen($pdfBlock) > 0 && ($byteCount = fwrite($outputStream, $pdfBlock)) != false ) {
                        $pdfBlock = substr($pdfBlock, $byteCount);
                    }
                }
            }
            $lastObjNum = $objNum;
        }
        // Save last cross-reference table subsection
        $xrefTable[] = $xrefSection;

        // Modify first entry (specail case - header of linked list of free objects).
        $xrefTable[0][0] = sprintf("%010d 65535 f \n", $lastFreeObject);

        $xrefTableStr = "xref\n";
        foreach ($xrefTable as $sectId => $xrefSection) {
            $xrefTableStr .= sprintf("%d %d \n", $xrefSectionStartNums[$sectId], count($xrefSection));
            foreach ($xrefSection as $xrefTableEntry) {
                $xrefTableStr .= $xrefTableEntry;
            }
        }

        $this->_trailer->Size->value = $this->_objFactory->getObjectCount();

        $pdfBlock = $xrefTableStr
                 .  $this->_trailer->toString()
                 . "startxref\n" . $offset . "\n"
                 . "%%EOF\n";

        $this->_objFactory->cleanEnumerationShiftCache();

        if ($outputStream === null) {
            $pdfSegmentBlocks[] = $pdfBlock;

            return implode('', $pdfSegmentBlocks);
        } else {
            while ( strlen($pdfBlock) > 0 && ($byteCount = fwrite($outputStream, $pdfBlock)) != false ) {
                $pdfBlock = substr($pdfBlock, $byteCount);
            }

            return '';
        }
    }


    /**
     * Set the document-level JavaScript
     *
     * @param string $javascript
     */
    public function setJavaScript($javascript)
    {
        $this->_javaScript = $javascript;
    }


    /**
     * Convert date to PDF format (it's close to ASN.1 (Abstract Syntax Notation
     * One) defined in ISO/IEC 8824).
     *
     * @todo This really isn't the best location for this method. It should
     *   probably actually exist as \Zend\Pdf\InternalType\Date or something like that.
     *
     * @todo Address the following E_STRICT issue:
     *   PHP Strict Standards:  date(): It is not safe to rely on the system's
     *   timezone settings. Please use the date.timezone setting, the TZ
     *   environment variable or the date_default_timezone_set() function. In
     *   case you used any of those methods and you are still getting this
     *   warning, you most likely misspelled the timezone identifier.
     *
     * @param integer $timestamp (optional) If omitted, uses the current time.
     * @return string
     */
    public static function pdfDate($timestamp = null)
    {
        if ($timestamp === null) {
            $date = date('\D\:YmdHisO');
        } else {
            $date = date('\D\:YmdHisO', $timestamp);
        }
        return substr_replace($date, '\'', -2, 0) . '\'';
    }
}
