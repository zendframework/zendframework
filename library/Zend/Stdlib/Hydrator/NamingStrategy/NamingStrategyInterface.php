<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\NamingStrategy;

/**
 * Allow property extraction / hydration strategy
 *
 * Interface PropertyStrategyInterface
 * @package Zend\Stdlib\Hydrator\NamingStrategy
 */
interface NamingStrategyInterface
{
    /**
     * @param $name
     *
     * @return mixed
     */
    public function hydrate($name);

    /**
     * @param $name
     *
     * @return mixed
     */
    public function extract($name);
} 