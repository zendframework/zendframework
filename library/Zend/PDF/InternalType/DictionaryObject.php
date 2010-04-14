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
namespace Zend\PDF\InternalType;
use Zend\PDF;

/**
 * PDF file 'dictionary' element implementation
 *
 * @uses       \Zend\PDF\InternalType\AbstractTypeObject
 * @uses       \Zend\PDF\InternalType\NameObject
 * @uses       \Zend\PDF\Exception
 * @category   Zend
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DictionaryObject extends AbstractTypeObject
{
    /**
     * Dictionary elements
     * Array of \Zend\PDF\InternalType objects ('name' => \Zend\PDF\InternalType\AbstaractTypeObject)
     *
     * @var array
     */
    private $_items = array();


    /**
     * Object constructor
     *
     * @param array $val   - array of \Zend\PDF\InternalType\AbstractTypeObject objects
     * @throws \Zend\PDF\Exception
     */
    public function __construct($val = null)
    {
        if ($val === null) {
            return;
        } else if (!is_array($val)) {
            throw new PDF\Exception('Argument must be an array');
        }

        foreach ($val as $name => $element) {
            if (!$element instanceof AbstractTypeObject) {
                throw new PDF\Exception('Array elements must be \Zend\PDF\InternalType\AbstractTypeObject objects');
            }
            if (!is_string($name)) {
                throw new PDF\Exception('Array keys must be strings');
            }
            $this->_items[$name] = $element;
        }
    }


    /**
     * Add element to an array
     *
     * @name \Zend\PDF\InternalType\NameObject $name
     * @param \Zend\PDF\InternalType\AbstractTypeObject $val   - \Zend\PDF\InternalType\AbstractTypeObject object
     * @throws \Zend\PDF\Exception
     */
    public function add(NameObject $name, AbstractTypeObject $val)
    {
        $this->_items[$name->value] = $val;
    }

    /**
     * Return dictionary keys
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->_items);
    }


    /**
     * Get handler
     *
     * @param string $property
     * @return \Zend\PDF\InternalType\AbstractTypeObject | null
     */
    public function __get($item)
    {
        $element = isset($this->_items[$item]) ? $this->_items[$item]
                                               : null;

        return $element;
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  mixed $value
     */
    public function __set($item, $value)
    {
        if ($value === null) {
            unset($this->_items[$item]);
        } else {
            $this->_items[$item] = $value;
        }
    }

    /**
     * Return type of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return AbstractTypeObject::TYPE_DICTIONARY;
    }


    /**
     * Return object as string
     *
     * @param Zend_PDF_Factory $factory
     * @return string
     */
    public function toString($factory = null)
    {
        $outStr = '<<';
        $lastNL = 0;

        foreach ($this->_items as $name => $element) {
            if (!is_object($element)) {
                throw new PDF\Exception('Wrong data');
            }

            if (strlen($outStr) - $lastNL > 128)  {
                $outStr .= "\n";
                $lastNL = strlen($outStr);
            }

            $nameObj = new NameObject($name);
            $outStr .= $nameObj->toString($factory) . ' ' . $element->toString($factory) . ' ';
        }
        $outStr .= '>>';

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

        foreach ($this->_items as $itemName => $item) {
            $phpArray[$itemName] = $item->toPhp();
        }

        return $phpArray;
    }
}
