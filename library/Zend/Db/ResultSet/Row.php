<?php

namespace Zend\Db\ResultSet;

class Row extends \ArrayObject implements RowObjectInterface
{
    public function __construct()
    {
        parent::__construct(array(), \ArrayObject::ARRAY_AS_PROPS);
    }
}
