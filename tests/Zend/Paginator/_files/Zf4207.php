<?php
require_once 'Zend/Paginator/Adapter/Interface.php';

class Zf4207 extends ArrayObject implements Zend_Paginator_Adapter_Interface
{
    public function count()
    {
        return 10;
    }

    public function getItems($pageNumber, $itemCountPerPage)
    {
        return new ArrayObject(range(1, 10));
    }
}