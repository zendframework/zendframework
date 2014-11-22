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
        if (isset($this->hydrationMap[$name])) {
            return $this->hydrationMap[$name];
        }

        return $name;
    }

    /**
     * {@inheritDoc}
     */
    public function extract($name)
    {
        if (isset($this->extractionMap[$name])) {
            return $this->extractionMap[$name];
        }

        return $name;
    }
}
