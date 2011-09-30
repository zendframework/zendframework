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
 * @package    Zend_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Dojo\View\Helper;

/**
 * Dojo HorizontalSlider dijit
 *
 * @uses       \Zend\Dojo\View\Helper\Slider
 * @package    Zend_Dojo
 * @subpackage View
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
  */
class HorizontalSlider extends Slider
{
    /**
     * Dijit being used
     * @var string
     */
    protected $_dijit  = 'dijit.form.HorizontalSlider';

    /**
     * Slider type
     * @var string
     */
    protected $_sliderType = 'Horizontal';

    /**
     * dijit.form.HorizontalSlider
     *
     * @param  int $id
     * @param  mixed $value
     * @param  array $params  Parameters to use for dijit creation
     * @param  array $attribs HTML attributes
     * @return string
     */
    public function __invoke($id = null, $value = null, array $params = array(), array $attribs = array())
    {
        return $this->prepareSlider($id, $value, $params, $attribs);
    }
}
