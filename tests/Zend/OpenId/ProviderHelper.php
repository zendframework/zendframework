<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OpenId
 */

namespace ZendTest\OpenId;

use Zend\OpenId;

/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage UnitTests
 */
class ProviderHelper extends OpenId\Provider\GenericProvider
{
    public function genSecret($func)
    {
        return $this->_genSecret($func);
    }
}
