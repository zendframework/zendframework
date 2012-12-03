<?php

namespace Zend\Mvc\Service;

use Zend\Mvc\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ResponseSenderFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $defaultOptions = array(
        'Zend\Http\PhpEnvironment\Response' => 'Zend\Mvc\ResponseSender\PhpEnvironmentResponseSender',
        'Zend\Console\Response'             => 'Zend\Mvc\ResponseSender\ConsoleResponseSender',
    );

    /**
     * Create response sender based on current response class
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return \Zend\Mvc\ResponseSender\ResponseSenderInterface
     * @throws Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (isset($config['response_sender'])) {
            $options = array_merge($this->defaultOptions, $config['response_sender']);
        } else {
            $options = $this->defaultOptions;
        }
        $response = $serviceLocator->get('Response');
        $responseClass = get_class($response);
        if (!isset($options[$responseClass])) {
            throw new Exception\RuntimeException(
                'No response sender for given response class "' . $responseClass . '" available.'
            );
        }
        $responseSender = new $options[$responseClass];
        /* @var $responseSender \Zend\Mvc\ResponseSender\ResponseSenderInterface */
        $responseSender->setResponse($response);
        return $responseSender;
    }

}
