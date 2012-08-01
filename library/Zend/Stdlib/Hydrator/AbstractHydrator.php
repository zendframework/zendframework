<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace Zend\Stdlib\Hydrator;

use Zend\Stdlib\Exception;
use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage Hydrator
 */
abstract class AbstractHydrator implements HydratorInterface
{
    protected $strategies;
    
    public function __construct()
    {
        $this->strategies = array();
    }
    
    public function getStrategy($name)
    {
        return $this->strategies[$name];
    }
    
    public function hasStrategy($name)
    {
        return array_key_exists($name, $this->strategies);
    }
    
    public function registerStrategy($name, StrategyInterface $strategy)
    {
        $this->strategies[$name] = $strategy;
    }
    
    public function extractValue($name, $value)
    {
        if ($this->hasStrategy($name)) {
            $strategy = $this->getStrategy($name);
            $value = $strategy->extract($value);
        }
        return $value;
    }
    
    public function hydrateValue($name, $value)
    {
        if ($this->hasStrategy($name)) {
            $strategy = $this->getStrategy($name);
            $value = $strategy->hydrate($value);
        }
        return $value;
    }
}
