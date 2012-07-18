<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Serializer
 */

namespace Zend\Serializer\Adapter;

use Zend\Stdlib\AbstractOptions;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 */
class AdapterOptions extends AbstractOptions
{
    /**
     * @see isBinary()
     * @var bool
     */
    protected $isBinary = false;

    /**
     * Whether adapter input/output format is binary
     *
     * @return bool
     */
    public function isBinary()
    {
        return $this->isBinary;
    }
}
