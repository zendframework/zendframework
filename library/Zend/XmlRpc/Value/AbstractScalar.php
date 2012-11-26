<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace Zend\XmlRpc\Value;

use Zend\XmlRpc\AbstractValue;

/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage Value
 */
abstract class AbstractScalar extends AbstractValue
{
    /**
     * Generate the XML code that represent a scalar native MXL-RPC value
     *
     * @return void
     */
    protected function _generateXml()
    {
        $generator = $this->getGenerator();

        $generator->openElement('value')
                  ->openElement($this->type, $this->value)
                  ->closeElement($this->type)
                  ->closeElement('value');
    }
}
