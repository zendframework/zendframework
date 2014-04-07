<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\NamingStrategy;

use Zend\Stdlib\Exception\InvalidArgumentException;

class MapNamingStrategy implements NamingStrategyInterface
{
    const MAP_HYDRATE = 'hydrate';
    const MAP_EXTRACT = 'extract';
    const MAP_BOTH    = 'both';

    /**
     * Map for extract name conversion.
     *
     * @var array
     */
    protected $extractMap = array();

    /**
     * Map for hydrate name conversion.
     *
     * @var array
     */
    protected $hydrateMap = array();

    /**
     * Initialize.
     *
     * @param array $hydrateMap
     * @param array $extractMap
     */
    public function __construct(array $hydrateMap, array $extractMap = null)
    {
        $type = (null === $extractMap) ? self::MAP_BOTH : self::MAP_HYDRATE;
        foreach ($hydrateMap as $original => $resolved) {
            $this->setMapping($original, $resolved, $type);
        }

        if (null !== $extractMap) {
            foreach ($extractMap as $original => $resolved) {
                $this->setMapping($original, $resolved, self::MAP_EXTRACT);
            }
        }
    }

    /**
     * Set mapping.
     *
     * @param  string                   $original Original name.
     * @param  string                   $resolved Resolved name.
     * @param  string                   $map      Type of map.
     * @throws InvalidArgumentException
     */
    protected function setMapping($original, $resolved, $map = self::MAP_BOTH)
    {
        if (!is_string($original) || !is_string($resolved)) {
            throw new InvalidArgumentException('Map should contain only strings');
        }

        switch ($map) {
            case self::MAP_HYDRATE:
                    $this->hydrateMap[$original] = $resolved;
                break;

            case self::MAP_EXTRACT:
                    $this->extractMap[$original] = $resolved;
                break;

            case self::MAP_BOTH:
                    $this->extractMap[$resolved] = $original;
                    $this->hydrateMap[$original] = $resolved;
                break;

            default:
                throw new InvalidArgumentException("Unknown map type {$map}!");
        }
    }

    /**
     * Converts the given name so that it can be extracted by the hydrator.
     *
     * @param  string $name The original name
     * @return mixed  The hydrated name
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
     * @param  string $name The original name
     * @return mixed  The extracted name
     */
    public function extract($name)
    {
        if (isset($this->extractMap[$name])) {
            $name = $this->extractMap[$name];
        }

        return $name;
    }
}
