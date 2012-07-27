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

/**
 * @category   Zend
 * @package    Zend_Filter
 */
class DashToSeparator extends AbstractSeparator
{
    /**
     * Defined by Zend\Filter\Filter
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $this->setPattern('#-#');
        $this->setReplacement($this->separator);
        return parent::filter($value);
    }
}
