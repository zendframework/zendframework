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
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Paginator\Adapter;

use Zend\Paginator\Adapter;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
