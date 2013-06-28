<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

class BaseName extends AbstractFilter
{
    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns basename($value)
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        if(!is_scalar($value)){
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects parameter to be scalar, "%s" given',
                __METHOD__,
                (is_object($value) ? get_class($value) : gettype($value))
            ));
        }

        return basename((string) $value);
    }
}
