<?php

namespace Zend\Db\Adapter\Profiler;

interface ProfilerInterface
{
    /**
     * @param string|\Zend\Db\Adapter\StatementContainerInterface $target
     * @return mixed
     */
    public function profilerStart($target);
    public function profilerFinish();
}
