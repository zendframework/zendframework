<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Db\Adapter\Platform;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SqlServer implements PlatformInterface
{
    /**
     * Get name
     * 
     * @return string 
     */
    public function getName()
    {
        return 'SQLServer';
    }
    /**
     * Get quote identifier symbol
     * 
     * @return string 
     */
    public function getQuoteIdentifierSymbol()
    {
        return array('[', ']');
    }
    /**
     * Quote identifier
     * 
     * @param  string $identifier
     * @return string 
     */
    public function quoteIdentifier($identifier)
    {
        return '[' . $identifier . ']';
    }
    /**
     * Get quote value symbol
     * 
     * @return string 
     */
    public function getQuoteValueSymbol()
    {
        return '\'';
    }
    /**
     * Quote value
     * 
     * @param  string $value
     * @return string 
     */
    public function quoteValue($value)
    {
        return '\'' . str_replace('\'', '\'\'', $value) . '\'';
    }
    /**
     * Get identifier separator
     * 
     * @return string 
     */
    public function getIdentifierSeparator()
    {
        return '.';
    }
    /**
     * Quote identifier in fragment
     * 
     * @param  string $identifier
     * @param  array $safeWords
     * @return string 
     */
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
                    $parts[$i] = '[' . $part . ']';
            }
        }
        return implode('', $parts);
    }
}