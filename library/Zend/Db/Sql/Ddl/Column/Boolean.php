<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Column;

class Boolean extends Column
{
    protected $specification = '%1$s TINYINT NOT NULL';

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getExpressionData()
    {
        $spec = $this->specification;

        $params = array($this->name);
        $types = array(self::TYPE_IDENTIFIER);

        return array(array(
            $spec,
            $params,
            $types
        ));

    }
}