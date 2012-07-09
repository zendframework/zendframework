<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo\View;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for Dojo view helpers.
 *
 * @category   Zend
 * @package    Zend_Dojo
 */
class HelperLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased view helpers
     */
    protected $plugins = array(
        'accordioncontainer' => 'Zend\Dojo\View\Helper\AccordionContainer',
        'accordionpane'      => 'Zend\Dojo\View\Helper\AccordionPane',
        'bordercontainer'    => 'Zend\Dojo\View\Helper\BorderContainer',
        'button'             => 'Zend\Dojo\View\Helper\Button',
        'checkbox'           => 'Zend\Dojo\View\Helper\CheckBox',
        'combobox'           => 'Zend\Dojo\View\Helper\ComboBox',
        'contentpane'        => 'Zend\Dojo\View\Helper\ContentPane',
        'currencytextbox'    => 'Zend\Dojo\View\Helper\CurrencyTextBox',
        'customdijit'        => 'Zend\Dojo\View\Helper\CustomDijit',
        'datetextbox'        => 'Zend\Dojo\View\Helper\DateTextBox',
        'dijitcontainer'     => 'Zend\Dojo\View\Helper\DijitContainer',
        'dijit'              => 'Zend\Dojo\View\Helper\Dijit',
        'dojo'               => 'Zend\Dojo\View\Helper\Dojo',
        'editor'             => 'Zend\Dojo\View\Helper\Editor',
        'filteringselect'    => 'Zend\Dojo\View\Helper\FilteringSelect',
        'dojoform'           => 'Zend\Dojo\View\Helper\DojoForm',
        'horizontalslider'   => 'Zend\Dojo\View\Helper\HorizontalSlider',
        'numberspinner'      => 'Zend\Dojo\View\Helper\NumberSpinner',
        'numbertextbox'      => 'Zend\Dojo\View\Helper\NumberTextBox',
        'passwordtextbox'    => 'Zend\Dojo\View\Helper\PasswordTextBox',
        'radiobutton'        => 'Zend\Dojo\View\Helper\RadioButton',
        'simpletextarea'     => 'Zend\Dojo\View\Helper\SimpleTextarea',
        'slider'             => 'Zend\Dojo\View\Helper\Slider',
        'splitcontainer'     => 'Zend\Dojo\View\Helper\SplitContainer',
        'stackcontainer'     => 'Zend\Dojo\View\Helper\StackContainer',
        'submitbutton'       => 'Zend\Dojo\View\Helper\SubmitButton',
        'tabcontainer'       => 'Zend\Dojo\View\Helper\TabContainer',
        'textarea'           => 'Zend\Dojo\View\Helper\Textarea',
        'textbox'            => 'Zend\Dojo\View\Helper\TextBox',
        'timetextbox'        => 'Zend\Dojo\View\Helper\TimeTextBox',
        'validationtextbox'  => 'Zend\Dojo\View\Helper\ValidationTextBox',
        'verticalslider'     => 'Zend\Dojo\View\Helper\VerticalSlider',
    );
}
