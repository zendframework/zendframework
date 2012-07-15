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

use Zend\Serializer\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage Adapter
 */
class PhpCode extends AbstractAdapter
{
    /**
     * Serialize PHP using var_export
     *
     * @param  mixed $value
     * @param  array $opts
     * @return string
     */
    public function serialize($value, array $opts = array())
    {
        return var_export($value, true);
    }

    /**
     * Deserialize PHP string
     *
     * Warning: this uses eval(), and should likely be avoided.
     *
     * @param  string $code
     * @param  array $opts
     * @return mixed
     * @throws RuntimeException on eval error
     */
    public function unserialize($code, array $opts = array())
    {
        $eval = @eval('$ret=' . $code . ';');
        if ($eval === false) {
            $lastErr = error_get_last();
            throw new RuntimeException('eval failed: ' . $lastErr['message']);
        }
        return $ret;
    }
}
