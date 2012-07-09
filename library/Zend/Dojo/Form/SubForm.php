<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo\Form;

/**
 * Dijit-enabled SubForm
 *
 * @package    Zend_Dojo
 * @subpackage Form
 */
class SubForm extends \Zend\Form\SubForm
{
    /**
     * Has the dojo view helper path been registered?
     * @var bool
     */
    protected $_dojoViewPathRegistered = false;

    /**
     * Constructor
     *
     * @param  array|\Traversable $options
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
                 ->addDecorator('HtmlTag', array('tag' => 'dl'))
                 ->addDecorator('ContentPane');
        }
    }

    /**
     * Get view
     *
     * @return \Zend\View\Renderer\RendererInterface
     */
    public function getView()
    {
        $view = parent::getView();
        if (!$this->_dojoViewPathRegistered) {
            if(false === $view->getBroker()->isLoaded('dojo')) {
                $loader = new \Zend\Dojo\View\HelperLoader();
                $view->getBroker()->getClassLoader()->registerPlugins($loader);
            }
            $this->_dojoViewPathRegistered = true;
        }
        return $view;
    }
}
