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
 * HorizontalSlider dijit
 *
 * @package    Zend_Dojo
 * @subpackage Form_Element
 */
class HorizontalSlider extends Slider
{
    /**
     * Use HorizontalSlider dijit view helper
     * @var string
     */
    public $helper = 'HorizontalSlider';

    /**
     * Get top decoration data
     *
     * @return array
     */
    public function getTopDecoration()
    {
        if ($this->hasDijitParam('topDecoration')) {
            return $this->getDijitParam('topDecoration');
        }
        return array();
    }

    /**
     * Set dijit to use with top decoration
     *
     * @param mixed $dijit
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setTopDecorationDijit($dijit)
    {
        $decoration = $this->getTopDecoration();
        $decoration['dijit'] = (string) $dijit;
        $this->setDijitParam('topDecoration', $decoration);
        return $this;
    }

    /**
     * Set container to use with top decoration
     *
     * @param mixed $container
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setTopDecorationContainer($container)
    {
        $decoration = $this->getTopDecoration();
        $decoration['container'] = (string) $container;
        $this->setDijitParam('topDecoration', $decoration);
        return $this;
    }

    /**
     * Set labels to use with top decoration
     *
     * @param  array $labels
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setTopDecorationLabels(array $labels)
    {
        $decoration = $this->getTopDecoration();
        $decoration['labels'] = array_values($labels);
        $this->setDijitParam('topDecoration', $decoration);
        return $this;
    }

    /**
     * Set params to use with top decoration
     *
     * @param  array $params
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setTopDecorationParams(array $params)
    {
        $decoration = $this->getTopDecoration();
        $decoration['params'] = $params;
        $this->setDijitParam('topDecoration', $decoration);
        return $this;
    }

    /**
     * Set attribs to use with top decoration
     *
     * @param  array $attribs
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setTopDecorationAttribs(array $attribs)
    {
        $decoration = $this->getTopDecoration();
        $decoration['attribs'] = $attribs;
        $this->setDijitParam('topDecoration', $decoration);
        return $this;
    }

    /**
     * Get bottom decoration data
     *
     * @return array
     */
    public function getBottomDecoration()
    {
        if ($this->hasDijitParam('bottomDecoration')) {
            return $this->getDijitParam('bottomDecoration');
        }
        return array();
    }

    /**
     * Set dijit to use with bottom decoration
     *
     * @param mixed $dijit
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setBottomDecorationDijit($dijit)
    {
        $decoration = $this->getBottomDecoration();
        $decoration['dijit'] = (string) $dijit;
        $this->setDijitParam('bottomDecoration', $decoration);
        return $this;
    }

    /**
     * Set container to use with bottom decoration
     *
     * @param mixed $container
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setBottomDecorationContainer($container)
    {
        $decoration = $this->getBottomDecoration();
        $decoration['container'] = (string) $container;
        $this->setDijitParam('bottomDecoration', $decoration);
        return $this;
    }

    /**
     * Set labels to use with bottom decoration
     *
     * @param  array $labels
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setBottomDecorationLabels(array $labels)
    {
        $decoration = $this->getBottomDecoration();
        $decoration['labels'] = array_values($labels);
        $this->setDijitParam('bottomDecoration', $decoration);
        return $this;
    }

    /**
     * Set params to use with bottom decoration
     *
     * @param  array $params
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setBottomDecorationParams(array $params)
    {
        $decoration = $this->getBottomDecoration();
        $decoration['params'] = $params;
        $this->setDijitParam('bottomDecoration', $decoration);
        return $this;
    }

    /**
     * Set attribs to use with bottom decoration
     *
     * @param  array $attribs
     * @return \Zend\Dojo\Form\Element\HorizontalSlider
     */
    public function setBottomDecorationAttribs(array $attribs)
    {
        $decoration = $this->getBottomDecoration();
        $decoration['attribs'] = $attribs;
        $this->setDijitParam('bottomDecoration', $decoration);
        return $this;
    }
}
