<?php

namespace Zend\Db\ResultSet;

use ArrayAccess,
    Countable;

interface RowObjectInterface extends Countable, ArrayAccess
{
    public function exchangeArray($input);
}
