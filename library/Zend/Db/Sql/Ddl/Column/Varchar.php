<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Column;

class Varchar extends Column
{
    protected $specification = '%1$s VARCHAR(%2$s)%3$s%4$s';
    protected $length;

    public function __construct($name, $length)
    {
        $this->name = $name;
        $this->length = $length;
    }

    public function getExpressionData()
    {
        $spec = $this->specification;

        $params = array();

        $types = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);
        $params[] = $this->name;
        $params[] = $this->length;


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