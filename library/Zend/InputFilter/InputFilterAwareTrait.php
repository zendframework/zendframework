<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace Zend\InputFilter;

use Zend\InputFilter\InputFilterInterface;

/**
 * @category  Zend
 * @package   Zend_InputFilter
 */
trait InputFilterAwareTrait
{
    /**
     * @var \Zend\InputFilter\InputFilterInterface
     */
    protected $input_filter = null;

    /**
     * setInputFilter
     *
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     * @return
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->input_filter = $inputFilter;

        return $this;
    }

    /**
     * getInputFilter
     *
     * @return \Zend\InputFilter\InputFilterInterface
     */
    public function getInputFilter()
    {
        return $this->input_filter;
    }
}
