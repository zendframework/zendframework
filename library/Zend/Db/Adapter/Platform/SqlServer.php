<?php

namespace Zend\Db\Adapter\Platform;

class SqlServer implements \Zend\Db\Adapter\PlatformInterface
{
    public function getName()
    {
        return 'SQLServer';
    }

    public function getQuoteIdentifierSymbol()
    {
        return array('[', ']');
    }

    public function quoteIdentifier($identifier)
    {
        return '[' . $identifier . ']';
    }

    public function getQuoteValueSymbol()
    {
        return '\'';
    }

    public function quoteValue($value)
    {
        return '\'' . str_replace('\'', '\'\'', $value) . '\'';
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
                    $parts[$i] = '[' . $part . ']';
            }
        }
        return implode('', $parts);
    }
}