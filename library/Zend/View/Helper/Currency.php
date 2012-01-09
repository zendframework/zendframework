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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;
use Zend;
use Zend\Locale;

/**
 * Currency view helper
 *
 * @uses      \Zend\Currency\Currency
 * @uses      \Zend\Locale\Locale
 * @uses      \Zend\Registry
 * @uses      \Zend\View\Helper\AbstractHelper
 * @category  Zend
 * @package   Zend_View
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Currency extends AbstractHelper
{
    /**
     * Currency object
     *
     * @var \Zend\Currency\Currency
     */
    protected $_currency;

    /**
     * Constructor for manually handling
     *
     * @param  \Zend\Currency\Currency $currency Instance of \Zend\Currency\Currency
     * @return void
     */
    public function __construct($currency = null)
    {
        if ($currency === null) {
            if (\Zend\Registry::isRegistered('Zend_Currency')) {
                $currency = \Zend\Registry::get('Zend_Currency');
            }
        }

        $this->setCurrency($currency);
    }

    /**
     * Output a formatted currency
     *
     * @param  integer|float                    $value    Currency value to output
     * @param  string|Zend_Locale|\Zend\Currency\Currency $currency OPTIONAL Currency to use for this call
     * @return string Formatted currency
     */
    public function __invoke($value = null, $currency = null)
    {
        if ($value === null) {
            return $this;
        }

        if (is_string($currency) || ($currency instanceof Locale\Locale)) {
            if (Locale\Locale::isLocale($currency)) {
                $currency = array('locale' => $currency);
            }
        }

        if (is_string($currency)) {
            $currency = array('currency' => $currency);
        }

        if (is_array($currency)) {
            return $this->_currency->toCurrency($value, $currency);
        }

        return $this->_currency->toCurrency($value);
    }

    /**
     * Sets a currency to use
     *
     * @param  Zend_Currency|String|\Zend\Locale\Locale $currency Currency to use
     * @throws \Zend\View\Exception When no or a false currency was set
     * @return \Zend\View\Helper\Currency
     */
    public function setCurrency($currency = null)
    {
        if (!$currency instanceof \Zend\Currency\Currency) {
            $currency = new \Zend\Currency\Currency($currency);
        }
        $this->_currency = $currency;

        return $this;
    }

    /**
     * Retrieve currency object
     *
     * @return \Zend\Currency\Currency|null
     */
    public function getCurrency()
    {
        return $this->_currency;
    }
}
