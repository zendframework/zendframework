<?php

namespace Zend\Db\Adapter;

interface ParameterContainerInterface extends \ArrayAccess, \Countable, \Traversable
{
    const TYPE_AUTO = 'auto';
    const TYPE_NULL = 'null';
    const TYPE_DOUBLE = 'double';
    const TYPE_INTEGER = 'integer';
    const TYPE_STRING = 'string';
    const TYPE_LOB = 'lob';

    public function setFromArray(Array $data);
    
    public function offsetSetErrata($nameOrPosition, $errata);
    public function offsetGetErrata($nameOrPosition);
    public function offsetHasErrata($nameOrPosition);
    public function offsetUnsetErrata($nameOrPosition);
    public function getErrataIterator();

    public function toArray();
}
