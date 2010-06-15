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
use Zend\Markup;

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
class HTML extends RendererAbstract
{

    /**
     * Element groups
     *
     * @var array
     */
    protected $_groups = array(
        'block'        => array('block', 'inline', 'block-empty', 'inline-empty', 'list'),
        'inline'       => array('inline', 'inline-empty'),
        'list'         => array('list-item'),
        'list-item'    => array('inline', 'inline-empty', 'list'),
        'block-empty'  => array(),
        'inline-empty' => array(),
    );

    /**
     * The current group
     *
     * @var string
     */
    protected $_group = 'block';


    /**
     * Constructor
     *
     * @param array|\Zend\Config\Config $options
     *
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof \Zend\Config\Config) {
            $options = $options->toArray();
        }

        $this->_pluginLoader = new \Zend\Loader\PluginLoader\PluginLoader(array(
            'Zend\Markup\Renderer\Markup\HTML' => 'Zend/Markup/Renderer/Markup/HTML/'
        ));

        $this->_defineDefaultMarkups();

        parent::__construct($options);
    }

    /**
     * Define the default markups
     *
     * @return void
     */
    protected function _defineDefaultMarkups()
    {
    }
}
