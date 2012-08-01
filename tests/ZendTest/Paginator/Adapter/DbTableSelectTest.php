<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace ZendTest\Paginator\Adapter;

use Zend\Paginator\Adapter;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @group      Zend_Paginator
 */
class DbTableSelectTest extends \ZendTest\Paginator\Adapter\DbSelectTest
{
    /**
     * @group ZF-3775
     */
    public function testSelectDoesReturnZendDbTableRowset()
    {
        $query   = $this->_table->select();
        $adapter = new Adapter\DbTableSelect($query);
        $items   = $adapter->getItems(0, 10);

        $this->assertInstanceOf('Zend\\Db\\Table\\Rowset', $items);
    }

    public function testToJsonWithRowset()
    {
        $query   = $this->_table->select();
        $paginator = new \Zend\Paginator\Paginator(new Adapter\DbTableSelect($query));
        $this->assertGreaterThan(2, strlen($paginator->toJson()));
    }
}
