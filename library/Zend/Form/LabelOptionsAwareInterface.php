<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

interface LabelOptionsAwareInterface
{
    /**
     * Set the attributes to use with the label
     *
     * @param array $labelAttributes
     * @return self
     */
    public function setLabelAttributes(array $labelAttributes);

    /**
     * Get the attributes to use with the label
     *
     * @return array
     */
    public function getLabelAttributes();

    /**
     * Set label specific options
     *
     * @param array $labelOptions
     * @return self
     */
    public function setLabelOptions(array $labelOptions);

    /**
     * Get label specific options
     *
     * @return array
     */
    public function getLabelOptions();
}
