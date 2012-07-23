<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\InternalType;

use Zend\Pdf;
use Zend\Pdf\Exception;

/**
 * PDF file 'array' element implementation
 *
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class ArrayObject extends AbstractTypeObject
{
    /**
     * Array element items
     *
     * Array of \Zend\Pdf\InternalType\AbstractTypeObject objects
     *
     * @var array
     */
    public $items;


    /**
     * Object constructor
     *
     * @param array $val   - array of \Zend\Pdf\InternalType\AbstractTypeObject objects
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function __construct($val = null)
    {
        $this->items = new \ArrayObject();

        if ($val !== null  &&  is_array($val)) {
            foreach ($val as $element) {
                if (!$element instanceof AbstractTypeObject) {
                    throw new Exception\RuntimeException('Array elements must be \Zend\Pdf\InternalType\AbstractTypeObject objects');
                }
                $this->items[] = $element;
            }
        } elseif ($val !== null){
            throw new Exception\RuntimeException('Argument must be an array');
        }
    }


    /**
     * Getter
     *
     * @param string $property
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function __get($property)
    {
        throw new Exception\RuntimeException('Undefined property: \Zend\Pdf\InternalType\ArrayObject::$' . $property);
    }


    /**
     * Setter
     *
     * @param mixed $offset
     * @param mixed $value
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function __set($property, $value)
    {
        throw new Exception\RuntimeException('Undefined property: \Zend\Pdf\InternalType\ArrayObject::$' . $property);
    }

    /**
     * Return type of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return AbstractTypeObject::TYPE_ARRAY;
    }


    /**
     * Return object as string
     *
     * @param \Zend\Pdf\ObjectFactory $factory
     * @return string
     */
    public function toString(Pdf\ObjectFactory $factory = null)
    {
        $outStr = '[';
        $lastNL = 0;

        foreach ($this->items as $element) {
            if (strlen($outStr) - $lastNL > 128)  {
                $outStr .= "\n";
                $lastNL = strlen($outStr);
            }

            $outStr .= $element->toString($factory) . ' ';
        }
        $outStr .= ']';

        return $outStr;
    }

    /**
     * Detach PDF object from the factory (if applicable), clone it and attach to new factory.
     *
     * @param \Zend\Pdf\ObjectFactory $factory  The factory to attach
     * @param array &$processed List of already processed indirect objects, used to avoid objects duplication
     * @param integer $mode  Cloning mode (defines filter for objects cloning)
     * @returns \Zend\Pdf\InternalType\AbstractTypeObject
     */
    public function makeClone(Pdf\ObjectFactory $factory, array &$processed, $mode)
    {
        $newArray = new self();

        foreach ($this->items as $key => $value) {
            $newArray->items[$key] = $value->makeClone($factory, $processed, $mode);
        }

        return $newArray;
    }

    /**
     * Set top level parent indirect object.
     *
     * @param \Zend\Pdf\InternalType\IndirectObject $parent
     */
    public function setParentObject(IndirectObject $parent)
    {
        parent::setParentObject($parent);

        foreach ($this->items as $item) {
            $item->setParentObject($parent);
        }
    }

    /**
     * Convert PDF element to PHP type.
     *
     * Dictionary is returned as an associative array
     *
     * @return mixed
     */
    public function toPhp()
    {
        $phpArray = array();

        foreach ($this->items as $item) {
            $phpArray[] = $item->toPhp();
        }

        return $phpArray;
    }
}
