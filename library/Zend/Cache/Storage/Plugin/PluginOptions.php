<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache\Storage\Plugin;

use Zend\Cache\Exception,
    Zend\Serializer\Adapter\AdapterInterface as SerializerAdapter,
    Zend\Serializer\Serializer as SerializerFactory,
    Zend\Stdlib\AbstractOptions;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PluginOptions extends AbstractOptions
{
    /**
     * Used by:
     * - ClearByFactor
     * @var int
     */
    protected $clearingFactor = 0;

    /**
     * Used by:
     * - ExceptionHandler
     * @var callable
     */
    protected $exceptionCallback;

    /**
     * Used by:
     * - IgnoreUserAbort
     * @var boolean
     */
    protected $exitOnAbort = true;

    /**
     * Used by:
     * - OptimizeByFactor
     * @var int
     */
    protected $optimizingFactor = 0;

    /**
     * Used by:
     * - Serializer
     * @var string|SerializerAdapter
     */
    protected $serializer;

    /**
     * Used by:
     * - Serializer
     * @var array
     */
    protected $serializerOptions = array();

    /**
     * Used by:
     * - ExceptionHandler
     * @var bool
     */
    protected $throwExceptions = true;

    /**
     * Set automatic clearing factor
     *
     * Used by:
     * - ClearExpiredByFactor
     *
     * @param  int $clearingFactor
     * @return PluginOptions
     */
    public function setClearingFactor($clearingFactor)
    {
        $this->clearingFactor = $this->normalizeFactor($clearingFactor);
        return $this;
    }

    /**
     * Get automatic clearing factor
     *
     * Used by:
     * - ClearExpiredByFactor
     *
     * @return int
     */
    public function getClearingFactor()
    {
        return $this->clearingFactor;
    }

    /**
     * Set callback to call on intercepted exception
     *
     * Used by:
     * - ExceptionHandler
     *
     * @param  callable EexceptionCallback
     * @return PluginOptions
     */
    public function setExceptionCallback($exceptionCallback)
    {
        if ($exceptionCallback !== null && !is_callable($exceptionCallback, true)) {
            throw new Exception\InvalidArgumentException('Not a valid callback');
        }
        $this->exceptionCallback = $exceptionCallback;
        return $this;
    }

    /**
     * Get callback to call on intercepted exception
     *
     * Used by:
     * - ExceptionHandler
     *
     * @return null|callable
     */
    public function getExceptionCallback()
    {
        return $this->exceptionCallback;
    }

    /**
     * Exit if connection aborted and ignore_user_abort is disabled.
     *
     * @param boolean $exitOnAbort
     * @return PluginOptions
     */
    public function setExitOnAbort($exitOnAbort)
    {
        $this->exitOnAbort = (bool) $exitOnAbort;
        return $this;
    }

    /**
     * Exit if connection aborted and ignore_user_abort is disabled.
     *
     * @return boolean
     */
    public function getExitOnAbort()
    {
        return $this->exitOnAbort;
    }

    /**
     * Set automatic optimizing factor
     *
     * Used by:
     * - OptimizeByFactor
     *
     * @param  int $optimizingFactor
     * @return PluginOptions
     */
    public function setOptimizingFactor($optimizingFactor)
    {
        $this->optimizingFactor = $this->normalizeFactor($optimizingFactor);
        return $this;
    }

    /**
     * Set automatic optimizing factor
     *
     * Used by:
     * - OptimizeByFactor
     *
     * @return int
     */
    public function getOptimizingFactor()
    {
        return $this->optimizingFactor;
    }

    /**
     * Set serializer
     *
     * Used by:
     * - Serializer
     *
     * @param  string|SerializerAdapter $serializer
     * @return Serializer
     */
    public function setSerializer($serializer)
    {
        if (!is_string($serializer) && !$serializer instanceof SerializerAdapter) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects either a string serializer name or Zend\Serializer\Adapter\AdapterInterface instance; '
                . 'received "%s"',
                __METHOD__,
                (is_object($serializer) ? get_class($serializer) : gettype($serializer))
            ));
        }
        $this->serializer = $serializer;
        return $this;
    }

    /**
     * Get serializer
     *
     * Used by:
     * - Serializer
     *
     * @return SerializerAdapter
     */
    public function getSerializer()
    {
        if (is_string($this->serializer)) {
            $options = $this->getSerializerOptions();
            $this->setSerializer(SerializerFactory::factory($this->serializer, $options));
        } elseif (null === $this->serializer) {
            $this->setSerializer(SerializerFactory::getDefaultAdapter());
        }

        return $this->serializer;
    }

    /**
     * Set configuration options for instantiating a serializer adapter
     *
     * Used by:
     * - Serializer
     *
     * @param  array $serializerOptions
     * @return PluginOptions
     */
    public function setSerializerOptions(array $serializerOptions)
    {
        $this->serializerOptions = $serializerOptions;
        return $this;
    }

    /**
     * Get configuration options for instantiating a serializer adapter
     *
     * Used by:
     * - Serializer
     *
     * @return array
     */
    public function getSerializerOptions()
    {
        return $this->serializerOptions;
    }

    /**
     * Set flag indicating we should re-throw exceptions
     *
     * Used by:
     * - ExceptionHandler
     *
     * @param  bool $throwExceptions
     * @return PluginOptions
     */
    public function setThrowExceptions($throwExceptions)
    {
        $this->throwExceptions = (bool) $throwExceptions;
        return $this;
    }

    /**
     * Should we re-throw exceptions?
     *
     * Used by:
     * - ExceptionHandler
     *
     * @return bool
     */
    public function getThrowExceptions()
    {
        return $this->throwExceptions;
    }

    /**
     * Normalize a factor
     *
     * Cast to int and ensure we have a value greater than zero.
     *
     * @param  int $factor
     * @return int
     * @throws Exception\InvalidArgumentException
     */
    protected function normalizeFactor($factor)
    {
        $factor = (int) $factor;
        if ($factor < 0) {
            throw new Exception\InvalidArgumentException(
                "Invalid factor '{$factor}': must be greater or equal 0"
            );
        }
        return $factor;
    }
}
