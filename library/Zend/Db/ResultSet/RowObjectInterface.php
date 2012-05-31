<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\ResultSet;

use ArrayAccess,
    Countable;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage ResultSet
 */
interface RowObjectInterface extends Countable, ArrayAccess
{
    public function populate(array $rowData);
}
