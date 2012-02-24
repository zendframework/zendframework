<?php

namespace Zend\Db\Adapter\Platform;

class Sqlite implements PlatformInterface
{
    public function getName()
    {
        return 'SQLite';
    }
    
    public function getQuoteIdentifierSymbol()
    {
        return '"';
    }
    
    public function quoteIdentifier($identifier)
    {
        return '"' . str_replace('"', '\\' . '"', $identifier) . '"';
    }
    
    public function getQuoteValueSymbol()
    {
        return '\'';
    }
    
    public function quoteValue($value)
    {
        return '\'' . str_replace('\'', '\\' . '\'', $value) . '\'';
    }

    public function getIdentifierSeparator()
    {
        return '.';
    }

    public function quoteIdentifierWithSeparator($identifier)
    {
        $parts = preg_split('#([\.\s])#', $identifier, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach($parts as $i => $part) {
            switch ($part) {
                case ' ':
                case '.':
                case 'AS':
                case 'As':
                case 'aS':
                case 'as':
                    break;
                default:
                    $parts[$i] = '"' . str_replace('"', '\\' . '"', $identifier) . '"';
            }
        }
        return implode('', $parts);
    }
}