<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\NamingStrategy;

class ArrayMapNamingStrategy implements NamingStrategyInterface
{
    /**
     * @var string[]
     */
    protected $extractionMap = array();

    /**
     * @var string[]
     */
    protected $hydrationMap = array();

    /**
     * Constructor
     *
     * @param array $extractionMap
     */
    public function __construct(array $extractionMap)
    {
        $this->extractionMap = $extractionMap;
        $this->hydrationMap  = array_flip($extractionMap);
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate($name)
    {
        return isset($this->hydrationMap[$name]) ? $this->hydrationMap[$name] : $name;
    }

    /**
     * {@inheritDoc}
     */
    public function extract($name)
    {
        return isset($this->extractionMap[$name]) ? $this->extractionMap[$name] : $name;
    }
}
