<?php

namespace Zend\Cache;

interface Pattern
{

    /**
     * Constructor
     *
     * @param array|Traversable $options
     */
    public function __construct($options = array());

    /**
     * Set pattern options
     *
     * @param array|Traversable $options
     * @return Zend\Cache\Pattern
     */
    public function setOptions($options);

    /**
     * Get all pattern options
     *
     * return array
     */
    public function getOptions();

}
