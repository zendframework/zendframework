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
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\InternalType;
use Zend\Pdf\Exception;
use Zend\Pdf;

/**
 * PDF file 'dictionary' element implementation
 *
 * @uses       \Zend\Pdf\InternalType\AbstractTypeObject
 * @uses       \Zend\Pdf\InternalType\NameObject
 * @uses       \Zend\Pdf\Exception
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DictionaryObject extends AbstractTypeObject
{
    /**
     * Dictionary elements
     * Array of \Zend\Pdf\InternalType objects ('name' => \Zend\Pdf\InternalType\AbstaractTypeObject)
     *
     * @var array
     */
    private $_items = array();


    /**
     * Object constructor
     *
     * @param array $val   - array of \Zend\Pdf\InternalType\AbstractTypeObject objects
     * @throws \Zend\Pdf\Exception
     */
    public function __construct($val = null)
    {
        if ($val === null) {
            return;
        } else if (!is_array($val)) {
            throw new Exception\RuntimeException('Argument must be an array');
        }

        foreach ($val as $name => $element) {
            if (!$element instanceof AbstractTypeObject) {
                throw new Exception\RuntimeException('Array elements must be \Zend\Pdf\InternalType\AbstractTypeObject objects');
            }
            if (!is_string($name)) {
                throw new Exception\RuntimeException('Array keys must be strings');
            }
            $this->_items[$name] = $element;
        }
    }


    /**
     * Add element to an array
     *
     * @name \Zend\Pdf\InternalType\NameObject $name
     * @param \Zend\Pdf\InternalType\AbstractTypeObject $val   - \Zend\Pdf\InternalType\AbstractTypeObject object
     * @throws \Zend\Pdf\Exception
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
     * @return \Zend\Pdf\InternalType\AbstractTypeObject | null
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
     * @param \Zend\Pdf\ObjectFactory $factory
     * @return string
     */
    public function toString(Pdf\ObjectFactory $factory = null)
    {
        $outStr = '<<';
        $lastNL = 0;

        foreach ($this->_items as $name => $element) {
            if (!is_object($element)) {
                throw new Exception\RuntimeException('Wrong data');
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
     * Detach PDF object from the factory (if applicable), clone it and attach to new factory.
     *
     * @param \Zend\Pdf\ObjectFactory $factory  The factory to attach
     * @param array &$processed List of already processed indirect objects, used to avoid objects duplication
     * @param integer $mode  Cloning mode (defines filter for objects cloning)
     * @returns \Zend\Pdf\InternalType\AbstractTypeObject
     * @throws \Zend\Pdf\Exception
     */
    public function makeClone(Pdf\ObjectFactory $factory, array &$processed, $mode)
    {
        if (isset($this->_items['Type'])) {
            if ($this->_items['Type']->value == 'Pages') {
                // It's a page tree node
                // skip it and its children
                return new NullObject();
            }

            if ($this->_items['Type']->value == 'Page'  &&
                $mode == AbstractTypeObject::CLONE_MODE_SKIP_PAGES
            ) {
                // It's a page node, skip it
                return new NullObject();
            }
        }

        $newDictionary = new self();
        foreach ($this->_items as $key => $value) {
            $newDictionary->_items[$key] = $value->makeClone($factory, $processed, $mode);
        }

        return $newDictionary;
    }

    /**
     * Set top level parent indirect object.
     *
     * @param \Zend\Pdf\InternalType\IndirectObject $parent
     */
    public function setParentObject(IndirectObject $parent)
    {
        parent::setParentObject($parent);

        foreach ($this->_items as $item) {
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

        foreach ($this->_items as $itemName => $item) {
            $phpArray[$itemName] = $item->toPhp();
        }

        return $phpArray;
    }
}
