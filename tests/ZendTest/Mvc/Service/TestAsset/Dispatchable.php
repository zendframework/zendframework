<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mvc\Service\TestAsset;

use Zend\Mvc\Controller\AbstractActionController;

class Dispatchable extends AbstractActionController
{
    /**
     * Override, so we can test injection
     */
    public function getEventManager()
    {
        return $this->events;
    }
}
