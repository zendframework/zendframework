<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\Util;

/**
 * Iteratable objects container
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Util
 */
class RecursivelyIteratableObjectsContainer implements \RecursiveIterator, \Countable
{
    protected $_objects = array();

    public function __construct(array $objects) { $this->_objects = $objects; }

    public function current()      { return current($this->_objects);            }
    public function key()          { return key($this->_objects);                }
    public function next()         { return next($this->_objects);               }
    public function rewind()       { return reset($this->_objects);              }
    public function valid()        { return current($this->_objects) !== false;  }
    public function getChildren()  { return current($this->_objects);            }
    public function hasChildren()  { return count($this->_objects) > 0;          }

    public function count() { return count($this->_objects); }
}
