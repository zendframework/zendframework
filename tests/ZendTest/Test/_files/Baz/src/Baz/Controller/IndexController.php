<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Baz\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Response;

class IndexController extends AbstractActionController
{
    public function unittestsAction()
    {
        $this->getResponse()
            ->getHeaders()
            ->addHeaderLine('Content-Type: text/html')
            ->addHeaderLine('WWW-Authenticate: Basic realm="ZF2"');

        $num_get = $this->getRequest()->getQuery()->get('num_get', 0);
        $num_post = $this->getRequest()->getPost()->get('num_post', 0);

        return array('num_get' => $num_get, 'num_post' => $num_post);
    }

    public function consoleAction()
    {
        return 'foo, bar';
    }

    public function persistencetestAction()
    {
        $this->flashMessenger()->addMessage('test');
    }

    public function redirectAction()
    {
        return $this->redirect()->toUrl('http://www.zend.com');
    }

    public function exceptionAction()
    {
        throw new \RuntimeException('Foo error !');
    }

    public function customResponseAction()
    {
        $response = new Response();
        $response->setCustomStatusCode(999);

        return $response;
    }
}
