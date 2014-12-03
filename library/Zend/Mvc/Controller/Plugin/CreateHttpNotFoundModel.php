<?php

namespace Zend\Mvc\Controller\Plugin;

use Zend\Http\PhpEnvironment\Response;
use Zend\View\Model\ViewModel;

/**
 * Create an HTTP view model representing a "not found" page
 */
class CreateHttpNotFoundModel extends AbstractPlugin
{
    /**
     * @param  Response $response
     * @return ViewModel
     */
    public function __invoke(Response $response)
    {
        $response->setStatusCode(404);

        return new ViewModel(
            [
                'content' => 'Page not found',
            ]
        );
    }
}
