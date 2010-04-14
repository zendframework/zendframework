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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\Outline;
use Zend\PDF;
use Zend\PDF\Action;
use Zend\PDF\Destination;
use Zend\PDF\InternalStructure;
use Zend\PDF\InternalType;
use Zend\PDF\ObjectFactory;

/**
 * PDF outline representation class
 *
 * @todo Implement an ability to associate an outline item with a structure element (PDF 1.3 feature)
 *
 * @uses       SplObjectStorage
 * @uses       \Zend\PDF\Action
 * @uses       \Zend\PDF\Destination
 * @uses       \Zend\PDF\InternalType
 * @uses       \Zend\PDF\InternalStructure
 * @uses       \Zend\PDF\Exception
 * @uses       \Zend\PDF\ObjectFactory
 * @uses       \Zend\PDF\Outline\AbstractOutline
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Outline
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @var \Zend\PDF\Color\RGB
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
     * @var \Zend\PDF\InternalStructure\NavigationTarget
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
     * @return \Zend\PDF\Outline\AbstractOutline
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
     * @return \Zend\PDF\Outline\AbstractOutline
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
     * @return \Zend\PDF\Outline\AbstractOutline
     */
    public function setIsBold($isBold)
    {
        $this->_bold = $isBold;
        return $this;
    }


    /**
     * Get outline text color.
     *
     * @return \Zend\PDF\Color\RGB
     */
    public function getColor()
    {
        return $this->_color;
    }

    /**
     * Set outline text color.
     * (null means default color which is black)
     *
     * @param \Zend\PDF\Color\RGB $color
     * @return \Zend\PDF\Outline\AbstractOutline
     */
    public function setColor(PDF\Color\RGB $color)
    {
        $this->_color = $color;
        return $this;
    }

    /**
     * Get outline target.
     *
     * @return \Zend\PDF\InternalStructure\NavigationTarget
     */
    public function getTarget()
    {
        return $this->_target;
    }

    /**
     * Set outline target.
     * Null means no target
     *
     * @param \Zend\PDF\InternalStructure\NavigationTarget|string $target
     * @return \Zend\PDF\Outline\AbstractOutline
     * @throws \Zend\PDF\Exception
     */
    public function setTarget($target = null)
    {
        if (is_string($target)) {
            $target = new PDF\Destination\Named($target);
        }

        if ($target === null  ||  $target instanceof InternalStructure\NavigationTarget) {
            $this->_target = $target;
        } else {
            throw new PDF\Exception('Outline target has to be \Zend\PDF\Destination or \Zend\PDF\Action object or string');
        }

        return $this;
    }


    /**
     * Object constructor
     *
     * @param array $options
     * @throws \Zend\PDF\Exception
     */
    public function __construct($options = array())
    {
        if (!isset($options['title'])) {
            throw new PDF\Exception('Title parameter is required.');
        }

        $this->setOptions($options);
    }

    /**
     * Dump Outline and its child outlines into PDF structures
     *
     * Returns dictionary indirect object or reference
     *
     * @internal
     * @param \Zend\PDF\ObjectFactory\ObjectFactory    $factory object factory for newly created indirect objects
     * @param boolean $updateNavigation  Update navigation flag
     * @param \Zend\PDF\InternalType\AbstractTypeObject $parent   Parent outline dictionary reference
     * @param \Zend\PDF\InternalType\AbstractTypeObject $prev     Previous outline dictionary reference
     * @param SplObjectStorage $processedOutlines  List of already processed outlines
     * @return \Zend\PDF\InternalType\AbstractTypeObject
     * @throws \Zend\PDF\Exception
     */
    public function dumpOutline(ObjectFactory\ObjectFactoryInterface $factory,
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
        } else if ($target instanceof PDF\Destination\AbstractDestination) {
            $outlineDictionary->Dest = $target->getResource();
        } else if ($target instanceof Action\AbstractAction) {
            $outlineDictionary->A    = $target->getResource();
        } else {
            throw new PDF\Exception('Outline target has to be \Zend\PDF\Destination, \Zend\PDF\Action object or null');
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
                throw new PDF\Exception('Outlines cyclyc reference is detected.');
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
