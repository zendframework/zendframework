<?php

namespace Zend\Db\Adapter\Platform;

interface PlatformInterface
{
    public function getName();
    public function getQuoteIdentifierSymbol();
    public function quoteIdentifier($identifier);
    public function getQuoteValueSymbol();
    public function quoteValue($value);
    public function getIdentifierSeparator();
    public function quoteIdentifierInFragment($identifier, array $additionalSafeWords = array());
}
