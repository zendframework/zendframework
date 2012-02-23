<?php

namespace Zend\Db\ResultSet;

interface ResultSetInterface extends \Countable, \Traversable
{
    public function getFieldCount();
    
}
