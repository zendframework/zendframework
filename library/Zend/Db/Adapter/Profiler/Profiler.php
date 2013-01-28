<?php

namespace Zend\Db\Adapter\Profiler;

use Zend\Db\Adapter\StatementContainerInterface;
use Zend\Db\Adapter\Exception;

class Profiler implements ProfilerInterface
{
    /**
     * @var array
     */
    protected $profiles = array();

    /**
     * @var null
     */
    protected $current = null;

    /**
     * @param string|StatementContainerInterface $target
     * @throws \Zend\Db\Adapter\Exception\InvalidArgumentException
     * @return Profiler
     */
    public function profilerStart($target)
    {
        $profileInformation = array(
            'sql' => '',
            'parameters' => null,
            'start' => microtime(true),
            'end' => null,
            'elapse' => null
        );
        if ($target instanceof StatementContainerInterface) {
            $profileInformation['sql'] = $target->getSql();
            $profileInformation['params'] = clone $target->getParameterContainer();
        } elseif (is_string($target)) {
            $profileInformation['sql'] = $target;
        } else {
            throw new Exception\InvalidArgumentException(__FUNCTION__ . ' takes either a StatementContainer or a string');
        }

        $this->profiles[] = $this->current = $profileInformation;
        return $this;
    }

    /**
     * @return Profiler
     */
    public function profilerFinish()
    {
        $this->current['end'] = microtime(true);
        $this->current['elapse'] = $this->current['end'] - $this->current['start'];
        return $this;
    }

    /**
     * @return array|null
     */
    public function getLastProfile()
    {
        return end($this->profiles);
    }

    /**
     * @return array
     */
    public function getProfiles()
    {
        return $this->profiles;
    }
}
