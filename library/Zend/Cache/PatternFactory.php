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
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache;

use Traversable,
    Zend\Loader\Broker,
    Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PatternFactory
{
    /**
     * The pattern broker
     *
     * @var null|Broker
     */
    protected static $broker = null;

    /**
     * Instantiate a cache pattern
     *
     * @param  string|Pattern\PatternInterface $patternName
     * @param  array|Traversable|Pattern\PatternOptions $options
     * @return Pattern\PatternInterface
     * @throws Exception\RuntimeException
     */
    public static function factory($patternName, $options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (is_array($options)) {
            $options = new Pattern\PatternOptions($options);
        }
        if (!$options instanceof Pattern\PatternOptions) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array, Traversable object, or %s\Pattern\PatternOptions object; received "%s"',
                __METHOD__,
                __NAMESPACE__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        if ($patternName instanceof Pattern\PatternInterface) {
            $patternName->setOptions($options);
            return $patternName;
        }

        $pattern = static::getBroker()->load($patternName);
        $pattern->setOptions($options);
        return $pattern;
    }

    /**
     * Get the pattern broker
     *
     * @return Broker
     */
    public static function getBroker()
    {
        if (static::$broker === null) {
            static::$broker = new PatternBroker();
            static::$broker->setRegisterPluginsOnLoad(false);
        }

        return static::$broker;
    }

    /**
     * Set the pattern broker
     *
     * @param  Broker $broker
     * @return void
     */
    public static function setBroker(Broker $broker)
    {
        static::$broker = $broker;
    }

    /**
     * Reset pattern broker to default
     *
     * @return void
     */
    public static function resetBroker()
    {
        static::$broker = null;
    }
}
