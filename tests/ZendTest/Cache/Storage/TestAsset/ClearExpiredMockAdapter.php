<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\TestAsset;

use Zend\Cache\Storage\ClearExpiredInterface;

class ClearExpiredMockAdapter extends MockAdapter implements ClearExpiredInterface
{
    public function clearExpired()
    {
    }
}
