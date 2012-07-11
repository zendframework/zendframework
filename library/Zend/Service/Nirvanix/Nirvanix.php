<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Nirvanix;

use Traversable;
use Zend\Http\Client as HttpClient;
use Zend\Stdlib\ArrayUtils;

/**
 * This class allows Nirvanix authentication credentials to be specified
 * in one place and provides a factory for returning convenience wrappers
 * around the Nirvanix web service namespaces.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Nirvanix
 */
class Nirvanix
{
    /**
     * Options to pass to namespace proxies
     * @param array
     */
    protected $options;

    /**
     * Class constructor.  Authenticates with Nirvanix to receive a
     * sessionToken, which is then passed to each future request.
     *
     * @param  array  $authParams  Authentication POST parameters.  This
     *                             should have keys "username", "password",
     *                             and "appKey".
     * @param  array  $options     Options to pass to namespace proxies
     */
    public function __construct($authParams, $options = array())
    {
        // merge options with default options
        $defaultOptions = array(
            'defaults'   => array(),
            'httpClient' => new HttpClient(),
            'host'       => 'http://services.nirvanix.com',
        );

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable object of options');
        }

        $this->options = array_merge($defaultOptions, $options);

        // login and save sessionToken to default POST params
        $resp = $this->getService('Authentication')->login($authParams);
        $this->options['defaults']['sessionToken'] = (string) $resp->SessionToken;
    }

    /**
     * Nirvanix divides its service into namespaces, with each namespace
     * providing different functionality.  This is a factory method that
     * returns a preconfigured Context\Base proxy.
     *
     * @param  string  $namespace  Name of the namespace
     * @return Context\Base
     */
    public function getService($namespace, $options = array())
    {
        switch ($namespace) {
            case 'IMFS':
                $class = __NAMESPACE__ . '\Context\Imfs';
                break;
            default:
                $class = __NAMESPACE__ . '\Context\Base';
        }

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . ' expects an array or Traversable object of options');
        }

        $options['namespace'] = ucfirst($namespace);
        $options = array_merge($this->options, $options);

        return new $class($options);
    }

    /**
     * Get the configured options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
