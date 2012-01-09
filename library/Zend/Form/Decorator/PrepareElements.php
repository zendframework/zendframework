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
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Form\Decorator;

use Zend\Form;

/**
 * Zend_Form_Decorator_PrepareElements
 *
 * Render all form elements registered with current form
 *
 * Accepts following options:
 * - separator: Separator to use between elements
 *
 * Any other options passed will be used as HTML attributes of the form tag.
 *
 * @uses       \Zend\Form\Decorator\FormElements
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Decorator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PrepareElements extends FormElements
{
    /**
     * Render form elements
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $form = $this->getElement();
        if ((!$form instanceof Form\Form)
            && (!$form instanceof Form\DisplayGroup)
        ) {
            return $content;
        }

        $this->_recursivelyPrepareForm($form);

        return $content;
    }

    protected function _recursivelyPrepareForm(Form\Form $form)
    {
        $belongsTo      = ($form instanceof Form\Form)
                        ? $form->getElementsBelongTo()
                        : null;
        $elementContent = '';
        $separator      = $this->getSeparator();
        $translator     = $form->getTranslator();
        $view           = $form->getView();

        foreach ($form as $item) {
            $item->setView($view)
                 ->setTranslator($translator);
            if ($item instanceof Form\Element) {
                $item->setBelongsTo($belongsTo);
            } elseif ($item instanceof Form\Form) {
                if (!empty($belongsTo)) {
                    if ($item->isArray()) {
                        $name = $this->mergeBelongsTo($belongsTo, $item->getElementsBelongTo());
                        $item->setElementsBelongTo($name, true);
                    } else {
                        $item->setElementsBelongTo($belongsTo, true);
                    }
                }
                $this->_recursivelyPrepareForm($item);
            } elseif ($item instanceof Form\DisplayGroup) {
                if (!empty($belongsTo)) {
                    foreach ($item as $element) {
                        $element->setBelongsTo($belongsTo);
                    }
                }
            }
        }
    }
}
