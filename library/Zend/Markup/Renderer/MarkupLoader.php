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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Markup\Renderer;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for markup converters.
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MarkupLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased markup converters 
     */
    protected $plugins = array(
        'code'      => 'Zend\Markup\Renderer\Markup\Html\Code',
        'img'       => 'Zend\Markup\Renderer\Markup\Html\Img',
        'list_item' => 'Zend\Markup\Renderer\Markup\Html\ListItem',
        'listitem'  => 'Zend\Markup\Renderer\Markup\Html\ListItem',
        'replace'   => 'Zend\Markup\Renderer\Markup\Html\Replace',
        'url'       => 'Zend\Markup\Renderer\Markup\Html\Url',
    );
}
