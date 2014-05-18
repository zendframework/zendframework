<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Resolver;

use Zend\View\Renderer\RendererInterface;

/**
 * Resolve addition
 * Allow refering to view script
 */
class RelativeFallbackResolver implements ResolverInterface
{
    const NS_SEPARATOR = '/';

    /**
     * @var RendererInterface
     */
    protected $resolver;

    /**
     * Constructor
     *
     * Set wrapped resolver
     *
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Resolve a template/pattern name to a resource the renderer can consume
     *
     * @param  string                 $name
     * @param  null|RendererInterface $renderer
     *
     * @return false|string
     */
    public function resolve($name, RendererInterface $renderer = null)
    {
        // There should exists view model to get template name
        if (! is_callable(array($renderer, 'plugin'))) {
            return false;
        }

        // Try to get it from the same name space (folder)
        $helper          = $renderer->plugin('view_model');
        $currentTemplate = $helper->getCurrent()->getTemplate();
        $position        = strrpos($currentTemplate, self::NS_SEPARATOR);

        if ($position > 0) {
            $absoluteName = substr($currentTemplate, 0, $position) . self::NS_SEPARATOR . $name;

            return $this->resolver->resolve($absoluteName, $renderer);
        }

        return false;
    }
}
