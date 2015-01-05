<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Stdlib\Hydrator\Strategy;

use Traversable;
use Zend\Stdlib\PriorityQueue;

final class StrategyChain implements StrategyInterface
{
    /**
     * Default priority at which strategies are added
     */
    const DEFAULT_PRIORITY = 1;

    /**
     * Strategy chain
     *
     * @var PriorityQueue
     */
    private $strategies;

    /**
     * Initialize Strategy chain
     *
     * @param array|Traversable $strategies
     */
    public function __construct($strategies)
    {
        $this->strategies = new PriorityQueue();
        $this->setStrategies($strategies);
    }

    /**
     * Sets strategies
     *
     * @param  array|Traversable                  $strategies
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    private function setStrategies($strategies)
    {
        if (!is_array($strategies) && !$strategies instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected array or Traversable; received "%s"',
                (is_object($strategies) ? get_class($strategies) : gettype($strategies))
            ));
        }

        foreach ($strategies as $value) {
            if (is_array($value)) {
                if (!isset($value['strategy'])) {
                    throw new Exception\DomainException('No strategy is provided.');
                }
                $strategy = $value['strategy'];
                $priority = isset($value['priority']) ? $value['priority'] : self::DEFAULT_PRIORITY;
            } else {
                $strategy = $value;
                $priority = self::DEFAULT_PRIORITY;
            }

            if (!$strategy instanceof StrategyInterface) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Strategy must implement Zend\Stdlib\Hydrator\Strategy\StrategyInterface, "%s provided instead"',
                    (is_object($strategy) ? get_class($strategy) : gettype($strategy))
                ));
            }

            $this->strategies->insert($strategy, $priority);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function extract($value)
    {
        foreach ($this->strategies as $strategy) {
            $value = $strategy->extract($value);
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate($value)
    {
        foreach (array_reverse(iterator_to_array($this->strategies)) as $strategy) {
            $value = $strategy->hydrate($value);
        }

        return $value;
    }
}
