<?php

namespace Zend\Db\Adapter\Driver;

interface ResultInterface extends \Countable, \Traversable
{
    public function isQueryResult();
    public function getAffectedRows();
    public function getResource();
}
