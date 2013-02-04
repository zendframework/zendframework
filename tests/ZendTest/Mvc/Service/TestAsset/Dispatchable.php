<?php
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
