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
    /**
     * @var string
     */
    protected $specification = '%s %s';

    /**
     * @var null
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $type = 'INTEGER';

    /**
     * @var bool
     */
    protected $isNullable = false;

    /**
     * @var null
     */
    protected $default = null;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        (!$name) ?: $this->setName($name);
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $nullable
     * @return $this
     */
    public function setNullable($nullable)
    {
        $this->isNullable = (bool) $nullable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return $this->isNullable;
    }

    /**
     * @param $default
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return null
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
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
