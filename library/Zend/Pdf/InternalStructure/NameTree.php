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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\InternalStructure;
use Zend\Pdf\Exception;
use Zend\Pdf\InternalType;
use Zend\Pdf;

/**
 * PDF name tree representation class
 *
 * @todo implement lazy resource loading so resources will be really loaded at access time
 *
 * @uses       ArrayAccess
 * @uses       Countable
 * @uses       Iterator
 * @uses       \Zend\Pdf\InternalType\AbstractTypeObject
 * @uses       \Zend\Pdf\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class NameTree implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * Elements
     * Array of name => object tree entries
     *
     * @var array
     */
    protected $_items = array();

    /**
     * Object constructor
     *
     * @param $rootDictionary root of name dictionary
     * @throws \Zend\Pdf\Exception
     */
    public function __construct(InternalType\AbstractTypeObject $rootDictionary)
    {
        if ($rootDictionary->getType() != InternalType\AbstractTypeObject::TYPE_DICTIONARY) {
            throw new Exception\CorruptedPdfException('Name tree root must be a dictionary.');
        }

        $intermediateNodes = array();
        $leafNodes         = array();
        if ($rootDictionary->Kids !== null) {
            $intermediateNodes[] = $rootDictionary;
        } else {
            $leafNodes[] = $rootDictionary;
        }

        while (count($intermediateNodes) != 0) {
            $newIntermediateNodes = array();
            foreach ($intermediateNodes as $node) {
                foreach ($node->Kids->items as $childNode) {
                    if ($childNode->Kids !== null) {
                        $newIntermediateNodes[] = $childNode;
                    } else {
                        $leafNodes[] = $childNode;
                    }
                }
            }
            $intermediateNodes = $newIntermediateNodes;
        }

        foreach ($leafNodes as $leafNode) {
            $destinationsCount = count($leafNode->Names->items)/2;
            for ($count = 0; $count < $destinationsCount; $count++) {
                $this->_items[$leafNode->Names->items[$count*2]->value] = $leafNode->Names->items[$count*2 + 1];
            }
        }
    }

    public function current()
    {
        return current($this->_items);
    }


    public function next()
    {
        return next($this->_items);
    }


    public function key()
    {
        return key($this->_items);
    }


    public function valid() {
        return current($this->_items)!==false;
    }


    public function rewind()
    {
        reset($this->_items);
    }


    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_items);
    }


    public function offsetGet($offset)
    {
        return $this->_items[$offset];
    }


    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->_items[]        = $value;
        } else {
            $this->_items[$offset] = $value;
        }
    }


    public function offsetUnset($offset)
    {
        unset($this->_items[$offset]);
    }


    public function clear()
    {
        $this->_items = array();
    }

    public function count()
    {
        return count($this->_items);
    }
}
