<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Ddl\Column;

use Zend\Db\Sql\Ddl\Column\Integer;
use Zend\Db\Sql\Ddl\Constraint\PrimaryKey;

class IntegerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Zend\Db\Sql\Ddl\Column\Integer::__construct
     */
    public function testObjectConstruction()
    {
        $integer = new Integer('foo');
        $this->assertEquals('foo', $integer->getName());
    }

    /**
     * @covers Zend\Db\Sql\Ddl\Column\Column::getExpressionData
     */
    public function testGetExpressionData()
    {
        $column = new Integer('foo');
        $this->assertEquals(
            array(array('%s %s NOT NULL', array('foo', 'INTEGER'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL))),
            $column->getExpressionData()
        );

        $column = new Integer('foo');
        $column->addConstraint(new PrimaryKey());
        $this->assertEquals(
            array(
                array('%s %s NOT NULL', array('foo', 'INTEGER'), array($column::TYPE_IDENTIFIER, $column::TYPE_LITERAL)),
                ' ',
                array('PRIMARY KEY', array(), array())
            ),
            $column->getExpressionData()
        );
    }
}
