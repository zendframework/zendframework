<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Column;

abstract class AbstractLengthColumn extends Column
{
    /**
     * @var int
     */
    protected $length;

    /**
     * @param null|string $name
     * @param int $length
     */
    public function __construct($name, $length)
    {
        parent::__construct($name);
        $this->length = $length;
    }

    /**
     * @param  int $length
     * @return self
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return string
     */
    public function getLengthExpression()
    {
        return (string) $this->length;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        $spec = $this->specification;

        $params   = array();
        $params[] = $this->name;
        $params[] = $this->type;

        if ($this->getLengthExpression()) {
            $params[1] .= '(' . $this->getLengthExpression() . ')';
        }

        $types = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);

        if (!$this->isNullable) {
            $spec .= ' NOT NULL';
        }

        if ($this->default !== null) {
            $spec    .= ' DEFAULT %s';
            $params[] = $this->default;
            $types[]  = self::TYPE_VALUE;
        }

        return array(array(
            $spec,
            $params,
            $types,
        ));
    }
}
