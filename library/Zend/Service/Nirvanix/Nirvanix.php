<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Nirvanix
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Nirvanix;

use Traversable,
    Zend\Http\Client as HttpClient,
    Zend\Stdlib\IteratorToArray;

/**
 * This class allows Nirvanix authentication credentials to be specified
 * in one place and provides a factory for returning convenience wrappers
 * around the Nirvanix web service namespaces.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Nirvanix
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
            $options = IteratorToArray::convert($options);
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
            $options = IteratorToArray::convert($options);
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
