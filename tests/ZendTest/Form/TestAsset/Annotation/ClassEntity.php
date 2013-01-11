<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\TestAsset\Annotation;

use Zend\Form\Annotation;

/**
 * @Annotation\Name("some_name")
 * @Annotation\Attributes({"legend":"Some Fieldset"})
 * @Annotation\InputFilter("ZendTest\Form\TestAsset\Annotation\InputFilter")
 * @Annotation\ValidationGroup({"omit", "keep"})
 */
class ClassEntity
{
    /**
     * @Annotation\Exclude()
     */
    public $omit;

    /**
     * @Annotation\Name("keeper")
     * @Annotation\Attributes({"type":"text"})
     */
    public $keep;
}
