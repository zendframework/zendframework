<?php

namespace Zend\Cache\Storage;

interface ExpirableInterface
{
    /**
     * Reset lifetime of an item
     *
     * @param  string $key
     * @return boolean
     * @throws \Zend\Cache\Exception\ExceptionInterface
     */
    public function touchItem($key);

    /**
     * Reset lifetime of multiple items.
     *
     * @param  array $keys
     * @return array Array of not updated keys
     * @throws \Zend\Cache\Exception\ExceptionInterface
    */
    public function touchItems(array $keys);
}
