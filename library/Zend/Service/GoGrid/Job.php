<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\GoGrid;

class Job extends AbstractGoGrid
{
    const API_GRID_JOB_LIST = 'grid/job/list';
    const API_GRID_JOB_GET = 'grid/job/get';

    /**
     * Get job list API
     * This call will list all the jobs in the system for a specified date range. The default is the last month.
     *
     * @param array $options
     * @return ObjectList
     */
    public function getList($options=null)
    {
        $result= parent::_call(self::API_GRID_JOB_LIST, $options);
        return new ObjectList($result);
    }
    /**
     * Get job API
     * This call will retrieve one or many job objects from your list of jobs
     *
     * @param string|array $job
     * @return ObjectList
     * @throws Exception\InvalidArgumentException
     */
    public function get($job)
    {
        if (empty($job)) {
            throw new Exception\InvalidArgumentException("The job.get API needs a id/job parameter");
        }
        $options=array();
        $options['job']= $job;
        $result= $this->_call(self::API_GRID_JOB_GET, $options);
        return new ObjectList($result);
    }
}
