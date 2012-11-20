<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter;

use Zend\Db\Adapter\Adapter;

/**
 * @category   Zend
 * @packcage   Zend_Db
 * @subpackage Adapter
 */
trait AdapterAwareTrait
{
    /**
     * @var Adapter
     */
    protected $adapter = null;

    /**
     * Set db adapter
     *
     * @param Adapter $adapter
     * @return mixed
     */
    public function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }
}
