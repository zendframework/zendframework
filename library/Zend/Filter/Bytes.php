<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

use Zend\Filter\Exception\InvalidArgumentException;

final class Bytes extends AbstractFilter
{
    const MODE_BINARY = 'binary';
    const MODE_DECIMAL = 'decimal';

    const TYPE_BITS = 'b';
    const TYPE_BYTES = 'B';

    /**
     * A list of all possible filter modes:
     *
     * @var array
     */
    protected static $modes = array(
        self::MODE_BINARY,
        self::MODE_DECIMAL,
    );

    /**
     * A list of all possible filter types
     *
     * @var array
     */
    protected static $types = array(
        self::TYPE_BITS,
        self::TYPE_BYTES,
    );

    /**
     * A list of standardized binary prefix formats for decimal and binary mode
     * @link https://en.wikipedia.org/wiki/Binary_prefix
     *
     * @var array
     */
    protected static $standardizedPrefixes = array(
        // binary IEC units:
        self::MODE_BINARY => array('', 'Ki', 'Mi', 'Gi', 'Ti', 'Pi', 'Ei', 'Zi', 'Yi'),
        // decimal SI units:
        self::MODE_DECIMAL => array('', 'k', 'M', 'G', 'T', 'P', 'E', 'Z', 'Y'),
    );

    /**
     * Default options:
     *
     * @var array
     */
    protected $options = array(
        'mode'         => self::MODE_DECIMAL,
        'type'         => self::TYPE_BYTES,
        'precision'    => 2,
        'prefixes'     => array(),
    );

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (!static::isOptions($options)) {
            return;
        }

        $this->setOptions($options);
    }

    /**
     * Define the mode of the filter. Possible values can be fount at self::$modes.
     *
     * @param string $mode
     *
     * @throws InvalidArgumentException
     */
    public function setMode($mode)
    {
        $mode = strtolower($mode);
        if (!in_array($mode, self::$modes)) {
            throw new InvalidArgumentException(sprintf('Invalid binary mode: %s', $mode));
        }
        $this->options['mode'] = $mode;
    }

    /**
     * Get current filter mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->options['mode'];
    }

    /**
     * Find out if the filter is in decimal mode.
     *
     * @return bool
     */
    public function isDecimalMode()
    {
        return $this->getMode() == self::MODE_DECIMAL;
    }

    /**
     * Find out if the filter is in binary mode.
     *
     * @return bool
     */
    public function isBinaryMode()
    {
        return $this->getMode() == self::MODE_BINARY;
    }

    /**
     * Define the type of the filter. Possible values can be fount at self::$types.
     *
     * @param string $type One of b or B (bits / bytes)
     *
     * @throws InvalidArgumentException
     */
    public function setType($type)
    {
        if (!in_array($type, self::$types)) {
            throw new InvalidArgumentException(sprintf('Invalid binary type: %s', $type));
        }
        $this->options['type'] = $type;
    }

    /**
     * Get current filter type
     *
     * @return string
     */
    public function getType()
    {
        return $this->options['type'];
    }

    /**
     * Set the precision of the filtered result.
     *
     * @param $precision
     */
    public function setPrecision($precision)
    {
        $this->options['precision'] = (int) $precision;
    }

    /**
     * Get the precision of the filtered result.
     *
     * @return int
     */
    public function getPrecision()
    {
        return $this->options['precision'];
    }

    /**
     * Set the precision of the result.
     *
     * @param array $prefixes
     */
    public function setPrefixes(array $prefixes)
    {
        $this->options['prefixes'] = $prefixes;
    }

    /**
     * Get the predefined prefixes or use the build-in standardized lists of prefixes.
     *
     * @return array
     */
    public function getPrefixes()
    {
        $prefixes = $this->options['prefixes'];
        if ($prefixes) {
            return $prefixes;
        }

        return self::$standardizedPrefixes[$this->getMode()];
    }

    /**
     * Find the prefix at a specific location in the prefixes array.
     *
     * @param $index
     *
     * @return string|null
     */
    public function getPrefixAt($index)
    {
        $prefixes = $this->getPrefixes();
        return isset($prefixes[$index]) ? $prefixes[$index] : null;
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns a human readable format of the amount of bits or bytes.
     *
     * If the value provided is not numeric, the value will remain unfiltered
     *
     * @param  string $value
     * @return string|mixed
     */
    public function filter($value)
    {
        if (!is_numeric($value)) {
            return $value;
        }

        // Parse to float and check if value is not zero
        $amount = (float) $value;
        if ($amount == 0) {
            return $amount . $this->getType();
        }

        // Calculate the correct size and prefix:
        $orderOfMagnitude = $this->isBinaryMode() ? 1024 : 1000;
        $power = floor(log($amount, $orderOfMagnitude));
        $result = ($amount / pow($orderOfMagnitude, $power));
        $formatted = number_format($result, $this->getPrecision());
        $prefix = $this->getPrefixAt((int)$power);

        // When the amount is too big, no prefix can be found:
        if (is_null($prefix)) {
            return $amount . $this->getType();
        }

        // return formatted value:
        return $formatted . $prefix . $this->getType();
    }
}
