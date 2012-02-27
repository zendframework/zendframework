<?php

namespace Zend\Db\Adapter\Platform;

class Mysql implements PlatformInterface
{
    public function getName()
    {
        return 'MySQL';
    }
    
    public function getQuoteIdentifierSymbol()
    {
        return '`';
    }
    
    public function quoteIdentifier($identifier)
    {
        return '`' . str_replace('`', '\\' . '`', $identifier) . '`';
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

    public function quoteIdentifierInFragment($identifier, array $safeWords = array())
    {
        $parts = preg_split('#([\.\s])#', $identifier, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach($parts as $i => $part) {
            if ($safeWords && in_array($part, $safeWords)) {
                continue;
            }
            switch ($part) {
                case ' ':
                case '.':
                case '*':
                case 'AS':
                case 'As':
                case 'aS':
                case 'as':
                    break;
                default:
                    $parts[$i] = '`' . str_replace('`', '\\' . '`', $part) . '`';
            }
        }
        return implode('', $parts);
    }
}