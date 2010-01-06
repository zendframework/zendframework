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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Db_Table_Row_Abstract
 */
require_once 'Zend/Db/Table/Row/Abstract.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class My_ZendDbTable_Row_TestMockRow extends Zend_Db_Table_Row_Abstract
{

    public $parentTable       = null;
    public $dependentTable    = null;
    public $ruleKey           = null;

    public $matchTable        = null;
    public $intersectionTable = null;
    public $callerRefRuleKey  = null;
    public $matchRefRuleKey   = null;

    public function findDependentRowset($dependentTable, $ruleKey = null, Zend_Db_Table_Select $select = null)
    {
        $this->dependentTable    = $dependentTable;
        $this->ruleKey           = $ruleKey;
    }

    public function findParentRow($parentTable, $ruleKey = null, Zend_Db_Table_Select $select = null)
    {
        $this->parentTable       = $parentTable;
        $this->ruleKey           = $ruleKey;
    }

    public function findManyToManyRowset($matchTable, $intersectionTable, $callerRefRule = null,
                                         $matchRefRule = null, Zend_Db_Table_Select $select = null)
    {
        $this->matchTable        = $matchTable;
        $this->intersectionTable = $intersectionTable;
        $this->callerRefRuleKey  = $callerRefRule;
        $this->matchRefRuleKey   = $matchRefRule;
    }

    protected function _transformColumn($columnName)
    {
        // convert 'columnFoo' to 'column_foo'
        $columnName = strtolower(preg_replace('/([A-Z])/', '_$1', $columnName));
        return $columnName;
    }

}
