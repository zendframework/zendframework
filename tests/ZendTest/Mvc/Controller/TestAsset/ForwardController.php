<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Controller\TestAsset;

use Zend\Mvc\Controller\AbstractActionController;

class ForwardController extends AbstractActionController
{
    public function testAction()
    {
        return array('content' => __METHOD__);
    }

    public function testMatchesAction()
    {
        $e = $this->getEvent();
        return $e->getRouteMatch()->getParams();
    }

    public function notFoundAction()
    {
        return array(
            'status' => 'not-found',
            'params' => $this->params()->fromRoute(),
        );
    }
}
