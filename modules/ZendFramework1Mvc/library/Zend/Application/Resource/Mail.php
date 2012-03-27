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
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application\Resource;

use Zend\Application\ResourceException;

/**
 * Resource for setting up Mail Transport and default From & ReplyTo addresses
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Mail extends AbstractResource
{

    /**
     * @var \Zend\Mail\AbstractTransport
     */
    protected $_transport;

    /**
     * Initialize mail resource
     *
     * @return \Zend\Mail\AbstractTransport
     */
    public function init()
    {
        return $this->getMail();
    }

    /**
     * @return \Zend\Mail\AbstractTransport|null
     */
    public function getMail()
    {
        if (null === $this->_transport) {
            $options = $this->getOptions();
            foreach($options as $key => $option) {
                $options[strtolower($key)] = $option;
            }
            $this->setOptions($options);

            if(isset($options['transport']) &&
               !is_numeric($options['transport']))
            {
                $this->_transport = $this->_setupTransport($options['transport']);
                if(!isset($options['transport']['register']) ||
                   $options['transport']['register'] == '1' ||
                   (isset($options['transport']['register']) &&
                        !is_numeric($options['transport']['register']) &&
                        (bool) $options['transport']['register'] == true))
                {
                    \Zend\Mail\Mail::setDefaultTransport($this->_transport);
                }
            }

            $this->_setDefaults('from');
            $this->_setDefaults('replyTo');
        }

        return $this->_transport;
    }

    /**
     * Set transport/message defaults
     *
     * @param  string $type
     * @return void
     */
    protected function _setDefaults($type)
    {
        $key = strtolower('default' . $type);
        $options = $this->getOptions();

        if(isset($options[$key]['email']) &&
           !is_numeric($options[$key]['email']))
        {
            $method = 'setDefault' . ucfirst($type);
            if(isset($options[$key]['name']) &&
               !is_numeric($options[$key]['name']))
            {
                \Zend\Mail\Mail::$method($options[$key]['email'], $options[$key]['name']);
            } else {
                \Zend\Mail\Mail::$method($options[$key]['email']);
            }
        }
    }

    /**
     * Setup mail transport
     *
     * @param  array $options
     * @return void
     */
    protected function _setupTransport(array $options)
    {
    	if(!isset($options['type'])) {
    		$options['type'] = 'sendmail';
    	}

        $transportName = ucfirst($options['type']);
        if (!class_exists($options['type'])) {
            $qualifiedTransportName = 'Zend\Mail\Transport\\' . $transportName;
            if (!class_exists($qualifiedTransportName)) {
                throw new Exception\InitializationException(
                    "Specified Mail Transport '{$transportName}' could not be found"
                );
            }
            $transportName = $qualifiedTransportName;
        }

        unset($options['type']);

        switch($transportName) {
            case 'Zend\Mail\Transport\Smtp':
                if(!isset($options['host'])) {
                    throw new Exception\InitializationException(
                        'A host is necessary for smtp transport,'
                        .' but none was given');
                }

                $transport = new $transportName($options['host'], $options);
                break;
            case 'Zend\Mail\Transport\Sendmail':
            default:
                $transport = new $transportName($options);
                break;
        }

        return $transport;
    }
}
