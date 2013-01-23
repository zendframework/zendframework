<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Controller\Plugin;

use Zend\Filter\FilterChain;
use Zend\Form\Form;
use Zend\Http\Response;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilterInterface;
use Zend\Mvc\Exception\RuntimeException;
use Zend\Session\Container;
use Zend\Stdlib\ArrayUtils;
use Zend\Validator\ValidatorChain;

/**
 * Plugin to help facilitate Post/Redirect/Get for file upload forms
 * (http://en.wikipedia.org/wiki/Post/Redirect/Get)
 *
 * Requires that the Form's File inputs contain a 'fileRenameUpload' filter
 * with the target option set: 'target' => /valid/target/path'.
 * This is so the files are moved to a new location between requests.
 * If this filter is not added, the temporary upload files will disappear
 * between requests.
 */
class FilePostRedirectGet extends AbstractPlugin
{
    /**
     * @var Container
     */
    protected $sessionContainer;

    /**
     * @param  Form    $form
     * @param  string  $redirect      Route or URL string (default: current route)
     * @param  boolean $redirectToUrl Use $redirect as a URL string (default: false)
     * @return boolean|array|Response
     */
    public function __invoke(Form $form, $redirect = null, $redirectToUrl = false)
    {
        $request = $this->getController()->getRequest();
        if ($request->isPost()) {
            return $this->handlePostRequest($form, $redirect, $redirectToUrl);
        } else {
            return $this->handleGetRequest($form);
        }
    }

    /**
     * @param  Form    $form
     * @param  string  $redirect      Route or URL string (default: current route)
     * @param  boolean $redirectToUrl Use $redirect as a URL string (default: false)
     * @return Response
     */
    protected function handlePostRequest(Form $form, $redirect, $redirectToUrl)
    {
        $container = $this->getSessionContainer();
        $request   = $this->getController()->getRequest();

        // Change required flag to false for any previously uploaded files
        $inputFilter   = $form->getInputFilter();
        $previousFiles = ($container->files) ?: array();
        $this->traverseInputs(
            $inputFilter,
            $previousFiles,
            function($input, $value) {
                if ($input instanceof FileInput) {
                    $input->setRequired(false);
                }
                return $value;
            }
        );

        // Run the form validations/filters and retrieve any errors
        $postFiles = $request->getFiles()->toArray();
        $postOther = $request->getPost()->toArray();
        $post      = ArrayUtils::merge($postOther, $postFiles);

        $form->setData($post);
        $isValid = $form->isValid();
        $data    = $form->getData(Form::VALUES_AS_ARRAY);
        $errors  = (!$isValid) ? $form->getMessages() : null;

        // Loop through data and merge previous files with new valid files
        $postFiles = ArrayUtils::merge(
            $previousFiles,
            $this->filterInvalidFileInputPostData($inputFilter, $data)
        );
        $post = ArrayUtils::merge($post, $postFiles);

        // Save form data in session
        $container->setExpirationHops(1, array('post', 'errors', 'isValid'));
        $container->post    = $post;
        $container->errors  = $errors;
        $container->isValid = $isValid;
        $container->files   = $postFiles;

        return $this->redirect($redirect, $redirectToUrl);
    }

    /**
     * @param  Form $form
     * @return boolean|array
     */
    protected function handleGetRequest(Form $form)
    {
        $container = $this->getSessionContainer();
        if (null === $container->post) {
            // No previous post, bail early
            unset($container->files);
            return false;
        }

        // Collect data from session
        $post          = $container->post;
        $errors        = $container->errors;
        $isValid       = $container->isValid;
        $previousFiles = ($container->files) ?: array();
        unset($container->post);
        unset($container->errors);
        unset($container->isValid);

        // Remove File Input validators and filters on previously uploaded files
        // in case $form->isValid() or $form->bindValues() is run
        $inputFilter = $form->getInputFilter();
        $this->traverseInputs(
            $inputFilter,
            $post,
            function($input, $value) {
                if ($input instanceof FileInput) {
                    $input->setAutoPrependUploadValidator(false)
                          ->setValidatorChain(new ValidatorChain())
                          ->setFilterChain(new FilterChain);
                }
                return $value;
            }
        );

        // Fill form with previous info and state
        $form->setData($post);
        $form->isValid(); // re-validate to bind values
        if (null !== $errors) {
            $form->setMessages($errors); // overwrite messages
        }
        $this->setProtectedFormProperty($form, 'isValid', $isValid); // force previous state

        // Clear previous files from session data if form was valid
        if ($isValid) {
            unset($container->files);
        }

        return $post;
    }

    /**
     * @return Container
     */
    public function getSessionContainer()
    {
        if (!isset($this->sessionContainer)) {
            $this->sessionContainer = new Container('file_prg_post1');
        }
        return $this->sessionContainer;
    }

    /**
     * @param  Form   $form
     * @param  string $property
     * @param  mixed  $value
     * @return FilePostRedirectGet
     */
    protected function setProtectedFormProperty(Form $form, $property, $value)
    {
        $formClass = new \ReflectionClass('Zend\Form\Form');
        $property  = $formClass->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($form, $value);
        return $this;
    }

    /**
     * @param  InputFilterInterface $inputFilter
     * @param  array                $values
     * @param  callable             $callback
     * @return array|null
     */
    protected function traverseInputs(InputFilterInterface $inputFilter, $values, $callback)
    {
        $returnValues = null;
        foreach ($values as $name => $value) {
            if (!$inputFilter->has($name)) {
                continue;
            }

            $input = $inputFilter->get($name);
            if ($input instanceof InputFilterInterface && is_array($value)) {
                $retVal = $this->traverseInputs($input, $value, $callback);
                if (null !== $retVal) {
                    $returnValues[$name] = $retVal;
                }
                continue;
            }

            $retVal = $callback($input, $value);
            if (null !== $retVal) {
                $returnValues[$name] = $retVal;
            }
        }
        return $returnValues;
    }

    /**
     * @param  InputFilterInterface $inputFilter
     * @param  array                $data
     * @return array
     */
    protected function filterInvalidFileInputPostData(InputFilterInterface $inputFilter, $data)
    {
        $returnValues = array();
        $validInputs = $inputFilter->getValidInput();
        foreach ($validInputs as $name => $input) {
            if (!isset($data[$name])) {
                continue;
            }
            $dataValue = $data[$name];

            if ($input instanceof InputFilterInterface && is_array($dataValue)) {
                $retVal = $this->filterInvalidFileInputPostData($input, $dataValue);
                if (!empty($retVal)) {
                    $returnValues[$name] = $retVal;
                }
                continue;
            }

            $messages = $input->getMessages();
            if (is_array($dataValue)
                && $input instanceof FileInput
                && empty($messages)
            ) {
                if (   (isset($dataValue['error'])    && $dataValue['error']    === UPLOAD_ERR_OK)
                    || (isset($dataValue[0]['error']) && $dataValue[0]['error'] === UPLOAD_ERR_OK)
                ) {
                    $returnValues[$name] = $dataValue;
                }
            }
        }
        return $returnValues;
    }

    /**
     * @param  string  $redirect
     * @param  boolean $redirectToUrl
     * @return Response
     * @throws \Zend\Mvc\Exception\RuntimeException
     */
    protected function redirect($redirect, $redirectToUrl)
    {
        $controller = $this->getController();
        $params     = array();

        if (null === $redirect) {
            $routeMatch = $controller->getEvent()->getRouteMatch();

            $redirect = $routeMatch->getMatchedRouteName();
            $params   = $routeMatch->getParams();
        }

        if (method_exists($controller, 'getPluginManager')) {
            // get the redirect plugin from the plugin manager
            $redirector = $controller->getPluginManager()->get('Redirect');
        } else {
            /*
             * If the user wants to redirect to a route, the redirector has to come
             * from the plugin manager -- otherwise no router will be injected
             */
            if ($redirectToUrl === false) {
                throw new RuntimeException('Could not redirect to a route without a router');
            }

            $redirector = new Redirect();
        }

        if ($redirectToUrl === false) {
            $response = $redirector->toRoute($redirect, $params);
            $response->setStatusCode(303);
            return $response;
        }

        $response = $redirector->toUrl($redirect);
        $response->setStatusCode(303);

        return $response;
    }
}
