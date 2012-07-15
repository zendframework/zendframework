<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\Infrastructure;

use Zend\Cloud\AbstractFactory;
use Zend\Cloud\Exception\InvalidArgumentException;

/**
 * Factory for infrastructure adapters
 *
 * @package    Zend_Cloud
 * @subpackage Infrastructure
 */
class Factory extends AbstractFactory
{
    const INFRASTRUCTURE_ADAPTER_KEY = 'infrastructure_adapter';

    /**
     * @var string Interface which adapter must implement to be considered valid
     */
    protected static $_adapterInterface = 'Zend\Cloud\Infrastructure\Adapter\AdapterInterface';

    /**
     * Constructor
     *
     * Private constructor - should not be used
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Retrieve an adapter instance
     *
     * @param  array $options
     * @return void
     */
    public static function getAdapter($options = array())
    {
        $adapter = parent::_getAdapter(self::INFRASTRUCTURE_ADAPTER_KEY, $options);

        if (!$adapter) {
            throw new InvalidArgumentException(sprintf(
                'Class must be specified using the "%s" key',
                self::INFRASTRUCTURE_ADAPTER_KEY
            ));
        } elseif (!$adapter instanceof self::$_adapterInterface) {
            throw new InvalidArgumentException(sprintf(
                'Adapter must implement "%s"', self::$_adapterInterface
            ));
        }
        return $adapter;
    }
}
