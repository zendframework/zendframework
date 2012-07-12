<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo;

use Zend\View\Renderer\PhpRenderer;
use Zend\View\Renderer\RendererInterface as Renderer;

/**
 * Enable Dojo components
 *
 * @package    Zend_Dojo
 */
class Dojo
{
    /**
     * Base path to AOL CDN
     */
    const CDN_BASE_AOL = 'http://o.aolcdn.com/dojo/';

    /**
     * Path to dojo on AOL CDN (following version string)
     */
    const CDN_DOJO_PATH_AOL = '/dojo/dojo.xd.js';

    /**
     * Base path to Google CDN
     */
    const CDN_BASE_GOOGLE = 'http://ajax.googleapis.com/ajax/libs/dojo/';

    /**
     * Path to dojo on Google CDN (following version string)
     */
    const CDN_DOJO_PATH_GOOGLE = '/dojo/dojo.xd.js';

    /**
     * Dojo-enable a form instance
     *
     * @param  \Zend\Form\Form $form
     * @return void
     */
    public static function enableForm(\Zend\Form\Form $form)
    {
        $form->addPrefixPath('Zend\Dojo\Form\Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
             ->addPrefixPath('Zend\Dojo\Form\Element', 'Zend/Dojo/Form/Element', 'element')
             ->addElementPrefixPath('Zend\Dojo\Form\Decorator', 'Zend/Dojo/Form/Decorator', 'decorator')
             ->addDisplayGroupPrefixPath('Zend\Dojo\Form\Decorator', 'Zend/Dojo/Form/Decorator')
             ->setDefaultDisplayGroupClass('Zend\Dojo\Form\DisplayGroup');

        foreach ($form->getSubForms() as $subForm) {
            self::enableForm($subForm);
        }

        if (null !== ($view = $form->getView())) {
            self::enableView($view);
        }
    }

    /**
     * Dojo-enable a view instance
     *
     * @param  Renderer $view
     * @return void
     */
    public static function enableView(Renderer $view)
    {
        if (!$view instanceof PhpRenderer) {
            return;
        }

        $view->getBroker()
             ->getClassLoader()
             ->registerPlugins(new View\HelperLoader());
    }

    /**
     * Dojo-disable a dojo enabled view
     *
     * @param  Renderer $view
     * @return void
     */
    public static function disableView(Renderer $view)
    {
        if (!$view instanceof PhpRenderer) {
            return;
        }

        $broker  = $view->getBroker();
        $loader  = $broker->getClassLoader();
        $plugins = $broker->getPlugins();
        foreach ($plugins as $plugin => $void) {
            $broker->unregister($plugin);
            $loader->unregisterPlugin($plugin);
        }
    }
}

