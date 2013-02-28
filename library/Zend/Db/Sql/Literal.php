<?php

namespace Zend\Db\Sql;

class Literal implements ExpressionInterface
{
    /**
     * @var string
     */
    protected $literal = '';

    /**
     * @param $literal
     */
    public function __construct($literal = '')
    {
        $this->literal = $literal;
    }

    /**
     * @param string $literal
     * @return Literal
     */
    public function setLiteral($literal)
    {
        $this->literal = $literal;
        return $this;
    }

    /**
     * @return string
     */
    public function getLiteral()
    {
        return $this->literal;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        return array(array(
            str_replace('%', '%%', $this->literal),
            array(),
            array()
        ));
    }
}
