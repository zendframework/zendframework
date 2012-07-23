<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * Abstract factory for Zend\Cloud resources
 *
 * @category   Zend
 * @package    Zend_Cloud
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
