<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\Strategy;

final class ExplodeStrategy implements StrategyInterface
{
    /**
     * @var string
     */
    private $valueDelimiter = ',';

    /**
     * @var int|null
     */
    private $explodeLimit = null;

    /**
     * Constructor
     *
     * @param string|null $delimiter    String that the values will be split upon
     * @param string|null $explodeLimit Explode limit
     */
    public function __construct($delimiter = null, $explodeLimit = null)
    {
        if ($delimiter !== null) {
            $this->setValueDelimiter($delimiter);
        }
        if ($explodeLimit !== null) {
            $this->explodeLimit = (int) $explodeLimit;
        }
    }

    /**
     * Sets the delimiter string that the values will be split upon
     *
     * @param  string $delimiter
     * @return self
     */
    private function setValueDelimiter($delimiter)
    {
        if (!is_string($delimiter)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects Delimiter to be string, %s provided instead',
                __METHOD__,
                is_object($delimiter) ? get_class($delimiter) : gettype($delimiter)
            ));
        }

        if ($delimiter === '') {
            throw new Exception\InvalidArgumentException('Delimiter cannot be empty.');
        }

        $this->valueDelimiter = $delimiter;
    }

    /**
     * Split a string by delimiter
     *
     * @param  string|null                        $value The original value.
     * @return array                              Returns the value that should be hydrated.
     * @throws Exception\InvalidArgumentException
     */
    public function hydrate($value)
    {
        if ($value === null) {
            return array();
        }

        if (!is_scalar($value)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects argument 1 to be string, %s provided instead',
                __METHOD__,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        if ($this->explodeLimit !== null) {
            return explode($this->valueDelimiter, $value, $this->explodeLimit);
        }

        return explode($this->valueDelimiter, $value);

    }

    /**
     * Join array elements with delimiter
     *
     * @param  array  $value The original value.
     * @return string Returns the value that should be extracted.
     */
    public function extract($value)
    {
        if (!is_array($value)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects argument 1 to be array, %s provided instead',
                __METHOD__,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        return implode($this->valueDelimiter, $value);
    }
}
