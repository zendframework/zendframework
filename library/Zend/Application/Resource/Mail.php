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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Resource for setting up Mail Transport and default From & ReplyTo addresses
 *
 * @uses       Zend_Application_Resource_Exception
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @uses       Zend_Loader_Autoloader
 * @uses       Zend_Mail
 * @uses       Zend_Mail_Transport_Sendmail
 * @uses       Zend_Mail_Transport_Smtp
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Resource_Mail extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * @var Zend_Mail_Transport_Abstract
     */
    protected $_transport;

    /**
     * Initialize mail resource
     * 
     * @return Zend_Mail_Transport_Abstract
     */
    public function init() 
    {
        return $this->getMail();
    }
    
    /**
     * @return Zend_Mail_Transport_Abstract|null
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
                    Zend_Mail::setDefaultTransport($this->_transport);
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
            $method = array('Zend_Mail', 'setDefault' . ucfirst($type));
            if(isset($options[$key]['name']) &&
               !is_numeric($options[$key]['name']))
            {
                call_user_func($method, $options[$key]['email'],
                                        $options[$key]['name']);
            } else {
                call_user_func($method, $options[$key]['email']);
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
    	
        $transportName = $options['type'];
        if(!Zend_Loader_Autoloader::autoload($transportName)) {
            $transportName = ucfirst(strtolower($transportName));

            if(!Zend_Loader_Autoloader::autoload($transportName)) {
                $transportName = 'Zend_Mail_Transport_' . $transportName;
                if(!Zend_Loader_Autoloader::autoload($transportName)) {
                    throw new Zend_Application_Resource_Exception(
                        "Specified Mail Transport '{$transportName}'"
                        . 'could not be found'
                    );
                }
            }
        }
        
        unset($options['type']);
        
        switch($transportName) {
            case 'Zend_Mail_Transport_Smtp':
                if(!isset($options['host'])) {
                    throw new Zend_Application_Resource_Exception(
                        'A host is necessary for smtp transport,'
                        .' but none was given');
                }
                
                $transport = new $transportName($options['host'], $options);
                break;
            case 'Zend_Mail_Transport_Sendmail':
            default:
                $transport = new $transportName($options);
                break;
        }

        return $transport;
    }
}
