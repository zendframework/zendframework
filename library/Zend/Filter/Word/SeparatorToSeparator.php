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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Filter\Word;

use Zend\Filter\Exception;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SeparatorToSeparator extends \Zend\Filter\PregReplace
{
    protected $_searchSeparator = null;
    protected $_replacementSeparator = null;

    /**
     * Constructor
     *
     * @param  string  $searchSeparator      Seperator to search for
     * @param  string  $replacementSeperator Seperator to replace with
     */
    public function __construct($searchSeparator = ' ', $replacementSeparator = '-')
    {
        $this->setSearchSeparator($searchSeparator);
        $this->setReplacementSeparator($replacementSeparator);
    }

    /**
     * Sets a new seperator to search for
     *
     * @param  string  $separator  Seperator to search for
     * @return SeparatorToSeparator
     */
    public function setSearchSeparator($separator)
    {
        $this->_searchSeparator = $separator;
        return $this;
    }

    /**
     * Returns the actual set seperator to search for
     *
     * @return  string
     */
    public function getSearchSeparator()
    {
        return $this->_searchSeparator;
    }

    /**
     * Sets a new seperator which replaces the searched one
     *
     * @param  string  $separator  Seperator which replaces the searched one
     * @return SeparatorToSeparator
     */
    public function setReplacementSeparator($separator)
    {
        $this->_replacementSeparator = $separator;
        return $this;
    }

    /**
     * Returns the actual set seperator which replaces the searched one
     *
     * @return  string
     */
    public function getReplacementSeparator()
    {
        return $this->_replacementSeparator;
    }

    /**
     * Defined by Zend\Filter\Filter
     *
     * Returns the string $value, replacing the searched seperators with the defined ones
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        return $this->_separatorToSeparatorFilter($value);
    }

    /**
     * Do the real work, replaces the seperator to search for with the replacement seperator
     *
     * Returns the replaced string
     *
     * @param  string $value
     * @return string
     * @throws Exception\RuntimeException
     */
    protected function _separatorToSeparatorFilter($value)
    {
        if ($this->_searchSeparator == null) {
            throw new Exception\RuntimeException('You must provide a search separator for this filter to work.');
        }

        $this->setPattern('#' . preg_quote($this->_searchSeparator, '#') . '#');
        $this->setReplacement($this->_replacementSeparator);
        return parent::filter($value);
    }

}
