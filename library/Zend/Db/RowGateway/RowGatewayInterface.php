<?php

namespace Zend\Db\RowGateway;

interface RowGatewayInterface
{
    public function save();
    public function delete();
}