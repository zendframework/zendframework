<?php

namespace Zend\Cache\Storage;

use Zend\EventManager\ListenerAggregate;

interface Plugin extends ListenerAggregate
{

    /**
     * Constructor
     *
     * @param array|Traversable $options
     */
    public function __construct($options = array());

    /**
     * Set options
     *
     * @param array|Traversable $options
     * @return Plugin
     */
    public function setOptions($options);

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions();

}
