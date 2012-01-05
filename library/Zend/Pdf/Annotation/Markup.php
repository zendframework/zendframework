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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
 * A markup annotation
 *
 * @uses       \Zend\Pdf\Annotation\AbstractAnnotation
 * @uses       \Zend\Pdf\InternalType\AbstractTypeObject
 * @uses       \Zend\Pdf\InternalType\ArrayObject
 * @uses       \Zend\Pdf\InternalType\DictionaryObject
 * @uses       \Zend\Pdf\InternalType\NameObject
 * @uses       \Zend\Pdf\InternalType\NumericObject
 * @uses       \Zend\Pdf\InternalType\StringObject
 * @uses       \Zend\Pdf\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Markup extends AbstractAnnotation
{
    /**
     * Annotation subtypes
     */
    const SUBTYPE_HIGHLIGHT = 'Highlight';
    const SUBTYPE_UNDERLINE = 'Underline';
    const SUBTYPE_SQUIGGLY  = 'Squiggly';
    const SUBTYPE_STRIKEOUT = 'StrikeOut';

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

        if ($annotationDictionary->Subtype === null  ||
            $annotationDictionary->Subtype->getType() != InternalType\AbstractTypeObject::TYPE_NAME  ||
            !in_array( $annotationDictionary->Subtype->value,
                       array(self::SUBTYPE_HIGHLIGHT,
                             self::SUBTYPE_UNDERLINE,
                             self::SUBTYPE_SQUIGGLY,
                             self::SUBTYPE_STRIKEOUT) )) {
            throw new Exception\CorruptedPdfException('Subtype => Markup entry is omitted or has wrong value.');
        }

        parent::__construct($annotationDictionary);
    }

    /**
     * Create markup annotation object
     *
     * Text markup annotations appear as highlights, underlines, strikeouts or
     * jagged ("squiggly") underlines in the text of a document. When opened,
     * they display a pop-up window containing the text of the associated note.
     *
     * $subType parameter may contain
     *     \Zend\Pdf\Annotation\Markup::SUBTYPE_HIGHLIGHT
     *     \Zend\Pdf\Annotation\Markup::SUBTYPE_UNDERLINE
     *     \Zend\Pdf\Annotation\Markup::SUBTYPE_SQUIGGLY
     *     \Zend\Pdf\Annotation\Markup::SUBTYPE_STRIKEOUT
     * for for a highlight, underline, squiggly-underline, or strikeout annotation,
     * respectively.
     *
     * $quadPoints is an array of 8xN numbers specifying the coordinates of
     * N quadrilaterals default user space. Each quadrilateral encompasses a word or
     * group of contiguous words in the text underlying the annotation.
     * The coordinates for each quadrilateral are given in the order
     *     x1 y1 x2 y2 x3 y3 x4 y4
     * specifying the quadrilateral’s four vertices in counterclockwise order
     * starting from left bottom corner.
     * The text is oriented with respect to the edge connecting points
     * (x1, y1) and (x2, y2).
     *
     * @param float $x1
     * @param float $y1
     * @param float $x2
     * @param float $y2
     * @param string $text
     * @param string $subType
     * @param array $quadPoints  [x1 y1 x2 y2 x3 y3 x4 y4]
     * @return \Zend\Pdf\Annotation\Markup
     * @throws \Zend\Pdf\Exception
     */
    public static function create($x1, $y1, $x2, $y2, $text, $subType, $quadPoints)
    {
        $annotationDictionary = new InternalType\DictionaryObject();

        $annotationDictionary->Type    = new InternalType\NameObject('Annot');
        $annotationDictionary->Subtype = new InternalType\NameObject($subType);

        $rectangle = new InternalType\ArrayObject();
        $rectangle->items[] = new InternalType\NumericObject($x1);
        $rectangle->items[] = new InternalType\NumericObject($y1);
        $rectangle->items[] = new InternalType\NumericObject($x2);
        $rectangle->items[] = new InternalType\NumericObject($y2);
        $annotationDictionary->Rect = $rectangle;

        $annotationDictionary->Contents = new InternalType\StringObject($text);

        if (!is_array($quadPoints)  ||  count($quadPoints) == 0  ||  count($quadPoints) % 8 != 0) {
            throw new Exception\InvalidArgumentException('$quadPoints parameter must be an array of 8xN numbers');
        }
        $points = new InternalType\ArrayObject();
        foreach ($quadPoints as $quadPoint) {
            $points->items[] = new InternalType\NumericObject($quadPoint);
        }
        $annotationDictionary->QuadPoints = $points;

        return new self($annotationDictionary);
    }
}
