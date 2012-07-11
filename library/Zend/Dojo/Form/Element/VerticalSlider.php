<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo\Form\Element;

/**
 * VerticalSlider dijit
 *
 * @package    Zend_Dojo
 * @subpackage Form_Element
 */
class VerticalSlider extends Slider
{
    /**
     * Use VerticalSlider dijit view helper
     * @var string
     */
    public $helper = 'VerticalSlider';

    /**
     * Get left decoration data
     *
     * @return array
     */
    public function getLeftDecoration()
    {
        if ($this->hasDijitParam('leftDecoration')) {
            return $this->getDijitParam('leftDecoration');
        }
        return array();
    }

    /**
     * Set dijit to use with left decoration
     *
     * @param mixed $dijit
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setLeftDecorationDijit($dijit)
    {
        $decoration = $this->getLeftDecoration();
        $decoration['dijit'] = (string) $dijit;
        $this->setDijitParam('leftDecoration', $decoration);
        return $this;
    }

    /**
     * Set container to use with left decoration
     *
     * @param mixed $container
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setLeftDecorationContainer($container)
    {
        $decoration = $this->getLeftDecoration();
        $decoration['container'] = (string) $container;
        $this->setDijitParam('leftDecoration', $decoration);
        return $this;
    }

    /**
     * Set labels to use with left decoration
     *
     * @param  array $labels
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setLeftDecorationLabels(array $labels)
    {
        $decoration = $this->getLeftDecoration();
        $decoration['labels'] = array_values($labels);
        $this->setDijitParam('leftDecoration', $decoration);
        return $this;
    }

    /**
     * Set params to use with left decoration
     *
     * @param  array $params
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setLeftDecorationParams(array $params)
    {
        $decoration = $this->getLeftDecoration();
        $decoration['params'] = $params;
        $this->setDijitParam('leftDecoration', $decoration);
        return $this;
    }

    /**
     * Set attribs to use with left decoration
     *
     * @param  array $attribs
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setLeftDecorationAttribs(array $attribs)
    {
        $decoration = $this->getLeftDecoration();
        $decoration['attribs'] = $attribs;
        $this->setDijitParam('leftDecoration', $decoration);
        return $this;
    }

    /**
     * Get right decoration data
     *
     * @return array
     */
    public function getRightDecoration()
    {
        if ($this->hasDijitParam('rightDecoration')) {
            return $this->getDijitParam('rightDecoration');
        }
        return array();
    }

    /**
     * Set dijit to use with right decoration
     *
     * @param mixed $dijit
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setRightDecorationDijit($dijit)
    {
        $decoration = $this->getRightDecoration();
        $decoration['dijit'] = (string) $dijit;
        $this->setDijitParam('rightDecoration', $decoration);
        return $this;
    }

    /**
     * Set container to use with right decoration
     *
     * @param mixed $container
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setRightDecorationContainer($container)
    {
        $decoration = $this->getRightDecoration();
        $decoration['container'] = (string) $container;
        $this->setDijitParam('rightDecoration', $decoration);
        return $this;
    }

    /**
     * Set labels to use with right decoration
     *
     * @param  array $labels
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setRightDecorationLabels(array $labels)
    {
        $decoration = $this->getRightDecoration();
        $decoration['labels'] = array_values($labels);
        $this->setDijitParam('rightDecoration', $decoration);
        return $this;
    }

    /**
     * Set params to use with right decoration
     *
     * @param  array $params
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setRightDecorationParams(array $params)
    {
        $decoration = $this->getRightDecoration();
        $decoration['params'] = $params;
        $this->setDijitParam('rightDecoration', $decoration);
        return $this;
    }

    /**
     * Set attribs to use with right decoration
     *
     * @param  array $attribs
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setRightDecorationAttribs(array $attribs)
    {
        $decoration = $this->getRightDecoration();
        $decoration['attribs'] = $attribs;
        $this->setDijitParam('rightDecoration', $decoration);
        return $this;
    }
}
