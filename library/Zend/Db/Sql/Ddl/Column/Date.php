<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Column;

class Date extends Column
{
    protected $specification = '%1$s DATE %2$s%3$s';

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getExpressionData()
    {
        $spec = $this->specification;

        $params = array();

        $types = array(self::TYPE_IDENTIFIER);
        $params[] = $this->name;


        $types[] = self::TYPE_LITERAL;
        $params[] = (!$this->isNullable) ? ' NOT NULL' : '';

        $types[] = ($this->default !== null) ? self::TYPE_VALUE : self::TYPE_LITERAL;
        $params[] = ($this->default !== null) ? $this->default : '';

        return array(array(
            $spec,
            $params,
            $types
        ));

    }
}