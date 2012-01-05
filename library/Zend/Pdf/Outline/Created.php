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
 * @subpackage Zend_PDF_Outline
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Outline;
use Zend\Pdf\Exception;
use Zend\Pdf;
use Zend\Pdf\Action;
use Zend\Pdf\Destination;
use Zend\Pdf\InternalStructure;
use Zend\Pdf\InternalType;
use Zend\Pdf\ObjectFactory;

/**
 * PDF outline representation class
 *
 * @todo Implement an ability to associate an outline item with a structure element (PDF 1.3 feature)
 *
 * @uses       SplObjectStorage
 * @uses       \Zend\Pdf\Action
 * @uses       \Zend\Pdf\Destination
 * @uses       \Zend\Pdf\InternalType
 * @uses       \Zend\Pdf\InternalStructure
 * @uses       \Zend\Pdf\Exception
 * @uses       \Zend\Pdf\ObjectFactory
 * @uses       \Zend\Pdf\Outline\AbstractOutline
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Outline
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Created extends AbstractOutline
{
    /**
     * Outline title.
     *
     * @var string
     */
    protected $_title;

    /**
     * Color to be used for the outline entry’s text.

     * It uses the DeviceRGB color space for color representation.
     * Null means default value - black ([0.0 0.0 0.0] in RGB representation).
     *
     * @var \Zend\Pdf\Color\Rgb
     */
    protected $_color = null;

    /**
     * True if outline item is displayed in italic.
     * Default value is false.
     *
     * @var boolean
     */
    protected $_italic = false;

    /**
     * True if outline item is displayed in bold.
     * Default value is false.
     *
     * @var boolean
     */
    protected $_bold = false;

    /**
     * Target destination or action.
     * String means named destination
     *
     * Null means no target.
     *
     * @var \Zend\Pdf\InternalStructure\NavigationTarget
     */
    protected $_target = null;


    /**
     * Get outline title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set outline title
     *
     * @param string $title
     * @return \Zend\Pdf\Outline\AbstractOutline
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * Returns true if outline item is displayed in italic
     *
     * @return boolean
     */
    public function isItalic()
    {
        return $this->_italic;
    }

    /**
     * Sets 'isItalic' outline flag
     *
     * @param boolean $isItalic
     * @return \Zend\Pdf\Outline\AbstractOutline
     */
    public function setIsItalic($isItalic)
    {
        $this->_italic = $isItalic;
        return $this;
    }

    /**
     * Returns true if outline item is displayed in bold
     *
     * @return boolean
     */
    public function isBold()
    {
        return $this->_bold;
    }

    /**
     * Sets 'isBold' outline flag
     *
     * @param boolean $isBold
     * @return \Zend\Pdf\Outline\AbstractOutline
     */
    public function setIsBold($isBold)
    {
        $this->_bold = $isBold;
        return $this;
    }


    /**
     * Get outline text color.
     *
     * @return \Zend\Pdf\Color\Rgb
     */
    public function getColor()
    {
        return $this->_color;
    }

    /**
     * Set outline text color.
     * (null means default color which is black)
     *
     * @param \Zend\Pdf\Color\Rgb $color
     * @return \Zend\Pdf\Outline\AbstractOutline
     */
    public function setColor(Pdf\Color\Rgb $color)
    {
        $this->_color = $color;
        return $this;
    }

    /**
     * Get outline target.
     *
     * @return \Zend\Pdf\InternalStructure\NavigationTarget
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * Set outline target.
     * Null means no target
     *
     * @param \Zend\Pdf\InternalStructure\NavigationTarget|string $target
     * @return \Zend\Pdf\Outline\AbstractOutline
     * @throws \Zend\Pdf\Exception
     */
    public function setTarget($target = null)
    {
        if (is_string($target)) {
            $target = new Pdf\Destination\Named($target);
        }

        if ($target === null  ||  $target instanceof InternalStructure\NavigationTarget) {
            $this->_target = $target;
        } else {
            throw new Exception\InvalidArgumentException('Outline target has to be \Zend\Pdf\Destination or \Zend\Pdf\Action object or string');
        }

        return $this;
    }


    /**
     * Object constructor
     *
     * @param array $options
     * @throws \Zend\Pdf\Exception
     */
    public function __construct($options = array())
    {
        if (!isset($options['title'])) {
            throw new Exception\InvalidArgumentException('Title is required.');
        }

        $this->setOptions($options);
    }

    /**
     * Dump Outline and its child outlines into PDF structures
     *
     * Returns dictionary indirect object or reference
     *
     * @internal
     * @param \Zend\Pdf\ObjectFactory    $factory object factory for newly created indirect objects
     * @param boolean $updateNavigation  Update navigation flag
     * @param \Zend\Pdf\InternalType\AbstractTypeObject $parent   Parent outline dictionary reference
     * @param \Zend\Pdf\InternalType\AbstractTypeObject $prev     Previous outline dictionary reference
     * @param SplObjectStorage $processedOutlines  List of already processed outlines
     * @return \Zend\Pdf\InternalType\AbstractTypeObject
     * @throws \Zend\Pdf\Exception
     */
    public function dumpOutline(ObjectFactory $factory,
                                              $updateNavigation,
              InternalType\AbstractTypeObject $parent,
              InternalType\AbstractTypeObject $prev = null,
                            \SplObjectStorage $processedOutlines = null)
    {
        if ($processedOutlines === null) {
            $processedOutlines = new \SplObjectStorage();
        }
        $processedOutlines->attach($this);

        $outlineDictionary = $factory->newObject(new InternalType\DictionaryObject());

        $outlineDictionary->Title = new InternalType\StringObject($this->getTitle());

        $target = $this->getTarget();
        if ($target === null) {
            // Do nothing
        } else if ($target instanceof Pdf\Destination\AbstractDestination) {
            $outlineDictionary->Dest = $target->getResource();
        } else if ($target instanceof Action\AbstractAction) {
            $outlineDictionary->A    = $target->getResource();
        } else {
            throw new Exception\CorruptedPdfException('Outline target has to be \Zend\Pdf\Destination, \Zend\Pdf\Action object or null');
        }

        $color = $this->getColor();
        if ($color !== null) {
            $components = $color->getComponents();
            $colorComponentElements = array(new InternalType\NumericObject($components[0]),
                                            new InternalType\NumericObject($components[1]),
                                            new InternalType\NumericObject($components[2]));
            $outlineDictionary->C = new InternalType\ArrayObject($colorComponentElements);
        }

        if ($this->isItalic()  ||  $this->isBold()) {
            $outlineDictionary->F = new InternalType\NumericObject(($this->isItalic()? 1 : 0)  |   // Bit 1 - Italic
                                                                   ($this->isBold()?   2 : 0));    // Bit 2 - Bold
        }


        $outlineDictionary->Parent = $parent;
        $outlineDictionary->Prev   = $prev;

        $lastChild = null;
        foreach ($this->childOutlines as $childOutline) {
            if ($processedOutlines->contains($childOutline)) {
                throw new Exception\CorruptedPdfException('Outlines cyclyc reference is detected.');
            }

            if ($lastChild === null) {
                $lastChild = $childOutline->dumpOutline($factory, true, $outlineDictionary, null, $processedOutlines);
                $outlineDictionary->First = $lastChild;
            } else {
                $childOutlineDictionary = $childOutline->dumpOutline($factory, true, $outlineDictionary, $lastChild, $processedOutlines);
                $lastChild->Next = $childOutlineDictionary;
                $lastChild       = $childOutlineDictionary;
            }
        }
        $outlineDictionary->Last = $lastChild;

        if (count($this->childOutlines) != 0) {
            $outlineDictionary->Count = new InternalType\NumericObject(($this->isOpen()? 1 : -1)*count($this->childOutlines));
        }

        return $outlineDictionary;
    }
}
