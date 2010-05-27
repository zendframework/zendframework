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
use Zend\PDF\Color;
use Zend\PDF\Destination;
use Zend\PDF\InternalType;
use Zend\PDF\ObjectFactory;

/**
 * Traceable PDF outline representation class
 *
 * Instances of this class trace object update uperations. That allows to avoid outlines PDF tree update
 * which should be performed at each document update otherwise.
 *
 * @uses       SplObjectStorage
 * @uses       \Zend\PDF\Action
 * @uses       \Zend\PDF\Color
 * @uses       \Zend\PDF\Destination
 * @uses       \Zend\PDF\InternalType
 * @uses       \Zend\PDF\Exception
 * @uses       \Zend\PDF\Outline
 * @uses       \Zend\PDF\ObjectFactory
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Outline
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Loaded extends AbstractOutline
{
    /**
     * Outline dictionary object
     *
     * @var  \Zend\PDF\InternalType\DictionaryObject
     *     | \Zend\PDF\InternalType\IndirectObject
     *     | \Zend\PDF\InternalType\IndirectObjectReference
     */
    protected $_outlineDictionary;

    /**
     * original array of child outlines
     *
     * @var array
     */
    protected $_originalChildOutlines = array();

    /**
     * Get outline title.
     *
     * @return string
     * @throws \Zend\PDF\Exception
     */
    public function getTitle()
    {
        if ($this->_outlineDictionary->Title === null) {
            throw new PDF\Exception('Outline dictionary Title entry is required.');
        }
        return $this->_outlineDictionary->Title->value;
    }

    /**
     * Set outline title
     *
     * @param string $title
     * @return \Zend\PDF\Outline\AbstractOutline
     */
    public function setTitle($title)
    {
        $this->_outlineDictionary->Title->touch();
        $this->_outlineDictionary->Title = new InternalType\StringObject($title);
        return $this;
    }

    /**
     * Sets 'isOpen' outline flag
     *
     * @param boolean $isOpen
     * @return \Zend\PDF\Outline\AbstractOutline
     */
    public function setIsOpen($isOpen)
    {
        parent::setIsOpen($isOpen);

        if ($this->_outlineDictionary->Count === null) {
            // Do Nothing.
            return this;
        }

        $childrenCount = $this->_outlineDictionary->Count->value;
        $isOpenCurrentState = ($childrenCount > 0);
        if ($isOpen != $isOpenCurrentState) {
            $this->_outlineDictionary->Count->touch();
            $this->_outlineDictionary->Count->value = ($isOpen? 1 : -1)*abs($childrenCount);
        }

        return $this;
    }

    /**
     * Returns true if outline item is displayed in italic
     *
     * @return boolean
     */
    public function isItalic()
    {
        if ($this->_outlineDictionary->F === null) {
            return false;
        }
        return $this->_outlineDictionary->F->value & 1;
    }

    /**
     * Sets 'isItalic' outline flag
     *
     * @param boolean $isItalic
     * @return \Zend\PDF\Outline\AbstractOutline
     */
    public function setIsItalic($isItalic)
    {
        if ($this->_outlineDictionary->F === null) {
            $this->_outlineDictionary->touch();
            $this->_outlineDictionary->F = new InternalType\NumericObject($isItalic? 1 : 0);
        } else {
            $this->_outlineDictionary->F->touch();
            if ($isItalic) {
                $this->_outlineDictionary->F->value = $this->_outlineDictionary->F->value | 1;
            } else {
                $this->_outlineDictionary->F->value = $this->_outlineDictionary->F->value | ~1;
            }
        }
        return $this;
    }

    /**
     * Returns true if outline item is displayed in bold
     *
     * @return boolean
     */
    public function isBold()
    {
        if ($this->_outlineDictionary->F === null) {
            return false;
        }
        return $this->_outlineDictionary->F->value & 2;
    }

    /**
     * Sets 'isBold' outline flag
     *
     * @param boolean $isBold
     * @return \Zend\PDF\Outline\AbstractOutline
     */
    public function setIsBold($isBold)
    {
        if ($this->_outlineDictionary->F === null) {
            $this->_outlineDictionary->touch();
            $this->_outlineDictionary->F = new InternalType\NumericObject($isBold? 2 : 0);
        } else {
            $this->_outlineDictionary->F->touch();
            if ($isBold) {
                $this->_outlineDictionary->F->value = $this->_outlineDictionary->F->value | 2;
            } else {
                $this->_outlineDictionary->F->value = $this->_outlineDictionary->F->value | ~2;
            }
        }
        return $this;
    }


    /**
     * Get outline text color.
     *
     * @return \Zend\PDF\Color\RGB
     */
    public function getColor()
    {
        if ($this->_outlineDictionary->C === null) {
            return null;
        }

        $components = $this->_outlineDictionary->C->items;

        return new Color\RGB($components[0], $components[1], $components[2]);
    }

    /**
     * Set outline text color.
     * (null means default color which is black)
     *
     * @param \Zend\PDF\Color\RGB $color
     * @return \Zend\PDF\Outline\AbstractOutline
     */
    public function setColor(Color\RGB $color)
    {
        $this->_outlineDictionary->touch();

        if ($color === null) {
            $this->_outlineDictionary->C = null;
        } else {
            $components = $color->getComponents();
            $colorComponentElements = array(new InternalType\NumericObject($components[0]),
                                            new InternalType\NumericObject($components[1]),
                                            new InternalType\NumericObject($components[2]));
            $this->_outlineDictionary->C = new InternalType\ArrayObject($colorComponentElements);
        }

        return $this;
    }

    /**
     * Get outline target.
     *
     * @return \Zend\PDF\InternalStructure\NavigationTarget
     * @throws \Zend\PDF\Exception
     */
    public function getTarget()
    {
        if ($this->_outlineDictionary->Dest !== null) {
            if ($this->_outlineDictionary->A !== null) {
                throw new PDF\Exception('Outline dictionary may contain Dest or A entry, but not both.');
            }
            return Destination\AbstractDestination::load($this->_outlineDictionary->Dest);
        } else if ($this->_outlineDictionary->A !== null) {
            return Action\AbstractAction::load($this->_outlineDictionary->A);
        }

        return null;
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
        $this->_outlineDictionary->touch();

        if (is_string($target)) {
            $target = Destination\Named::create($target);
        }

        if ($target === null) {
            $this->_outlineDictionary->Dest = null;
            $this->_outlineDictionary->A    = null;
        } else if ($target instanceof Destination\AbstractDestination) {
            $this->_outlineDictionary->Dest = $target->getResource();
            $this->_outlineDictionary->A    = null;
        } else if ($target instanceof Action\AbstractAction) {
            $this->_outlineDictionary->Dest = null;
            $this->_outlineDictionary->A    = $target->getResource();
        } else {
            throw new PDF\Exception('Outline target has to be Zend_PDF_Destination or Zend_PDF_Action object or string');
        }

        return $this;
    }

    /**
     * Set outline options
     *
     * @param array $options
     * @return Zend_PDF_Actions_Traceable
     * @throws \Zend\PDF\Exception
     */
    public function setOptions(array $options)
    {
        parent::setOptions($options);

        return $this;
    }



    /**
     * Create PDF outline object using specified dictionary
     *
     * @internal
     * @param \Zend\PDF\InternalType\AbstractTypeObject $dictionary (It's actually Dictionary or Dictionary Object or Reference to a Dictionary Object)
     * @param \Zend\PDF\Action\AbstractAction  $parentAction
     * @param SplObjectStorage $processedOutlines  List of already processed Outline dictionaries,
     *                                             used to avoid cyclic references
     * @return \Zend\PDF\Action\AbstractAction
     * @throws \Zend\PDF\Exception
     */
    public function __construct(InternalType\AbstractTypeObject $dictionary, \SplObjectStorage $processedDictionaries = null)
    {
        if ($dictionary->getType() != InternalType\AbstractTypeObject::TYPE_DICTIONARY) {
            throw new PDF\Exception('$dictionary mast be an indirect dictionary object.');
        }

        if ($processedDictionaries === null) {
            $processedDictionaries = new \SplObjectStorage();
        }
        $processedDictionaries->attach($dictionary);

        $this->_outlineDictionary = $dictionary;

        if ($dictionary->Count !== null) {
            if ($dictionary->Count->getType() != InternalType\AbstractTypeObject::TYPE_NUMERIC) {
                throw new PDF\Exception('Outline dictionary Count entry must be a numeric element.');
            }

            $childOutlinesCount = $dictionary->Count->value;
            if ($childOutlinesCount > 0) {
                $this->_open = true;
            }
            $childOutlinesCount = abs($childOutlinesCount);

            $childDictionary = $dictionary->First;
            for ($count = 0; $count < $childOutlinesCount; $count++) {
                if ($childDictionary === null) {
                    throw new PDF\Exception('Outline childs load error.');
                }

                if (!$processedDictionaries->contains($childDictionary)) {
                    $this->childOutlines[] = new Loaded($childDictionary, $processedDictionaries);
                }

                $childDictionary = $childDictionary->Next;
            }

            if ($childDictionary !== null) {
                throw new PDF\Exception('Outline childs load error.');
            }

            $this->_originalChildOutlines = $this->childOutlines;
        }
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

        if ($updateNavigation) {
            $this->_outlineDictionary->touch();

            $this->_outlineDictionary->Parent = $parent;
            $this->_outlineDictionary->Prev   = $prev;
            $this->_outlineDictionary->Next   = null;
        }

        $updateChildNavigation = false;
        if (count($this->_originalChildOutlines) != count($this->childOutlines)) {
            // If original and current children arrays have different size then children list was updated
            $updateChildNavigation = true;
        } else if ( !(array_keys($this->_originalChildOutlines) === array_keys($this->childOutlines)) ) {
            // If original and current children arrays have different keys (with a glance to an order) then children list was updated
            $updateChildNavigation = true;
        } else {
            foreach ($this->childOutlines as $key => $childOutline) {
                if ($this->_originalChildOutlines[$key] !== $childOutline) {
                    $updateChildNavigation = true;
                    break;
                }
            }
        }

        $lastChild = null;
        if ($updateChildNavigation) {
            $this->_outlineDictionary->touch();
            $this->_outlineDictionary->First = null;

            foreach ($this->childOutlines as $childOutline) {
                if ($processedOutlines->contains($childOutline)) {
                    throw new PDF\Exception('Outlines cyclyc reference is detected.');
                }

                if ($lastChild === null) {
                    // First pass. Update Outlines dictionary First entry using corresponding value
                    $lastChild = $childOutline->dumpOutline($factory, $updateChildNavigation, $this->_outlineDictionary, null, $processedOutlines);
                    $this->_outlineDictionary->First = $lastChild;
                } else {
                    // Update previous outline dictionary Next entry (Prev is updated within dumpOutline() method)
                    $childOutlineDictionary = $childOutline->dumpOutline($factory, $updateChildNavigation, $this->_outlineDictionary, $lastChild, $processedOutlines);
                    $lastChild->Next = $childOutlineDictionary;
                    $lastChild       = $childOutlineDictionary;
                }
            }

            $this->_outlineDictionary->Last  = $lastChild;

            if (count($this->childOutlines) != 0) {
                $this->_outlineDictionary->Count = new InternalType\NumericObject(($this->isOpen()? 1 : -1)*count($this->childOutlines));
            } else {
                $this->_outlineDictionary->Count = null;
            }
        } else {
            foreach ($this->childOutlines as $childOutline) {
                if ($processedOutlines->contains($childOutline)) {
                    throw new PDF\Exception('Outlines cyclyc reference is detected.');
                }
                $lastChild = $childOutline->dumpOutline($factory, $updateChildNavigation, $this->_outlineDictionary, $lastChild, $processedOutlines);
            }
        }

        return $this->_outlineDictionary;
    }

    public function dump($level = 0)
    {
        printf(":%3d:%s:%s:%s%s  :\n", count($this->childOutlines),$this->isItalic()? 'i':' ', $this->isBold()? 'b':' ', str_pad('', 4*$level), $this->getTitle());

        if ($this->isOpen()  ||  true) {
            foreach ($this->childOutlines as $child) {
                $child->dump($level + 1);
            }
        }
    }
}
