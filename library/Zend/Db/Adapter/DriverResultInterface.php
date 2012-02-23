<?php

namespace Zend\Db\Adapter;

interface DriverResultInterface extends \Countable, \Traversable
{
    public function isQueryResult();
    public function getAffectedRows();
    public function getResource();
}
