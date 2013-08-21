<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Config;

use Zend\ServiceManager;
use Zend\Stdlib\ArrayUtils;

/**
 * Class AbstractConfigFactory
 */
class AbstractConfigFactory implements ServiceManager\AbstractFactoryInterface
{
    /**
     * @var string[]
     */
    protected $defaultPatterns = array(
        '#config[\._-](.*)$#i',
        '#^(.*)[\\\\\._-]config$#i'
    );

    /**
     * @var string[]
     */
    protected $patterns;

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (!$serviceLocator->has('Config')) {
            return false;
        }

        $key = $this->match($requestedName);
        if (null === $key) {
            return false;
        }

        $config = $serviceLocator->get('Config');
        return isset($config[$key]);
    }

    /**
     * Create service with name
     *
     * @param ServiceManager\ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $key = $this->match($requestedName);
        $config = $serviceLocator->get('Config');
        return new Config($config[$key]);
    }

    /**
     * @param string $pattern
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addPattern($pattern)
    {
        if (!is_string($pattern)) {
            throw new \InvalidArgumentException('pattern must be string');
        }

        $patterns = $this->getPatterns();
        array_unshift($patterns, $pattern);
        $this->setPatterns($patterns);
        return $this;
    }

    /**
     * @param array|\Traversable $patterns
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addPatterns($patterns)
    {
        if ($patterns instanceof \Traversable) {
            $patterns = ArrayUtils::iteratorToArray($patterns);
        }

        if (!is_array($patterns)) {
            throw new \InvalidArgumentException("patterns must be array or Traversable");
        }

        foreach ($patterns as $pattern) {
            $this->addPattern($pattern);
        }

        return $this;
    }

    /**
     * @param array|\Traversable $patterns
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setPatterns($patterns)
    {
        if ($patterns instanceof \Traversable) {
            $patterns = ArrayUtils::iteratorToArray($patterns);
        }

        if (!is_array($patterns)) {
            throw new \InvalidArgumentException("patterns must be array or Traversable");
        }

        $this->patterns = $patterns;
        return $this;
    }

    /**
     * @return array
     */
    public function getPatterns()
    {
        if (null === $this->patterns) {
            $this->setPatterns($this->defaultPatterns);
        }
        return $this->patterns;
    }

    /**
     * @param string $requestedName
     * @return null|string
     */
    protected function match($requestedName)
    {
        foreach ($this->getPatterns() as $pattern) {
            if (preg_match($pattern, $requestedName, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}