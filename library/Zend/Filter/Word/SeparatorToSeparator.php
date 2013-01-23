<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace Zend\Filter\Word;

use Zend\Filter\Exception;

/**
 * @category   Zend
 * @package    Zend_Filter
 */
class SeparatorToSeparator extends \Zend\Filter\PregReplace
{
    protected $searchSeparator = null;
    protected $replacementSeparator = null;

    /**
     * Constructor
     *
     * @param  string  $searchSeparator      Separator to search for
     * @param  string  $replacementSeparator Separator to replace with
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
        $this->searchSeparator = $separator;
        return $this;
    }

    /**
     * Returns the actual set separator to search for
     *
     * @return  string
     */
    public function getSearchSeparator()
    {
        return $this->searchSeparator;
    }

    /**
     * Sets a new separator which replaces the searched one
     *
     * @param  string  $separator  Separator which replaces the searched one
     * @return SeparatorToSeparator
     */
    public function setReplacementSeparator($separator)
    {
        $this->replacementSeparator = $separator;
        return $this;
    }

    /**
     * Returns the actual set separator which replaces the searched one
     *
     * @return  string
     */
    public function getReplacementSeparator()
    {
        return $this->replacementSeparator;
    }

    /**
     * Defined by Zend\Filter\Filter
     *
     * Returns the string $value, replacing the searched separators with the defined ones
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
        if ($this->searchSeparator == null) {
            throw new Exception\RuntimeException('You must provide a search separator for this filter to work.');
        }

        $this->setPattern('#' . preg_quote($this->searchSeparator, '#') . '#');
        $this->setReplacement($this->replacementSeparator);
        return parent::filter($value);
    }
}
