<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\NamingStrategy;

use Zend\Stdlib\Hydrator\NamingStrategy\NamingStrategyInterface;

class MapNamingStrategy implements NamingStrategyInterface
{
    /**
     * Map for extract name converion.
     *
     * @var array
     */
    protected $extractMap = array();

    /**
     * Map for hydrate name converion.
     *
     * @var array
     */
    protected $hydrateMap = array();

    /**
     * Initialize.
     *
     * @param array $hydrateMap Map for extract name converion.
     * @param array $extractMap Map for hydrate name converion.
     */
    public function __construct(array $hydrateMap = null, array $extractMap = null)
    {
        if (null !== $hydrateMap) {
            $this->setHydrateMap($hydrateMap);
            if (null === $extractMap) {
                $this->setExtractMap(array_flip($hydrateMap));
            }
        }

        if (null !== $extractMap) {
            $this->setExtractMap($extractMap);
        }
    }

    /**
     * Set name map for extract coversion.
     *
     * @param array $extractMap Map for extract name converion.
     * @return \Application\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy
     */
    public function setExtractMap($extractMap)
    {
        $this->extractMap = $extractMap;

        return $this;
    }

    /**
     * Set name map for hydrate converion.
     *
     * @param array $hydrateMap Map for hydrate name converion.
     * @return \Application\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy
     */
    public function setHydrateMap($hydrateMap)
    {
        $this->hydrateMap = $hydrateMap;

        return $this;
    }


    /**
     * Converts the given name so that it can be extracted by the hydrator.
     *
     * @param string $name The original name
     * @return mixed       The hydrated name
     */
    public function hydrate($name)
    {
        if (isset($this->hydrateMap[$name])) {
            $name = $this->hydrateMap[$name];
        }

        return $name;
    }

    /**
     * Converts the given name so that it can be hydrated by the hydrator.
     *
     * @param string $name The original name
     * @return mixed       The extracted name
     */
    public function extract($name)
    {
        if (isset($this->extractMap[$name])) {
            $name = $this->extractMap[$name];
        }

        return $name;
    }
}

