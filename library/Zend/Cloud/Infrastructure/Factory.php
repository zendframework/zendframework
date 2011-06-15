<?php
/**
 * Factory
 *
 * @category   Zend
 * @package    Zend\Cloud
 * @subpackage Infrastructure
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * namespace
 */
namespace Zend\Cloud\Infrastructure;

use Zend\Cloud\AbstractFactory,
    Zend\Cloud\Exception\InvalidArgumentException;

class Factory extends AbstractFactory
{
    const INFRASTRUCTURE_ADAPTER_KEY = 'infrastructure_adapter';

    /**
     * @var string Interface which adapter must implement to be considered valid
     */
    protected static $_adapterInterface = 'Zend\Cloud\Infrastructure\Adapter';

    /**
     * Constructor
     *
     * @return void
     */
    private function __construct()
    {
        // private ctor - should not be used
    }

    /**
     * Retrieve an adapter instance
     *
     * @param array $options
     * @return void
     */
    public static function getAdapter($options = array())
    {
        $adapter = parent::_getAdapter(self::INFRASTRUCTURE_ADAPTER_KEY, $options);
        if (!$adapter) {
            throw new InvalidArgumentException(
                'Class must be specified using the \''
                . self::INFRASTRUCTURE_ADAPTER_KEY . '\' key'
            );
        } elseif (!$adapter instanceof self::$_adapterInterface) {
            throw new InvalidArgumentException(
                'Adapter must implement \'' . self::$_adapterInterface . '\''
            );
        }
        return $adapter;
    }
}
