<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\TestAsset\Annotation;

use Zend\Form\Annotation;

/**
 * @Annotation\Name("hierarchical")
 */
class EntityUsingComposedObjectAndOptions
{
    /**
     * @Annotation\Name("child")
     * @Annotation\Options({"label": "My label"})
     * @Annotation\ComposedObject({"target_object":"ZendTest\Form\TestAsset\Annotation\Entity", "is_collection":"true"})
     */
    public $child;

    /**
     * @Annotation\Name("childTheSecond")
     * @Annotation\ComposedObject({"target_object":"ZendTest\Form\TestAsset\Annotation\Entity", "is_collection":"true"})
     * @Annotation\Options({"label": "My label"})
     */
    public $childTheSecond;

    /**
     * @Annotation\Name("childTheThird")
     * @Annotation\ComposedObject("ZendTest\Form\TestAsset\Annotation\Entity")
     * @Annotation\Options({"label": "My label"})
     */
    public $childTheThird;

    /**
     * @Annotation\Name("childTheFourth")
     * @Annotation\Options({"label": "My label"})
     * @Annotation\ComposedObject("ZendTest\Form\TestAsset\Annotation\Entity")
     */
    public $childTheFourth;
}
