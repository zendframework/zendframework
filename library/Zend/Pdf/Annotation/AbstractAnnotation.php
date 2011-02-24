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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Annotation;
use Zend\Pdf\Exception;
use Zend\Pdf;
use Zend\Pdf\InternalType;

/**
 * Abstract PDF annotation representation class
 *
 * An annotation associates an object such as a note, sound, or movie with a location
 * on a page of a PDF document, or provides a way to interact with the user by
 * means of the mouse and keyboard.
 *
 * @uses       \Zend\Pdf\InternalType\AbstractTypeObject
 * @uses       \Zend\Pdf\InternalType\StringObject
 * @uses       \Zend\Pdf\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Annotation
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAnnotation
{
    /**
     * Annotation dictionary
     *
     * @var \Zend\Pdf\InternalType\DictionaryObject|\Zend\Pdf\InternalType\IndirectObject|\Zend\Pdf\InternalType\IndirectObjectReference
     */
    protected $_annotationDictionary;

    /**
     * Get annotation dictionary
     *
     * @internal
     * @return \Zend\Pdf\InternalType\AbstractTypeObject
     */
    public function getResource()
    {
        return $this->_annotationDictionary;
    }


    /**
     * Set bottom edge of the annotation rectangle.
     *
     * @param float $bottom
     * @return \Zend\Pdf\Annotation\AbstractAnnotation
     */
    public function setBottom($bottom) {
        $this->_annotationDictionary->Rect->items[1]->touch();
        $this->_annotationDictionary->Rect->items[1]->value = $bottom;

        return $this;
    }

    /**
     * Get bottom edge of the annotation rectangle.
     *
     * @return float
     */
    public function getBottom() {
        return $this->_annotationDictionary->Rect->items[1]->value;
    }

    /**
     * Set top edge of the annotation rectangle.
     *
     * @param float $top
     * @return \Zend\Pdf\Annotation\AbstractAnnotation
     */
    public function setTop($top) {
        $this->_annotationDictionary->Rect->items[3]->touch();
        $this->_annotationDictionary->Rect->items[3]->value = $top;

        return $this;
    }

    /**
     * Get top edge of the annotation rectangle.
     *
     * @return float
     */
    public function getTop() {
        return $this->_annotationDictionary->Rect->items[3]->value;
    }

    /**
     * Set right edge of the annotation rectangle.
     *
     * @param float $right
     * @return \Zend\Pdf\Annotation\AbstractAnnotation
     */
    public function setRight($right) {
        $this->_annotationDictionary->Rect->items[2]->touch();
        $this->_annotationDictionary->Rect->items[2]->value = $right;

        return $this;
    }

    /**
     * Get right edge of the annotation rectangle.
     *
     * @return float
     */
    public function getRight() {
        return $this->_annotationDictionary->Rect->items[2]->value;
    }

    /**
     * Set left edge of the annotation rectangle.
     *
     * @param float $left
     * @return \Zend\Pdf\Annotation\AbstractAnnotation
     */
    public function setLeft($left) {
        $this->_annotationDictionary->Rect->items[0]->touch();
        $this->_annotationDictionary->Rect->items[0]->value = $left;

        return $this;
    }

    /**
     * Get left edge of the annotation rectangle.
     *
     * @return float
     */
    public function getLeft() {
        return $this->_annotationDictionary->Rect->items[0]->value;
    }

    /**
     * Return text to be displayed for the annotation or, if this type of annotation
     * does not display text, an alternate description of the annotation’s contents
     * in human-readable form.
     *
     * @return string
     */
    public function getText() {
        if ($this->_annotationDictionary->Contents === null) {
            return '';
        }

        return $this->_annotationDictionary->Contents->value;
    }

    /**
     * Set text to be displayed for the annotation or, if this type of annotation
     * does not display text, an alternate description of the annotation’s contents
     * in human-readable form.
     *
     * @param string $text
     * @return \Zend\Pdf\Annotation\AbstractAnnotation
     */
    public function setText($text) {
        if ($this->_annotationDictionary->Contents === null) {
            $this->_annotationDictionary->touch();
            $this->_annotationDictionary->Contents = new InternalType\StringObject($text);
        } else {
            $this->_annotationDictionary->Contents->touch();
            $this->_annotationDictionary->Contents->value = new InternalType\StringObject($text);
        }

        return $this;
    }

    /**
     * Annotation object constructor
     *
     * @throws \Zend\Pdf\Exception
     */
    public function __construct(InternalType\AbstractTypeObject $annotationDictionary)
    {
        if ($annotationDictionary->getType() != InternalType\AbstractTypeObject::TYPE_DICTIONARY) {
            throw new Exception\CorruptedPdfException('Annotation dictionary resource has to be a dictionary.');
        }

        $this->_annotationDictionary = $annotationDictionary;

        if ($this->_annotationDictionary->Type !== null  &&
            $this->_annotationDictionary->Type->value != 'Annot') {
            throw new Exception\CorruptedPdfException('Wrong resource type. \'Annot\' expected.');
        }

        if ($this->_annotationDictionary->Rect === null) {
            throw new Exception\CorruptedPdfException('\'Rect\' dictionary entry is required.');
        }

        if (count($this->_annotationDictionary->Rect->items) != 4 ||
            $this->_annotationDictionary->Rect->items[0]->getType() != InternalType\AbstractTypeObject::TYPE_NUMERIC ||
            $this->_annotationDictionary->Rect->items[1]->getType() != InternalType\AbstractTypeObject::TYPE_NUMERIC ||
            $this->_annotationDictionary->Rect->items[2]->getType() != InternalType\AbstractTypeObject::TYPE_NUMERIC ||
            $this->_annotationDictionary->Rect->items[3]->getType() != InternalType\AbstractTypeObject::TYPE_NUMERIC ) {
            throw new Exception\CorruptedPdfException('\'Rect\' dictionary entry must be an array of four numeric elements.');
        }
    }

    /**
     * Load Annotation object from a specified resource
     *
     * @internal
     * @param $destinationArray
     * @return \Zend\Pdf\Annotation\AbstractAnnotation
     */
    public static function load(InternalType\AbstractTypeObject $resource)
    {
        /** @todo implementation */
    }
}
