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
 * @package    Zend_Markup
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Markup;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigurationInterface;

/**
 * Plugin manager implementation for renderer adapters
 *
 * Enforces that renderers retrieved are instances of
 * Renderer\AbstractRenderer. Additionally, it registers a number of default 
 * renderers available.
 *
 * @category   Zend
 * @package    Zend_Markup
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RendererPluginManager extends AbstractPluginManager
{
    /**
     * Default set of renderers
     * 
     * @var array
     */
    protected $invokableClasses = array(
        'html'  => 'Zend\Markup\Renderer\Html',
    );

    /**
     * @var Parser\ParserInterface instance to inject in renderer
     */
    protected $parser;

    /**
     * Constructor
     *
     * After processing parent constructor, adds an initializer (injectParser()).
     * 
     * @param  null|ConfigurationInterface $configuration 
     * @return void
     */
    public function __construct(ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->addInitializer(array($this, 'injectParser'));
    }

    /**
     * Inject parser into broker, for injecting into renderer
     * 
     * @param  Parser\ParserInterface $parser
     * @return RendererBroker
     */
    public function setParser(Parser\ParserInterface $parser)
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * Retrieve parser instance
     * 
     * @return null|Parser\ParserInterface
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Inject the composed parser into the retrieved renderer instance
     * 
     * @param  Renderer\AbstractRenderer $instance 
     * @return void
     */
    public function injectParser($instance)
    {
        $parser = $this->getParser();
        if ($parser === null) {
            return;
        }
        $instance->setParser($parser);
    }

    /**
     * Validate the plugin
     *
     * Checks that the parser loaded is an instance of Renderer\AbstractRenderer.
     * 
     * @param  mixed $plugin 
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Renderer\AbstractRenderer) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must extend %s\Renderer\AbstractRenderer',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
