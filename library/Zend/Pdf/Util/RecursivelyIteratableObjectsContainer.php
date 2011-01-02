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
 * @subpackage Zend_PDF_Util
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Util;

/**
 * Iteratable objects container
 *
 * @uses       Countable
 * @uses       RecursiveIterator
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Util
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
