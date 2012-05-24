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
 * @package    Zend_Cloud
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cloud;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * Abstract factory for Zend\Cloud resources
 *
 * @category   Zend
 * @package    Zend_Cloud
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AbstractFactory
{
    /**
     * Constructor
     *
     * @return void
     */
    private function __construct()
    {
        // private constructor - should not be used
    }

    /**
     * Get an individual adapter instance
     *
     * @param  string $adapterOption
     * @param  array|Traversable $options
     * @return null|DocumentService\Adapter\AdapterInterface|QueueService\Adapter\AdapterInterface|StorageService\Adapter\AdapterInterface|Infrastructure\Adapter\AdapterInterface
     */
    protected static function _getAdapter($adapterOption, $options)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!isset($options[$adapterOption])) {
            return null;
        }

        $classname = $options[$adapterOption];
        unset($options[$adapterOption]);

        return new $classname($options);
    }
}
