<?php

namespace Zend\Db\Adapter\Driver;

interface ResultInterface extends \Countable, \Iterator
{
    public function isQueryResult();
    public function getAffectedRows();
    public function getResource();
}
