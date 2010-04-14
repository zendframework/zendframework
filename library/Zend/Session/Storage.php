<?php

namespace Zend\Session;

interface Storage extends \Traversable, \ArrayAccess, \Serializable, \Countable
{
    public function lock($key = null);
    public function isLocked($key = null);
    public function unlock($key = null);
    public function markImmutable();
    public function isImmutable();

    public function setMetadata($key, $value, $overwriteArray = false);
    public function getMetadata($key = null);

    public function clear($key = null);

    public function toArray();
    public function fromArray(array $array);
}
