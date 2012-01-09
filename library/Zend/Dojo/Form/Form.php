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
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Dojo\Form;

use Zend\View\Renderer as View;

/**
 * Dijit-enabled Form
 *
 * @uses       \Zend\Form\Form
 * @package    Zend_Dojo
 * @subpackage Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Form extends \Zend\Form\Form
{
    /**
     * Constructor
     *
     * @param  array|\Zend\Config\Config|null $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->addPrefixPath('Zend\Dojo\Form\Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
             ->addPrefixPath('Zend\Dojo\Form\Element', 'Zend/Dojo/Form/Element', 'element')
             ->addElementPrefixPath('Zend\Dojo\Form\Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
             ->addDisplayGroupPrefixPath('Zend\Dojo\Form\Decorator', 'Zend/Dojo/Form/Decorator')
             ->setDefaultDisplayGroupClass('Zend\Dojo\Form\DisplayGroup');
        parent::__construct($options);
    }

    /**
     * Load the default decorators
     *
     * @return void
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FormElements')
                 ->addDecorator('HtmlTag', array('tag' => 'dl', 'class' => 'zend_form_dojo'))
                 ->addDecorator('DijitForm');
        }
    }

    /**
     * Set the view object
     *
     * Ensures that the view object has the dojo view helper path set.
     *
     * @param  \Zend\View\Renderer $view
     * @return \Zend\Dojo\Form\Element\Dijit
     */
    public function setView(View $view = null)
    {
        if (null !== $view) {
            if(false === $view->getBroker()->isLoaded('dojo')) {
                $loader = new \Zend\Dojo\View\HelperLoader();
                $view->getBroker()->getClassLoader()->registerPlugins($loader);
            }
        }
        return parent::setView($view);
    }
}
