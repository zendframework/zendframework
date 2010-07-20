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
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Pdf\InternalType;

/**
 * PDF file element implementation
 *
 * @uses       \Zend\Pdf\InternalType\ArrayObject
 * @uses       \Zend\Pdf\InternalType\BooleanObject
 * @uses       \Zend\Pdf\InternalType\DictionaryObject
 * @uses       \Zend\Pdf\InternalType\NumericObject
 * @uses       \Zend\Pdf\InternalType\StringObject
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractTypeObject
{
    const TYPE_BOOL        = 1;
    const TYPE_NUMERIC     = 2;
    const TYPE_STRING      = 3;
    const TYPE_NAME        = 4;
    const TYPE_ARRAY       = 5;
    const TYPE_DICTIONARY  = 6;
    const TYPE_STREAM      = 7;
    const TYPE_NULL        = 11;

    /**
     * Reference to the top level indirect object, which contains this element.
     *
     * @var \Zend\Pdf\InternalType\IndirectObject
     */
    private $_parentObject = null;

    /**
     * Return type of the element.
     * See ZPDFPDFConst for possible values
     *
     * @return integer
     */
    abstract public function getType();

    /**
     * Convert element to a string, which can be directly
     * written to a PDF file.
     *
     * $factory parameter defines operation context.
     *
     * @param Zend_PDF_Factory $factory
     * @return string
     */
    abstract public function toString($factory = null);


    /**
     * Set top level parent indirect object.
     *
     * @param \Zend\Pdf\InternalType\IndirectObject $parent
     */
    public function setParentObject(IndirectObject $parent)
    {
        $this->_parentObject = $parent;
    }


    /**
     * Get top level parent indirect object.
     *
     * @return \Zend\Pdf\InternalType\IndirectObject
     */
    public function getParentObject()
    {
        return $this->_parentObject;
    }


    /**
     * Mark object as modified, to include it into new PDF file segment.
     *
     * We don't automate this action to keep control on PDF update process.
     * All new objects are treated as "modified" automatically.
     */
    public function touch()
    {
        if ($this->_parentObject !== null) {
            $this->_parentObject->touch();
        }
    }

    /**
     * Clean up resources, used by object
     */
    public function cleanUp()
    {
        // Do nothing
    }

    /**
     * Convert PDF element to PHP type.
     *
     * @return mixed
     */
    public function toPhp()
    {
        return $this->value;
    }

    /**
     * Convert PHP value into PDF element.
     *
     * @param mixed $input
     * @return \Zend\Pdf\InternalType\AbstractTypeObject
     */
    public static function phpToPDF($input)
    {
        if (is_numeric($input)) {
            return new NumericObject($input);
        } else if (is_bool($input)) {
            return new BooleanObject($input);
        } else if (is_array($input)) {
            $pdfElementsArray = array();
            $isDictionary = false;

            foreach ($input as $key => $value) {
                if (is_string($key)) {
                    $isDictionary = true;
                }
                $pdfElementsArray[$key] = self::phpToPDF($value);
            }

            if ($isDictionary) {
                return new DictionaryObject($pdfElementsArray);
            } else {
                return new ArrayObject($pdfElementsArray);
            }
        } else {
            return new StringObject((string)$input);
        }
    }
}
