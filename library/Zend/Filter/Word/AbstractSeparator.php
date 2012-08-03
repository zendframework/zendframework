<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace Zend\Filter\Word;

use Zend\Filter\Exception;
use Zend\Filter\PregReplace as PregReplaceFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 */
abstract class AbstractSeparator extends PregReplaceFilter
{

    protected $separator = null;

    /**
     * Constructor
     *
     * @param  string $separator Space by default
     */
    public function __construct($separator = ' ')
    {
        if (is_array($separator)) {
            $temp = ' ';
            if (isset($separator['separator']) && is_string($separator['separator'])) {
                $temp = $separator['separator'];
            }
            $separator = $temp;
        }
        $this->setSeparator($separator);
    }

    /**
     * Sets a new separator
     *
     * @param  string  $separator  Separator
     * @return AbstractSeparator
     * @throws Exception\InvalidArgumentException
     */
    public function setSeparator($separator)
    {
        if (!is_string($separator)) {
            throw new Exception\InvalidArgumentException('"' . $separator . '" is not a valid separator.');
        }
        $this->separator = $separator;
        return $this;
    }

    /**
     * Returns the actual set separator
     *
     * @return  string
     */
    public function getSeparator()
    {
        return $this->separator;
    }
}
