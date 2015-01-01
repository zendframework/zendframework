<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Ddl\Column;

use Zend\Db\Sql\Ddl\Column\Varchar;

class VarcharTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zend\Db\Sql\Ddl\Column\Varchar::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Varchar('foo', 20);
        $this->assertEquals(
            array(array('%s %s NOT NULL', array('foo', 'VARCHAR(20)'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );

        $column->setDefault('bar');
        $this->assertEquals(
            array(array('%s %s NOT NULL DEFAULT %s', array('foo', 'VARCHAR(20)', 'bar'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL, $column::TYPE_VALUE))),
            $column->getExpressionData()
        );
    }
}
