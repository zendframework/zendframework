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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Validator;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StaticValidator
{
    /**
     * @var ValidatorPluginManager
     */
    protected static $plugins;

    /**
     * Set plugin manager to use for locating validators
     * 
     * @param  ValidatorPluginManager|null $plugins
     * @return void
     */
    public static function setPluginManager(ValidatorPluginManager $plugins = null)
    {
        // Don't share by default to allow different arguments on subsequent calls
        if ($plugins instanceof ValidatorPluginManager) {
            $plugins->setShareByDefault(false);
        }
        self::$plugins = $plugins;
    }

    /**
     * Get plugin manager for locating validators
     * 
     * @return ValidatorPluginManager
     */
    public static function getPluginManager()
    {
        if (null === self::$plugins) {
            static::setPluginManager(new ValidatorPluginManager());
        }
        return self::$plugins;
    }

    /**
     * @param  mixed    $value
     * @param  string   $classBaseName
     * @param  array    $args          OPTIONAL
     * @return boolean
     */
    public static function execute($value, $classBaseName, array $args = array())
    {
        $plugins = static::getPluginManager();

        $validator = $plugins->get($classBaseName, $args);
        return $validator->isValid($value);
    }
}
