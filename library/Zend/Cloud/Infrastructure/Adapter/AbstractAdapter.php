<?php
/**
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
 * @subpackage DocumentService
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * namespace
 */
namespace Zend\Cloud\Infrastructure\Adapter;

use Zend\Cloud\Infrastructure\Adapter,
    Zend\Cloud\Infrastructure\Instance;

/**
 * Abstract infrastructure service adapter
 *
 * @category   Zend
 * @package    Zend\Cloud
 * @subpackage Infrastructure
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAdapter implements Adapter
{
    /**
     * Store the last response from the adpter
     * 
     * @var array
     */
    protected $adapterResult;

    /**
     * Valid metrics for monitor
     * 
     * @var array
     */
    protected $validMetrics = array(
        Instance::MONITOR_CPU,
        Instance::MONITOR_DISK_READ,
        Instance::MONITOR_DISK_WRITE,
        Instance::MONITOR_NETWORK_IN,
        Instance::MONITOR_NETWORK_OUT,
    );

    /**
     * Get the last result of the adapter
     *
     * @return array
     */
    public function getAdapterResult()
    {
        return $this->adapterResult;
    }

    /**
     * Wait for status $status with a timeout of $timeout seconds
     * 
     * @param  string $id
     * @param  string $status
     * @param  integer $timeout 
     * @return boolean
     */
    public function waitStatusInstance($id, $status, $timeout = self::TIMEOUT_STATUS_CHANGE)
    {
        if (empty($id) || empty($status)) {
            return false;
        }

        $num = 0;
        while (($num<$timeout) && ($this->statusInstance($id) != $status)) {
            sleep(self::TIME_STEP_STATUS_CHANGE);
            $num += self::TIME_STEP_STATUS_CHANGE;
        }
        return ($num < $timeout);
    }
}
