<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\TestAsset;

use Zend\Form\FieldsetInterface;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\View\Helper\FormCollection as FormCollectionHelper;

class CustomFieldsetHelper extends AbstractHelper
{
    /**
     * @var FormCollection
     */
    protected $fieldsetHelper;

    public function __invoke(FieldsetInterface $fieldset)
    {
        $fieldsetHelper = $this->getFieldsetHelper();

        $name = preg_replace('/[^a-z0-9_-]+/', '', $fieldset->getName());
        $result = '<div id="customFieldset' . $name . '">' . $fieldsetHelper($fieldset) . '</div>';

        return $result;
    }

    /**
     * Retrieve the FormCollection helper
     *
     * @return FormCollection
     */
    protected function getFieldsetHelper()
    {
        if ($this->fieldsetHelper) {
            return $this->fieldsetHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->fieldsetHelper = $this->view->plugin('form_collection');
        }

        if (!$this->fieldsetHelper instanceof FormCollectionHelper) {
            $this->fieldsetHelper = new FormCollectionHelper();
        }

        return $this->fieldsetHelper;
    }
}
