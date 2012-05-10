<?php
/**
 * Zend Framework
 *
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
 * @package    Zend\Service
 * @subpackage GoGrid
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\GoGrid;

use Zend\Service\GoGrid\GoGrid as GoGridAbstract,
        Zend\Service\GoGrid\Object as GoGridObject,
        Zend\Service\GoGrid\ObjectList as GoGridObjectList;

class Job extends GoGridAbstract
{
    const API_GRID_JOB_LIST = 'grid/job/list';
    const API_GRID_JOB_GET = 'grid/job/get';
    /**
     * Get job list API
     * This call will list all the jobs in the system for a specified date range. The default is the last month.
     *
     * @param array $options
     * @return Zend\Service\GoGrid\ObjectList
     */
    public function getList($options=null) {
        $result= parent::_call(self::API_GRID_JOB_LIST, $options);
        return new GoGridObjectList($result);
    }
    /**
     * Get job API
     * This call will retrieve one or many job objects from your list of jobs
     *
     * @param string|array $job
     * @return Zend\Service\GoGrid\ObjectList
     */
    public function get($job)
    {
        if (empty($job)) {
            throw new Exception\InvalidArgumentException("The job.get API needs a id/job parameter");
        }
        $options=array();
        $options['job']= $job;
        $result= $this->_call(self::API_GRID_JOB_GET, $options);
        return new GoGridObjectList($result);
    }
}
