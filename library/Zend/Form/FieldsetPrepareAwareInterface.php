<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form;

/**
 * @category   Zend
 * @package    Zend\Form
 */
interface FieldsetPrepareAwareInterface
{
    /**
     * Prepare the fieldset element (called while this fieldset is added to another one)
     *
     * @return mixed
     */
    public function prepareFieldset();
}
