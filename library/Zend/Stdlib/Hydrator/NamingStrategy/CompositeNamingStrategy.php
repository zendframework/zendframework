<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\NamingStrategy;

use Zend\Stdlib\Exception;

final class CompositeNamingStrategy implements NamingStrategyInterface
{
    /**
     * @var array
     */
    private $namingStrategies = array();

    /**
     * @var NamingStrategyInterface|null
     */
    private $defaultNamingStrategy;

    /**
     * @param NamingStrategyInterface[]    $strategies            indexed by the name they translate
     * @param NamingStrategyInterface|null $defaultNamingStrategy
     */
    public function __construct(array $strategies, NamingStrategyInterface $defaultNamingStrategy = null)
    {
        $this->namingStrategies = array_map(
            function (NamingStrategyInterface $strategy) {
                // this callback is here only to ensure type-safety
                return $strategy;
            },
            $strategies
        );

        $this->defaultNamingStrategy = $defaultNamingStrategy;
    }

    /**
     * Checks if the naming strategy with the given name exists.
     *
     * @param  string $name The name of the naming strategy to check for.
     * @return bool
     */
    private function has($name)
    {
        return array_key_exists($name, $this->namingStrategies)
            || ($this->defaultNamingStrategy instanceof NamingStrategyInterface);
    }

    /**
     * Gets the naming strategy with the given name.
     *
     * @param string $name The name of the naming strategy to get.
     *
     * @return NamingStrategyInterface
     */
    private function get($name)
    {
        if (array_key_exists($name, $this->namingStrategies)) {
            return $this->namingStrategies[$name];
        }

        return $this->defaultNamingStrategy;
    }

    /**
     * {@inheritDoc}
     */
    public function extract($name)
    {
        if (!$this->has($name)) {
            return $name;
        }

        return $this->get($name)->extract($name);
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate($name)
    {
        if (!$this->has($name)) {
            return $name;
        }

        return $this->get($name)->hydrate($name);
    }
}
