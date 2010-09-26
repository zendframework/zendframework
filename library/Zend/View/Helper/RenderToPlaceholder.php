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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;

/**
 * Renders a template and stores the rendered output as a placeholder
 * variable for later use.
 *
 * @uses       \Zend\View\Helper\AbstractHelper
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class RenderToPlaceholder extends AbstractHelper
{

    /**
     * Renders a template and stores the rendered output as a placeholder
     * variable for later use.
     *
     * @param $script The template script to render
     * @param $placeholder The placeholder variable name in which to store the rendered output
     * @return void
     */
    public function direct($script = null, $placeholder = null)
    {
        if ($script == null || $placeholder == null) {
            throw new \InvalidArgumentException('Action: missing argument. $script and $placeholder are required in renderToPlaceholder($script, $placeholder)');
        }
        
        $this->view->placeholder($placeholder)->captureStart();
        echo $this->view->render($script);
        $this->view->placeholder($placeholder)->captureEnd();
    }
}
