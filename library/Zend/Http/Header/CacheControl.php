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
        $header->directives = self::parseCacheControl($value);

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

    protected static function parseCacheControl($value)
    {
        $directives = array();
        preg_match_all('#([a-zA-Z][a-zA-Z_-]*)\s*(?:=(?:"([^"]*)"|([^ \t",;]*)))?#', $value, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $directives[strtolower($match[1])] = isset($match[2]) && $match[2] ? $match[2] : (isset($match[3]) ? $match[3] : true);
        }
        return $directives;
    }
}
