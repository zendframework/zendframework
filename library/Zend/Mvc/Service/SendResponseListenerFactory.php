<?php

namespace Zend\Mvc\Service;

use Zend\Mvc\SendResponseListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SendResponseListenerFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $defaultOptions = array(
        'Zend\Http\PhpEnvironment\Response' => 'HttpResponseSender',
        'Zend\Console\Response'             => 'ConsoleResponseSender',
        'Zend\Http\Response\Stream'         => 'StreamResponseSender',
        'Zend\Http\Response'                => 'HttpResponseSender',
    );

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return SendResponseListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (isset($config['send_response_listener'])) {
            $options = array_merge($this->defaultOptions, $config['send_response_listener']);
        } else {
            $options = $this->defaultOptions;
        }
        return new SendResponseListener($options);
    }

}
