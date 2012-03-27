<?php

namespace Zend\Http\Header;

/**
 * @throws Exception\InvalidArgumentException
 * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.9
 */
class CacheControl implements HeaderDescription
{

    const SEPARATOR = ", \t";

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
        $directives = array();
        $lastPosition = 0;
        while (false !== ($token = self::tokenizer($value, self::SEPARATOR, $lastPosition))) {
            $directive = explode('=', trim($token));
            if (false === $directive) {
                // explode shouldn't fail
                throw new Exception\InvalidArgumentException(
                	'Invalid header line for Cache-Control string: "' . $value . '"'
                );
            }
            if (preg_match('/^[^a-zA-Z]{1,1}/', $directive[0])) {
                //directives should start with a letter
                throw new Exception\InvalidArgumentException(
                	'Invalid Cache-Control directive: "' . $token . '"'
                );
            }
            $directives[$directive[0]] = true;
            if (isset($directive[1])) {
                if (!preg_match('/^"([^"]*)"|([^a-zA-Z0-9._-]*)$/', $directive[1])) {
                    // the value should either be enclosed in quotes or contain only safe chars
                    throw new Exception\InvalidArgumentException(
                    	'Invalid Cache-Control directive: "' . $token . '"'
                    );
                }
                $directives[$directive[0]] = trim($directive[1], '"');
            }
        }
        return $directives;
    }

    protected static function tokenizer($string, $sep, &$lastPosition)
    {
        $quoted = false;
        $startPosition = $lastPosition;
        $array = str_split($string);
        $length = count($array);
        if ($lastPosition > $length) {
            return false;
        }
        for ($lastPosition; $lastPosition < $length; $lastPosition++) {
            if (!$quoted) {
                if ('"' == $array[$lastPosition]) {
                    $quoted = true;
                } elseif (false !== strpos($sep, $array[$lastPosition])) {
                    if ($startPosition == $lastPosition) {
                        $startPosition = $lastPosition = $lastPosition++;
                        continue;
                    } else {
                        break;
                    }
                }
            } else {
                if ('"' == $array[$lastPosition]) {
                    $quoted = false;
                }
            }
        }
        $return = substr($string, $startPosition, $lastPosition - $startPosition);
        $lastPosition++;
        return $return;
    }
}
