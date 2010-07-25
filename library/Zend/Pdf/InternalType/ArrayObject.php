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
use Zend\Pdf;

/**
 * PDF file 'array' element implementation
 *
 * @uses       ArrayObject
 * @uses       \Zend\Pdf\InternalType\AbstractTypeObject
 * @uses       \Zend\Pdf\Exception
 * @category   Zend
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
     * @throws \Zend\Pdf\Exception
     */
    public function __construct($val = null)
    {
        $this->items = new \ArrayObject();

        if ($val !== null  &&  is_array($val)) {
            foreach ($val as $element) {
                if (!$element instanceof AbstractTypeObject) {
                    throw new Pdf\Exception('Array elements must be \Zend\Pdf\InternalType\AbstractTypeObject objects');
                }
                $this->items[] = $element;
            }
        } else if ($val !== null){
            throw new Pdf\Exception('Argument must be an array');
        }
    }


    /**
     * Getter
     *
     * @param string $property
     * @throws \Zend\Pdf\Exception
     */
    public function __get($property)
    {
        throw new Pdf\Exception('Undefined property: \Zend\Pdf\InternalType\ArrayObject::$' . $property);
    }


    /**
     * Setter
     *
     * @param mixed $offset
     * @param mixed $value
     * @throws \Zend\Pdf\Exception
     */
    public function __set($property, $value)
    {
        throw new Pdf\Exception('Undefined property: \Zend\Pdf\InternalType\ArrayObject::$' . $property);
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
     * @param Zend_PDF_Factory $factory
     * @return string
     */
    public function toString($factory = null)
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
