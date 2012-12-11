<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace Zend\Form\Annotation;

use Zend\Filter\Boolean as BooleanFilter;

/**
 * AllowEmpty annotation
 *
 * Presence of this annotation is a hint that the associated
 * \Zend\InputFilter\Input should enable the allow_empty flag.
 *
 * @Annotation
 * @package    Zend_Form
 * @subpackage Annotation
 */
class AllowEmpty
{
    /**
     * @var bool
     */
    protected $allow_empty = true;

    /**
     * Receive and process the contents of an annotation
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (!isset($data['value'])) {
            $data['value'] = false;
        }

        $allow_empty = $data['value'];

        if (!is_bool($allow_empty)) {
            $filter   = new BooleanFilter();
            $allow_empty = $filter->filter($allow_empty);
        }

        $this->allow_empty = $allow_empty;
    }

    /**
     * Get value of required flag
     *
     * @return bool
     */
    public function getAllowEmpty()
    {
        return $this->allow_empty;
    }
}
