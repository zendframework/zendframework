<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\I18n\Filter;

use Locale;
use Zend\Filter\AbstractFilter;

abstract class AbstractLocale extends AbstractFilter
{
    /**
     * Sets the locale option
     *
     * @param  string|null $locale
     * @return AbstractLocale
     */
    public function setLocale($locale = null)
    {
        $this->options['locale'] = $locale;
        return $this;
    }

    /**
     * Returns the locale option
     *
     * @return string
     */
    public function getLocale()
    {
        if (!isset($this->options['locale'])) {
            $this->options['locale'] = Locale::getDefault();
        }
        return $this->options['locale'];
    }
}
