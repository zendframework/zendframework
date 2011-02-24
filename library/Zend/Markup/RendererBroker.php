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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Markup;

use Zend\Loader\PluginBroker;

/**
 * Broker for markup renderer instances
 *
 * @category   Zend
 * @package    Zend_Markup
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RendererBroker extends PluginBroker
{
    /**
     * @var string Default plugin loading strategy
     */
    protected $defaultClassLoader = 'Zend\Markup\RendererLoader';

    /**
     * @var Parser instance to inject in renderer
     */
    protected $parser;

    /**
     * Inject parser into broker, for injecting into renderer
     * 
     * @param  Parser $parser 
     * @return RendererBroker
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * Retrieve parser instance
     * 
     * @return null|Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Register plugin with broker
     *
     * Injects parser into plugin, if registered.
     * 
     * @param  string $name 
     * @param  mixed $plugin 
     * @return RendererBroker
     */
    public function register($name, $plugin)
    {
        parent::register($name, $plugin);

        if (null !== ($parser = $this->getParser())) {
            $plugin->setParser($parser);
        }

        return $this;
    }

    /**
     * Determine if we have a valid renderer
     * 
     * @param  mixed $plugin 
     * @return true
     * @throws Exception
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof Renderer\AbstractRenderer) {
            throw new Exception('Markup renderers must extend Zend\Markup\Renderer\AbstractRenderer');
        }
        return true;
    }
}
