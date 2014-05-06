<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Resolver;

use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Resolver\ResolverInterface as Resolver;

/**
 * Resolve addition
 * Allow refering to view script
 */
class RelativeFallbackResolver implements ResolverInterface
{
    const NS_SEPARATOR = '/';

    /**
     * @var Resolve
     */
    protected $resolver;

    /**
     * Constructor
     *
     * Set wrapped resolver
     *
     */
    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Resolve a template/pattern name to a resource the renderer can consume
     *
     * @param  string $name
     * @param  null|Renderer $renderer
     * @return false|string
     */
    public function resolve($name, Renderer $renderer = null)
    {
        // It may sense only in context of view
        if ($renderer === null) {
            return $this->resolver->resolve($name);
        }

        $resource = $this->resolver->resolve($name, $renderer);
        if ($resource) {
            return $resource;
        }

        // Try to get it from the same name space (folder)
        $helper = $renderer->plugin('view_model');
        $currentTemplate = $helper->getCurrent()->getTemplate();
        $position = strrpos($currentTemplate, self::NS_SEPARATOR);
        if ($position > 0) {
            $absoluteName = substr($currentTemplate, 0, $position) . self::NS_SEPARATOR . $name;
            $resource = $this->resolver->resolve($absoluteName, $renderer);
        }

        return $resource;
    }
}
