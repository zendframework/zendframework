<?php

namespace Zend\Db\Adapter;

interface PlatformInterface
{
    public function getName();
    public function getQuoteIdentifierSymbol();
    public function quoteIdentifier($identifier);
    public function getQuoteValueSymbol();
    public function quoteValue($value);
    public function getIdentifierSeparator();
    public function quoteIdentifierWithSeparator($identifier);
}
