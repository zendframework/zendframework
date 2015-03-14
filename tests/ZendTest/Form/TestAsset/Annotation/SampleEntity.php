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

class SampleEntity
{
    /**
     * @Annotation\ErrorMessage("Invalid or missing sampleinput")
     * @Annotation\Required(true)
     * @Annotation\AllowEmpty(true)
     * @Annotation\ContinueIfEmpty(true)
     */
    public $sampleinput;

    /**
     *
     * @Annotation\Attributes({"type":"text"})
     */
    public $anotherSampleInput;
}
