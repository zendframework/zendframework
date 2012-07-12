<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\StorageService;

use Zend\Cloud\AbstractFactory;

/**
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage StorageService
 */
class Factory extends AbstractFactory
{
    const STORAGE_ADAPTER_KEY = 'storage_adapter';

    /**
     * @var string Interface which adapter must implement to be considered valid
     */
    protected static $_adapterInterface = 'Zend\Cloud\StorageService\Adapter\AdapterInterface';
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
     * Retrieve StorageService adapter
     *
     * @param  array $options
     * @return void
     */
    public static function getAdapter($options = array())
    {
        $adapter = parent::_getAdapter(self::STORAGE_ADAPTER_KEY, $options);
        if (!$adapter) {
            throw new Exception\InvalidArgumentException('Class must be specified using the \'' .
            self::STORAGE_ADAPTER_KEY . '\' key');
        } elseif (!$adapter instanceof self::$_adapterInterface) {
            throw new Exception\InvalidArgumentException(
                'Adapter must implement \'' . self::$_adapterInterface . '\''
            );
        }
        return $adapter;
    }
}
