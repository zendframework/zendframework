<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ServiceManager
 */

namespace ZendTest\ServiceManager;

use Zend\ServiceManager\ServiceManager;

use ZendTest\ServiceManager\TestAsset\FooCounterAbstractFactory;

class ServiceManagerPerformanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @var int
     */
    protected $iterations = 100000;

    /**
     * @var float
     */
    protected $lastSnapshotTime;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->serviceManager = new ServiceManager();
    }

    /**
     * @bufferedOutput
     */
    public function testGetPerformance()
    {
        $this->serviceManager->setService('foo', new \stdClass());
        $this->startProfiling();

        for ($i = 0; $i < $this->iterations; $i += 1) {
            $this->serviceManager->get('foo');
        }

        var_dump(__METHOD__, $this->stopProfiling());
    }

    /**
     * @bufferedOutput
     */
    public function testHasPerformance()
    {
        $this->serviceManager->setService('foo', new \stdClass());
        $this->startProfiling();

        for ($i = 0; $i < $this->iterations; $i += 1) {
            $this->serviceManager->has('foo');
        }

        var_dump(__METHOD__, $this->stopProfiling());
    }

    /**
     * @bufferedOutput
     */
    public function testHasPerformanceWithNoService()
    {
        $this->startProfiling();

        for ($i = 0; $i < $this->iterations; $i += 1) {
            $this->serviceManager->has('foo');
        }

        var_dump(__METHOD__, $this->stopProfiling());
    }

    /**
     * @bufferedOutput
     */
    public function testCreatePerformance()
    {
        $this->serviceManager->setInvokableClass('foo', 'stdClass');
        $this->startProfiling();

        for ($i = 0; $i < $this->iterations; $i += 1) {
            $this->serviceManager->create('foo');
        }

        var_dump(__METHOD__, $this->stopProfiling());
    }

    /**
     * Start profiling execution
     */
    private function startProfiling()
    {
        $this->lastSnapshotTime = microtime(true);
    }

    /**
     * Get current profiler snapshot and stop profiling
     *
     * @return array
     */
    private function stopProfiling()
    {
        return array(
            'time'       => microtime(true) - $this->lastSnapshotTime,
            'iterations' => $this->iterations,
        );
    }
}
