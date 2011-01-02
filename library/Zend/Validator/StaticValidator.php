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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Validator;

use Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StaticValidator
{
    /**
     * @var Zend\Loader\Broker
     */
    protected static $broker;

    /**
     * Set plugin broker to use for locating validators
     * 
     * @param  Broker|null $broke 
     * @return void
     */
    public static function setBroker(Broker $broker = null)
    {
        self::$broker = $broker;
    }

    /**
     * Get plugin broker for locating validators
     * 
     * @return Broker
     */
    public static function getBroker()
    {
        if (null === self::$broker) {
            static::setBroker(new ValidatorBroker());
        }
        return self::$broker;
    }

    /**
     * @param  mixed    $value
     * @param  string   $classBaseName
     * @param  array    $args          OPTIONAL
     * @return boolean
     * @throws \Zend\Validator\Exception
     */
    public static function execute($value, $classBaseName, array $args = array())
    {
        $broker = static::getBroker();

        $validator = $broker->load($classBaseName, $args);
        $result    = $validator->isValid($value);

        // Unregister validator in case different args are used on later invocation
        $broker->unregister($classBaseName);

        return $result;
    }
}
