<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql;

use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\TableIdentifier;
use ZendTest\Db\TestAsset\TrustingSql92Platform;

/**
 * Tests for {@see \Zend\Db\Sql\TableIdentifier}
 *
 * @covers \Zend\Db\Sql\TableIdentifier
 */
class TableIdentifierTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTableName()
    {
        $tableIdentifier = new TableIdentifier('foo');

        $this->assertSame('foo', $tableIdentifier->getTable());
    }
}
