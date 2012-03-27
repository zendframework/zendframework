<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
 */
class CacheControl implements HeaderDescription
{

    protected $directives = array();

    /**
     * Creates a CacheControl object from a headerLine
     *
     * @param string $headerLine
     * @throws Exception\InvalidArgumentException
     * @return CacheControl
     */
    public static function fromString($headerLine)
    {
        $header = new static();

        list($name, $value) = explode(': ', $headerLine, 2);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'cache-control') {
            throw new Exception\InvalidArgumentException('Invalid header line for Cache-Control string: "' . $name . '"');
        }

        // @todo implementation details
        $header->directives = self::parseValue($value);

        return $header;
    }

    public function getFieldName()
    {
        return 'Cache-Control';
    }

    public function isEmpty()
    {
        return empty($this->directives);
    }

    public function addDirective($key, $value = true)
    {
        $this->directives[$key] = $value;
        return $this;
    }

    public function hasDirective($key)
    {
        return array_key_exists($key, $this->directives);
    }

    public function getDirective($key)
    {
        return array_key_exists($key, $this->directives) ? $this->directives[$key] : null;
    }

    public function removeDirective($key)
    {
        unset($this->directives[$key]);
        return $this;
    }

    public function getFieldValue()
    {
        $parts = array();
        ksort($this->directives);
        foreach ($this->directives as $key => $value) {
            if (true === $value) {
                $parts[] = $key;
            } else {
                if (preg_match('#[^a-zA-Z0-9._-]#', $value)) {
                    $value = '"'.$value.'"';
                }
                $parts[] = "$key=$value";
            }
        }
        return implode(', ', $parts);
    }

    public function toString()
    {
        return 'Cache-Control: ' . $this->getFieldValue();
    }

    protected static function parseValue($value)
    {
        $value = trim($value);

        $directives = array();

        // handle empty string early so we don't need a separate start state
        if ($value == '') {
            return $directives;
        }

        $lastMatch = null;

        state_directive:
        switch (self::match(array('[a-zA-Z][a-zA-Z_-]*'), $value, $lastMatch)) {
            case 0:
                $directive = $lastMatch;
                goto state_value;
                break;

            default:
                throw new Exception\InvalidArgumentException('expected DIRECTIVE');
                break;
        }

        state_value:
        switch (self::match(array('="[^"]*"', '=[^",\s;]*'), $value, $lastMatch)) {
            case 0:
                $directives[$directive] = substr($lastMatch, 2, -1);
                goto state_separator;
                break;

            case 1:
                $directives[$directive] = rtrim(substr($lastMatch, 1));
                goto state_separator;
                break;

            default:
                $directives[$directive] = true;
                goto state_separator;
                break;
        }

        state_separator:
        switch (self::match(array('\s*,\s*', '$'), $value, $lastMatch)) {
            case 0:
                goto state_directive;
                break;

            case 1:
                return $directives;
                break;

            default:
                throw new Exception\InvalidArgumentException('expected SEPARATOR or END');
                break;

        }
    }

    protected static function match($tokens, &$string, &$lastMatch)
    {
        foreach ($tokens as $i => $token) {
            if (preg_match('/^'.$token.'/', $string, $matches)) {
                $lastMatch = $matches[0];
                $string = substr($string, strlen($matches[0]));
                return $i;
            }
        }
        return -1;
    }
}
