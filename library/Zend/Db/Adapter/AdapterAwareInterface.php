<?php

namespace Zend\Db\Adapter;

interface AdapterAware
{
    public function setDbAdapter(Adapter $adapter);
}