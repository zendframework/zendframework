<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace Zend\Markup\Renderer\Markup;

use Zend\Filter\FilterChain;
use Zend\Filter\FilterInterface;
use Zend\Markup\Renderer\AbstractRenderer;
use Zend\Markup\Renderer\Markup;

/**
 * Abstract markup
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Markup
 */
abstract class AbstractMarkup implements Markup\MarkupInterface
{

    /**
     * The renderer
     *
     * @var \Zend\Markup\Renderer\AbstractRenderer
     */
    protected $_renderer;

    /**
     * Markup's encoding
     *
     * @var string
     */
    protected $_encoding = 'UTF-8';

    /**
     * Chain filter
     *
     * @var \Zend\Filter\FilterChain
     */
    protected $_filter;


    /**
     * Set the encoding on this markup
     *
     * @param string $encoding
     *
     * @return \Zend\Markup\Renderer\Markup\AbstractMarkup
     */
    public function setEncoding($encoding = 'UTF-8')
    {
        $this->_encoding = $encoding;

        return $this;
    }

    /**
     * Get this markup's encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Set the renderer instance
     *
     * @param \Zend\Markup\Renderer\AbstractRenderer $renderer
     *
     * @return \Zend\Markup\Renderer\Markup\AbstractMarkup
     */
    public function setRenderer(AbstractRenderer $renderer)
    {
        $this->_renderer = $renderer;

        $this->setEncoding($renderer->getEncoding());

        return $this;
    }

    /**
     * Get the renderer instance
     *
     * @return \Zend\Markup\Renderer\AbstractRenderer
     */
    public function getRenderer()
    {
        return $this->_renderer;
    }

    /**
     * Get the filter chain
     *
     * @return \Zend\Filter\FilterChain
     */
    public function getFilterChain()
    {
        if (null === $this->_filter) {
            $this->_filter = new FilterChain();
        }

        return $this->_filter;
    }

    /**
     * Adds a filter to the chain
     *
     * @param  \Zend\Filter\FilterInterface $filter
     * @param  int $priority Priority at which to add filter; higher numbers are executed earlier. Defaults to 0
     * @return AbstractMarkup
     */
    public function addFilter(FilterInterface $filter, $priority = 0)
    {
        $this->getFilterChain()->attach($filter, $priority);

        return $this;
    }

    /**
     * Filter
     *
     * @param string $value
     *
     * @return string
     */
    public function filter($value)
    {
        // ok, __invoke() simply looks confusing in this case, better use the
        // filter() method on the Filter Chain
        return $this->getFilterChain()->filter($value);
    }
}
