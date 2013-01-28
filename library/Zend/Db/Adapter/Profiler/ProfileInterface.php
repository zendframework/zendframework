<?php

namespace Zend\Db\Adapter\Profiler;

use Zend\Db\Adapter\StatementContainerInterface;

interface ProfilerInterface
{
    /**
     * @param string|StatementContainerInterface $target
     * @return mixed
     */
    public function profilerStart($target);
    public function profilerFinish();
}