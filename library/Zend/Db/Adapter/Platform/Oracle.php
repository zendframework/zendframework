<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Platform;

class Oracle extends AbstractPlatform
{
    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        if (isset($options['quote_identifiers'])
            && ($options['quote_identifiers'] == false
            || $options['quote_identifiers'] === 'false')
        ) {
            $this->quoteIdentifiers = false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'Oracle';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteIdentifierChain($identifierChain)
    {
        if ($this->quoteIdentifiers === false) {
            return implode('.', (array) $identifierChain);
        }

        return '"' . implode('"."', (array) str_replace('"', '\\"', $identifierChain)) . '"';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteValue($value)
    {
        trigger_error(
            'Attempting to quote a value in ' . __CLASS__ . ' without extension/driver support '
                . 'can introduce security vulnerabilities in a production environment.'
        );
        return '\'' . addcslashes(str_replace('\'', '\'\'', $value), "\x00\n\r\"\x1a") . '\'';
    }

    /**
     * {@inheritDoc}
     */
    public function quoteTrustedValue($value)
    {
        return '\'' . addcslashes(str_replace('\'', '\'\'', $value), "\x00\n\r\"\x1a") . '\'';
    }
}
