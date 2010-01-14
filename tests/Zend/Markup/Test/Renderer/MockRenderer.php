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
 * @version    $Id$
 */

/**
 * @see Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * @see Zend_Markup_Renderer_RendererAbstract
 */
require_once 'Zend/Markup/Renderer/RendererAbstract.php';

/**
 * HTML renderer
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Markup_Test_Renderer_MockRenderer extends Zend_Markup_Renderer_RendererAbstract
{

    /**
     * Set the default filter
     *
     * @return void
     */
    public function setDefaultFilter(Zend_Filter_Interface $filter = null)
    {
        if (empty($filter)) {
            $this->_defaultFilter = new Zend_Filter();
        } else {
            $this->_defaultFilter = $filter;
        }
    }
}
