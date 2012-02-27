<?php

namespace Zend\Db\Sql\Predicate;

class Literal implements PredicateInterface
{
    protected $literal = '';
    protected $parameter = null;
    
    public function __construct($literal = null, $parameter = null)
    {
        if ($literal) {
            $this->setLiteral($literal);
        }
        if ($parameter) {
            $this->setParameter($parameter);
        }
    }
    
    public function setLiteral($literal)
    {
        $this->literal = $literal;
    }

    public function getLiteral()
    {
        return $this->literal;
    }

    public function setParameter($parameter)
    {
        if (!is_array($parameter)) {
            $parameter = array($parameter);
        }
        $this->parameter = $parameter;
    }

    public function getParameter()
    {
        return $this->parameter;
    }


    public function getWhereParts()
    {
        $spec = $this->literal;
        if ($this->parameter) {
            $values = array_fill(0, count($this->parameter), self::TYPE_VALUE);
            $spec = preg_replace('/\?/', '%s', $spec);
            return array(
                array($spec, $this->parameter, $values)
            );
        } else {
            return array($spec);
        }
    }
}
