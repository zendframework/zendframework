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

namespace Zend\Pdf\Annotation;
use Zend\Pdf\Exception;
use Zend\Pdf;
use Zend\Pdf\InternalType;

/**
 * A file attachment annotation contains a reference to a file,
 * which typically is embedded in the PDF file.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FileAttachment extends AbstractAnnotation
{
    /**
     * Annotation object constructor
     *
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function __construct(InternalType\AbstractTypeObject $annotationDictionary)
    {
        if ($annotationDictionary->getType() != InternalType\AbstractTypeObject::TYPE_DICTIONARY) {
            throw new Exception\CorruptedPdfException('Annotation dictionary resource has to be a dictionary.');
        }

        if ($annotationDictionary->Subtype === null  ||
            $annotationDictionary->Subtype->getType() != InternalType\AbstractTypeObject::TYPE_NAME  ||
            $annotationDictionary->Subtype->value != 'FileAttachment') {
            throw new Exception\CorruptedPdfException('Subtype => FileAttachment entry is requires');
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
     * @param string $fileSpecification
     * @return \Zend\Pdf\Annotation\FileAttachment
     */
    public static function create($x1, $y1, $x2, $y2, $fileSpecification)
    {
        $annotationDictionary = new InternalType\DictionaryObject();

        $annotationDictionary->Type    = new InternalType\NameObject('Annot');
        $annotationDictionary->Subtype = new InternalType\NameObject('FileAttachment');

        $rectangle = new InternalType\ArrayObject();
        $rectangle->items[] = new InternalType\NumericObject($x1);
        $rectangle->items[] = new InternalType\NumericObject($y1);
        $rectangle->items[] = new InternalType\NumericObject($x2);
        $rectangle->items[] = new InternalType\NumericObject($y2);
        $annotationDictionary->Rect = $rectangle;

        $fsDictionary = new InternalType\DictionaryObject();
        $fsDictionary->Type = new InternalType\NameObject('Filespec');
        $fsDictionary->F    = new InternalType\StringObject($fileSpecification);

        $annotationDictionary->FS = $fsDictionary;


        return new self($annotationDictionary);
    }
}
