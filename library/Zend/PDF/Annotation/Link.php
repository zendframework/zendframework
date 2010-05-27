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
 * @subpackage Zend_PDF_Annotation
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\Annotation;
use Zend\PDF;
use Zend\PDF\InternalStructure;
use Zend\PDF\InternalType;
use Zend\PDF\Destination;

/**
 * A link annotation represents either a hypertext link to a destination elsewhere in
 * the document or an action to be performed.
 *
 * Only destinations are used now since only GoTo action can be created by user
 * in current implementation.
 *
 * @uses       \Zend\PDF\Action\AbstractAction
 * @uses       \Zend\PDF\Annotation\AbstractAnnotation
 * @uses       \Zend\PDF\Destination\AbstractDestination
 * @uses       \Zend\PDF\Destination\Named
 * @uses       \Zend\PDF\InternalType\AbstractTypeObject
 * @uses       \Zend\PDF\InternalType\ArrayObject
 * @uses       \Zend\PDF\InternalType\DictionaryObject
 * @uses       \Zend\PDF\InternalType\NameObject
 * @uses       \Zend\PDF\InternalType\NumericObject
 * @uses       \Zend\PDF\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Annotation
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Link extends AbstractAnnotation
{
    /**
     * Annotation object constructor
     *
     * @throws \Zend\PDF\Exception
     */
    public function __construct(InternalType\AbstractTypeObject $annotationDictionary)
    {
        if ($annotationDictionary->getType() != InternalType\AbstractTypeObject::TYPE_DICTIONARY) {
            throw new PDF\Exception('Annotation dictionary resource has to be a dictionary.');
        }

        if ($annotationDictionary->Subtype === null  ||
            $annotationDictionary->Subtype->getType() != InternalType\AbstractTypeObject::TYPE_NAME  ||
            $annotationDictionary->Subtype->value != 'Link') {
            throw new PDF\Exception('Subtype => Link entry is requires');
        }

        parent::__construct($annotationDictionary);
    }

    /**
     * Create link annotation object
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param \Zend\PDF\InternalStructure\NavigationTarget|string $target
     * @return \Zend\PDF\Annotation\Link
     */
    public static function create($x1, $y1, $x2, $y2, $target)
    {
        if (is_string($target)) {
            $destination = Destination\Named::create($target);
        }
        if (!$target instanceof InternalStructure\NavigationTarget) {
            throw new PDF\Exception('$target parameter must be a Zend_PDF_Target object or a string.');
        }

        $annotationDictionary = new InternalType\DictionaryObject();

        $annotationDictionary->Type    = new InternalType\NameObject('Annot');
        $annotationDictionary->Subtype = new InternalType\NameObject('Link');

        $rectangle = new InternalType\ArrayObject();
        $rectangle->items[] = new InternalType\NumericObject($x1);
        $rectangle->items[] = new InternalType\NumericObject($y1);
        $rectangle->items[] = new InternalType\NumericObject($x2);
        $rectangle->items[] = new InternalType\NumericObject($y2);
        $annotationDictionary->Rect = $rectangle;

        if ($target instanceof Destination\AbstractDestination) {
            $annotationDictionary->Dest = $target->getResource();
        } else {
            $annotationDictionary->A = $target->getResource();
        }

        return new self($annotationDictionary);
    }

    /**
     * Set link annotation destination
     *
     * @param \Zend\PDF\InternalStructure\NavigationTarget|string $target
     * @return \Zend\PDF\Annotation\Link
     */
    public function setDestination($target)
    {
        if (is_string($target)) {
            $destination = Destination\Named::create($target);
        }
        if (!$target instanceof InternalStructure\NavigationTarget) {
            throw new PDF\Exception('$target parameter must be a Zend_PDF_Target object or a string.');
        }

        $this->_annotationDictionary->touch();
        $this->_annotationDictionary->Dest = $destination->getResource();
        if ($target instanceof Destination\AbstractDestination) {
            $this->_annotationDictionary->Dest = $target->getResource();
            $this->_annotationDictionary->A    = null;
        } else {
            $this->_annotationDictionary->Dest = null;
            $this->_annotationDictionary->A    = $target->getResource();
        }

        return $this;
    }

    /**
     * Get link annotation destination
     *
     * @return \Zend\PDF\InternalStructure\NavigationTarget|null
     */
    public function getDestination()
    {
        if ($this->_annotationDictionary->Dest === null  &&
            $this->_annotationDictionary->A    === null) {
            return null;
        }

        if ($this->_annotationDictionary->Dest !== null) {
            return Destination\AbstractDestination::load($this->_annotationDictionary->Dest);
        } else {
            return PDF\Action\AbstractAction::load($this->_annotationDictionary->A);
        }
    }
}
