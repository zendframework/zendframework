<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\QueueService;

use Zend\Cloud\AbstractFactory;

/**
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage QueueService
 */
class Factory extends AbstractFactory
{
    const QUEUE_ADAPTER_KEY = 'queue_adapter';

    /**
     * @var string Interface which adapter must implement to be considered valid
     */
    protected static $_adapterInterface = 'Zend\Cloud\QueueService\Adapter\AdapterInterface';

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
     * Retrieve QueueService adapter
     *
     * @param  array $options
     * @return void
     */
    public static function getAdapter($options = array())
    {
        $adapter = parent::_getAdapter(self::QUEUE_ADAPTER_KEY, $options);
        if (!$adapter) {
            throw new Exception\InvalidArgumentException('Class must be specified using the \'' .
            self::QUEUE_ADAPTER_KEY . '\' key');
        } elseif (!$adapter instanceof self::$_adapterInterface) {
            throw new Exception\InvalidArgumentException(
                'Adapter must implement \'' . self::$_adapterInterface . '\''
            );
        }
        return $adapter;
    }
}
