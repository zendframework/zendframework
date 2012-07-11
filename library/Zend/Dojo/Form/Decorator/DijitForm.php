<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo\Form\Decorator;

/**
 * Zend_Dojo_Form_Decorator_DijitForm
 *
 * Render a dojo form dijit via a view helper
 *
 * Accepts the following options:
 * - helper:    the name of the view helper to use
 *
 * @package    Zend_Dojo
 * @subpackage Form_Decorator
 */
class DijitForm extends DijitContainer
{
    /**
     * Render a form
     *
     * Replaces $content entirely from currently set element.
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        $view    = $element->getView();
        if (null === $view) {
            return $content;
        }

        $dijitParams = $this->getDijitParams();
        $attribs     = array_merge($this->getAttribs(), $this->getOptions());

        return $view->dojoform($element->getName(), $attribs, $content);
    }
}
