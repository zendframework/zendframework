<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Column;

class Column implements ColumnInterface
{
    protected $specification = '%s %s';

    protected $name = null;
    protected $type = 'INTEGER';
    protected $isNullable = false;
    protected $default = null;
    protected $options = array();

    public function __construct($name = null)
    {
        (!$name) ?: $this->setName($name);
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setNullable($nullable)
    {
        $this->isNullable = (bool) $nullable;
        return $this;
    }

    public function isNullable()
    {
        return $this->isNullable;
    }

    public function setDefault($default)
    {
        $this->default = $default;
    }

    public function getDefault()
    {
        return $this->default;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }



    public function getExpressionData()
    {
        $spec = $this->specification;

        $params = array();
        $params[] = $this->name;
        $params[] = $this->type;

        $types = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);

        if (!$this->isNullable) {
            $params[1] .= ' NOT NULL';
        }

        if ($this->default !== null) {
            $spec .= ' DEFAULT %s';
            $params[] = $this->default;
            $types[] = self::TYPE_VALUE;
        }

        return array(array(
            $spec,
            $params,
            $types
        ));

    }

}
