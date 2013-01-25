<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace ZendTest\Log\TestAsset;

use Zend\Db\Adapter\Adapter as DbAdapter;

class MockDbAdapter extends DbAdapter
{
    public $plaftorm;
    public $driver;

    public $calls = array();

    public function __call($method, $params)
    {
        $this->calls[$method][] = $params;
    }

    public function __construct()
    {
        $this->platform = new MockDbPlatform;
        $this->driver = new MockDbDriver;

    }
    public function query($sql, $parametersOrQueryMode = DbAdapter::QUERY_MODE_PREPARE)
    {
        $this->calls[__FUNCTION__][] = $sql;
        return $this;
    }
}
