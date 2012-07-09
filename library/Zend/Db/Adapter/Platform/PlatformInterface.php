<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Platform;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
interface PlatformInterface
{
    public function getName();
    public function getQuoteIdentifierSymbol();
    public function quoteIdentifier($identifier);
    public function quoteIdentifierChain($identifierChain);
    public function getQuoteValueSymbol();
    public function quoteValue($value);
    public function quoteValueList($valueList);
    public function getIdentifierSeparator();
    public function quoteIdentifierInFragment($identifier, array $additionalSafeWords = array());
}
