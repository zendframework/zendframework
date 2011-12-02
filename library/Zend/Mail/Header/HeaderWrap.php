<?php

namespace Zend\Mail\Header;

abstract class HeaderWrap
{
    /**
     * Wrap a long header line
     * 
     * @param  string $value 
     * @param  HeaderDescription $header 
     * @return string
     */
    public static function wrap($value, HeaderDescription $header)
    {
        if ($header instanceof UnstructuredHeader) {
            return static::wrapUnstructuredHeader($value);
        } elseif ($header instanceof StructuredHeader) {
            return static::wrapStructuredHeader($value, $header);
        }
        return $value;
    }

    /**
     * Wrap an unstructured header line
     *
     * Wrap at 78 characters or before, based on whitespace.
     * 
     * @param  string $value 
     * @return string
     */
    protected static function wrapUnstructuredHeader($value)
    {
        return wordwrap($value, 78, "\r\n ");
    }

    /**
     * Wrap a structured header line
     * 
     * @param  string $value 
     * @param  HeaderDescription $header 
     * @return string
     */
    protected static function wrapStructuredHeader($value, HeaderDescription $header)
    {
        $delimiter = $header->getDelimiter();

        $length = strlen($value);
        $lines  = array();
        $temp   = '';
        for ($i = 0; $i < $length; $i++) {
            $temp .= $value[$i];
            if ($value[$i] == $delimiter) {
                $lines[] = $temp;
                $temp    = '';
            }
        }
        return implode("\r\n ", $lines);
    }
}
