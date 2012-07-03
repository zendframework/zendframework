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
 * @package    Zend_I18n
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\I18n\View;

use Zend\ServiceManager\ConfigurationInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Service manager configuration for i18n view helpers.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HelperConfiguration implements ConfigurationInterface
{
    /**
     * @var array Pre-aliased view helpers
     */
    protected $invokables = array(
        'currencyformat'  => 'Zend\I18n\View\Helper\CurrencyFormat',
        'dateformat'      => 'Zend\I18n\View\Helper\DateFormat',
        'numberformat'    => 'Zend\I18n\View\Helper\NumberFormat',
        'translate'       => 'Zend\I18n\View\Helper\Translate',
        'translateplural' => 'Zend\I18n\View\Helper\TranslatePlural',
    );

    /**
     * Configure the provided service manager instance with the configuration
     * in this class.
     *
     * @param  ServiceManager $serviceManager
     * @return void
     */
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        foreach ($this->invokables as $name => $service) {
            $serviceManager->setInvokableClass($name, $service);
        }
    }
}
