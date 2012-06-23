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
 * @package    Zend_Code
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Code\Annotation;

use Zend\Code\Exception;

/**
 * @category   Zend
 * @package    Zend_Code
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AnnotationManager
{
    /**
     * @var array
     */
    protected $aliases = array();

    /**
     * @var string[]
     */
    protected $annotationNames = array();

    /**
     * @var AnnotationInterface[]
     */
    protected $annotations = array();

    /**
     * Constructor
     *
     * @param array $annotations
     */
    public function __construct(array $annotations = array())
    {
        if ($annotations) {
            foreach ($annotations as $annotation) {
                $this->registerAnnotation($annotation);
            }
        }
    }

    /**
     * Register annotations
     *
     * @param  AnnotationInterface $annotation
     * @throws Exception\InvalidArgumentException
     */
    public function registerAnnotation(AnnotationInterface $annotation)
    {
        $class = get_class($annotation);

        if (in_array($class, $this->annotationNames)) {
            throw new Exception\InvalidArgumentException('An annotation for this class ' . $class . ' already exists');
        }

        $this->annotations[]     = $annotation;
        $this->annotationNames[] = $class;
    }

    /**
     * Alias an annotation name
     * 
     * @param  string $alias 
     * @param  string $class May be either a registered annotation name or another alias
     * @return AnnotationManager
     */
    public function setAlias($alias, $class)
    {
        if (!in_array($class, $this->annotationNames) && !$this->hasAlias($class)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: Cannot alias "%s" to "%s", as class "%s" is not currently a registered annotation or alias',
                __METHOD__,
                $alias,
                $class,
                $class
            ));
        }

        $alias = $this->normalizeAlias($alias);
        $this->aliases[$alias] = $class;
        return $this;
    }

    /**
     * Checks if the manager has annotations for a class
     *
     * @param $class
     * @return bool
     */
    public function hasAnnotation($class)
    {
        if (in_array($class, $this->annotationNames)) {
            return true;
        }

        if ($this->hasAlias($class)) {
            return true;
        }

        return false;
    }

    /**
     * Create Annotation
     *
     * @param  string $class
     * @param  null|string $content
     * @throws Exception\RuntimeException
     * @return AnnotationInterface
     */
    public function createAnnotation($class, $content = null)
    {
        if (!$this->hasAnnotation($class)) {
            throw new Exception\RuntimeException('This annotation class is not supported by this annotation manager');
        }

        if ($this->hasAlias($class)) {
            $class = $this->resolveAlias($class);
        }

        $index      = array_search($class, $this->annotationNames);
        $annotation = $this->annotations[$index];

        $newAnnotation = clone $annotation;
        if ($content) {
            $newAnnotation->initialize($content);
        }
        return $newAnnotation;
    }

    /**
     * Normalize an alias name
     * 
     * @param  string $alias 
     * @return string
     */
    protected function normalizeAlias($alias)
    {
        return strtolower(str_replace(array('-', '_', ' ', '\\', '/'), '', $alias));
    }

    /**
     * Do we have an alias by the provided name?
     * 
     * @param  string $alias 
     * @return bool
     */
    protected function hasAlias($alias)
    {
        $alias = $this->normalizeAlias($alias);
        return (isset($this->aliases[$alias]));
    }

    /**
     * Resolve an alias to a class name
     * 
     * @param  string $alias 
     * @return string
     */
    protected function resolveAlias($alias)
    {
        do {
            $normalized = $this->normalizeAlias($alias);
            $class      = $this->aliases[$normalized];
        } while ($this->hasAlias($class));
        return $class;
    }
}
