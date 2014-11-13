<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Platform;

abstract class AbstractPlatform implements PlatformInterface
{
    /**
     * @var string[]
     */
    protected $quoteIdentifier = array('"', '"');

    /**
     * @var string
     */
    protected $quoteIdentifierTo = '\'';

    /**
     * @var bool
     */
    protected $quoteIdentifiers = true;

    /**
     * Quote identifier in fragment
     *
     * @param  string $identifier
     * @param  array $safeWords
     * @return string
     */
    public function quoteIdentifierInFragment($identifier, array $safeWords = array())
    {
        if (! $this->quoteIdentifiers) {
            return $identifier;
        }

        $safeRegex    = '';
        $safeWordsInt = array('*' => true, ' ' => true, '.' => true, 'as' => true);

        foreach($safeWords as $sWord) {
            $safeWordsInt[strtolower($sWord)] = true;

            $safeRegex .= '|' . preg_quote($sWord);
        }

        $parts = preg_split(
            '/([\.\s]' . $safeRegex . ')/i',
            $identifier,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $identifier = '';

        foreach ($parts as $part) {
            $identifier .= isset($safeWordsInt[strtolower($part)])
                    ? $part
                    : $this->quoteIdentifier[0]
                        . str_replace($this->quoteIdentifier[0], $this->quoteIdentifierTo, $part)
                        . $this->quoteIdentifier[1];
        }

        return $identifier;
    }

    /**
     * Quote identifier
     *
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        if (! $this->quoteIdentifiers) {
            return $identifier;
        }

        return $this->quoteIdentifier[0]
            . str_replace($this->quoteIdentifier[0], $this->quoteIdentifierTo, $identifier)
            . $this->quoteIdentifier[1];
    }

    /**
     * Get quote indentifier symbol
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return $this->quoteIdentifier[0];
    }

    /**
     * Quote value list
     *
     * @param string|string[] $valueList
     * @return string
     */
    public function quoteValueList($valueList)
    {
        if (! is_array($valueList)) {
            return $this->quoteValue($valueList);
        }

        return implode(', ', array_map(array($this, 'quoteValue'), $valueList));
    }
}
