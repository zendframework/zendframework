<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Column;

abstract class AbstractPrecisionColumn extends AbstractLengthColumn
{
    /**
     * @var int
     */
    protected $decimal;

    /**
     * @param null|string $name
     * @param int $digits
     * @param null|int $decimal
     */
    public function __construct($name, $digits = null, $decimal = null)
    {
        parent::__construct($name, $digits);
        $this->decimal = $decimal;
    }

    /**
     * @param  int $digits
     * @return self
     */
    public function setDigits($digits)
    {
        return $this->setLength($digits);
    }

    /**
     * @return int
     */
    public function getDigits()
    {
        return $this->getLength();
    }

    /**
     * @param int $decimal
     * @return self
     */
    public function setDecimal($decimal)
    {
        $this->decimal = $decimal;
        return $this;
    }

    /**
     * @return int
     */
    public function getDecimal()
    {
        return $this->decimal;
    }

    /**
     * @return string
     */
    public function getLengthExpression()
    {
        $expr = $this->length;
        if ($this->decimal !== null) {
            $expr .= ',' . $this->decimal;
        }
        return $expr;
    }
}
