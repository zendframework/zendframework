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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Helper;

use Zend\View\Exception,
    Zend\View\Model\ModelInterface as Model;

/**
 * View helper for retrieving layout object
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Layout extends AbstractHelper
{
    /**
     * @var ViewModel
     */
    protected $viewModelHelper;

    /**
     * Get layout template
     *
     * @return string
     */
    public function getLayout()
    {
        $model = $this->getRoot();
        return $model->getTemplate();
    }

    /**
     * Set layout template
     *
     * @param  string $template
     * @return Layout
     */
    public function setTemplate($template)
    {
        $model = $this->getRoot();
        $model->setTemplate((string) $template);
        return $this;
    }

    /**
     * Set layout template or retrieve "layout" view model
     *
     * If no arguments are given, grabs the "root" or "layout" view model.
     * Otherwise, attempts to set the template for that view model.
     *
     * @param  null|string $template
     * @return Layout
     */
    public function __invoke($template = null)
    {
        if (null === $template) {
            return $this->getRoot();
        }
        return $this->setTemplate($template);
    }

    /**
     * Get the root view model
     * 
     * @return null|Model
     */
    protected function getRoot()
    {
        $helper = $this->getViewModelHelper();
        if (!$helper->hasRoot()) {
            throw new Exception\RuntimeException(sprintf(
                '%s: no view model currently registered as root in renderer',
                __METHOD__
            ));
        }
        return $helper->getRoot();
    }

    /**
     * Retrieve the view model helper
     * 
     * @return ViewModel
     */
    protected function getViewModelHelper()
    {
        if ($this->viewModelHelper) {
            return $this->viewModelHelper;
        }
        $view = $this->getView();
        $this->viewModelHelper = $view->plugin('view_model');
        return $this->viewModelHelper;
    }
}
