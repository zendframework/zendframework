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
 * @namespace
 */
namespace ZendTest\Db\Table\TestAsset\Row;
use Zend\Db\Table\Select;


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TestMockRow extends \Zend\Db\Table\AbstractRow
{

    public $parentTable       = null;
    public $dependentTable    = null;
    public $ruleKey           = null;

    public $matchTable        = null;
    public $intersectionTable = null;
    public $callerRefRuleKey  = null;
    public $matchRefRuleKey   = null;

    public function findDependentRowset($dependentTable, $ruleKey = null, Select $select = null)
    {
        $this->dependentTable    = $dependentTable;
        $this->ruleKey           = $ruleKey;
    }

    public function findParentRow($parentTable, $ruleKey = null, Select $select = null)
    {
        $this->parentTable       = $parentTable;
        $this->ruleKey           = $ruleKey;
    }

    public function findManyToManyRowset($matchTable, $intersectionTable, $callerRefRule = null,
                                         $matchRefRule = null, Select $select = null)
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
