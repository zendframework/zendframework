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

use Zend\Markup\Token,
    Zend\Loader\Broker,
    Zend\Markup\Renderer\Markup\Html as Markup;

/**
 * HTML renderer
 *
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
        $this->_groups = array(
            'block'  => array('inline', 'block'),
            'inline' => array('inline')
        );

        $this->_group  = 'block';

        $this->addMarkup('code', new Markup\Code());
        $this->addMarkup('img', new Markup\Img());
        $this->addMarkup('url', new Markup\Url());
        $this->addMarkup('b', new Markup\Replace('strong'));
    }
}
