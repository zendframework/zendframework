<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Package
 */

namespace Zend\Db\Adapter\Driver\Feature;

use Zend\Db\Adapter\Driver\DriverInterface;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
abstract class AbstractFeature
{

    /**
     * @var DriverInterface
     */
    protected $driver = null;

    /**
     * @param DriverInterface $pdoDriver
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    abstract public function getName();

}
