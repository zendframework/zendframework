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
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Markup\Renderer;
use Zend\Markup,
    Zend\Loader\PluginLoader;

/**
 * HTML renderer
 *
 * @uses       \Zend\Filter\FilterChain
 * @uses       \Zend\Filter\Callback
 * @uses       \Zend\Filter\HtmlEntities
 * @uses       \Zend\Filter\PregReplace
 * @uses       \Zend\Loader\PluginLoader\PluginLoader
 * @uses       \Zend\Markup\Renderer\RendererAbstract
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Html extends AbstractRenderer
{

    /**
     * Load the default configuration for this renderer
     *
     * @return void
     */
    protected function _loadDefaultConfig()
    {
        $this->_pluginLoader = new PluginLoader(array(
            'Zend\Markup\Renderer\Markup\Html' => 'Zend/Markup/Renderer/Markup/Html'
        ));

        $this->_groups = array(
            'block'  => array('inline', 'block'),
            'inline' => array('inline')
        );

        $this->_group  = 'block';

        $this->addMarkupByName('code', 'code');
        $this->addMarkupByName('img', 'img');
        $this->addMarkupByName('url', 'url');
    }
}
