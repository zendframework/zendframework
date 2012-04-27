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
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\View;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for form view helpers.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HelperLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased view helpers
     */
    protected $plugins = array(
        'form'                => 'Zend\Form\View\Helper\Form',
        'formelementerrors'   => 'Zend\Form\View\Helper\FormElementErrors',
        'form_element_errors' => 'Zend\Form\View\Helper\FormElementErrors',
        'forminput'           => 'Zend\Form\View\Helper\FormInput',
        'form_input'          => 'Zend\Form\View\Helper\FormInput',
        'formlabel'           => 'Zend\Form\View\Helper\FormLabel',
        'form_label'          => 'Zend\Form\View\Helper\FormLabel',
        'formmulticheckbox'   => 'Zend\Form\View\Helper\FormMultiCheckbox',
        'form_multicheckbox'  => 'Zend\Form\View\Helper\FormMultiCheckbox',
        'form_multi_checkbox' => 'Zend\Form\View\Helper\FormMultiCheckbox',
        'formradio'           => 'Zend\Form\View\Helper\FormRadio',
        'form_radio'          => 'Zend\Form\View\Helper\FormRadio',
        'formselect'          => 'Zend\Form\View\Helper\FormSelect',
        'form_select'         => 'Zend\Form\View\Helper\FormSelect',
        'formtextarea'        => 'Zend\Form\View\Helper\FormTextarea',
        'form_textarea'       => 'Zend\Form\View\Helper\FormTextarea',
    );
}
