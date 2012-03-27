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
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application\Resource;

use Zend\Locale as SystemLocale;

/**
 * Resource for initializing the locale
 *
 * @uses       \Zend\Application\Resource\AbstractResource
 * @uses       \Zend\Locale\Locale
 * @uses       \Zend\Registry
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Locale extends AbstractResource
{
    const DEFAULT_REGISTRY_KEY = 'Zend_Locale';

    /**
     * @var \Zend\Locale\Locale
     */
    protected $_locale;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return \Zend\Locale\Locale
     */
    public function init()
    {
        return $this->getLocale();
    }

    /**
     * Retrieve locale object
     *
     * @return \Zend\Locale\Locale
     */
    public function getLocale()
    {
        if (null === $this->_locale) {
            $options = $this->getOptions();
            if(!isset($options['default'])) {
                $this->_locale = new SystemLocale\Locale();
            } elseif(!isset($options['force']) ||
                     (bool) $options['force'] == false)
            {
                // Don't force any locale, just go for auto detection
                SystemLocale\Locale::setFallback($options['default']);
                $this->_locale = new SystemLocale\Locale();
            } else {
                $this->_locale = new SystemLocale\Locale($options['default']);
            }

            $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
                ? $options['registry_key']
                : self::DEFAULT_REGISTRY_KEY;
            \Zend\Registry::set($key, $this->_locale);
        }

        return $this->_locale;
    }
}
