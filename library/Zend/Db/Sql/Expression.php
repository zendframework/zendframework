<?php

namespace Zend\Db\Sql;

class Expression implements ExpressionInterface
{
    protected $pseudoMarkers = array('?');

    protected $expression = '';
    protected $parameters = array();
    protected $types = array();

    public function __construct($expression = null, $parameters = null, array $types = array())
    {
        if ($expression) {
            $this->setExpression($expression);
        }
        if ($parameters) {
            $this->setParameters($parameters);
        }
        if ($types) {
            $this->setTypes($types);
        }
    }

    public function setExpression($expression)
    {
        if (!is_string($expression) || $expression == '') {
            throw new Exception\InvalidArgumentException('Supplied expression must be a string.');
        }
        $this->expression = $expression;
        return $this;
    }

    public function getExpression()
    {
        return $this->expression;
    }

    public function setParameters($parameters)
    {
        if (!is_string($parameters) && !is_array($parameters)) {
            throw new Exception\InvalidArgumentException('Expression parameters must be a string or array.');
        }
        $this->parameters = $parameters;
        return $this;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function setTypes(array $types)
    {
        $this->types = $types;
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function getExpressionData()
    {
        $parameters = (is_string($this->parameters)) ? $this->parameters : array($this->parameters);

        $types = array();
        for ($i = 0; $i < count($parameters); $i++) {
            $types[$i] = (isset($this->types[$i]) && $this->types[$i] == self::TYPE_IDENTIFIER)
                ? self::TYPE_IDENTIFIER : self::TYPE_VALUE;
        }

        $expression = str_replace($this->pseudoMarkers, '%s', $this->expression);

        return array(
            $expression,
            $parameters,
            $types
        );
    }

}