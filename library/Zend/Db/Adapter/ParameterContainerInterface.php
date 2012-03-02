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
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Adapter;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface ParameterContainerInterface extends \ArrayAccess, \Countable, \Traversable
{
    const POSITION_APPEND = null;

    const TYPE_AUTO    = 'auto';
    const TYPE_NULL    = 'null';
    const TYPE_DOUBLE  = 'double';
    const TYPE_INTEGER = 'integer';
    const TYPE_STRING  = 'string';
    const TYPE_LOB     = 'lob';

    public function setFromArray(Array $data);
    
    public function offsetSetErrata($nameOrPosition, $errata);
    public function offsetGetErrata($nameOrPosition);
    public function offsetHasErrata($nameOrPosition);
    public function offsetUnsetErrata($nameOrPosition);
    public function getErrataIterator();

    public function toArray();
}
