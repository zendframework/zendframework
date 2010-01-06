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
 * Resource for setting translation options
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Resource_Translate extends Zend_Application_Resource_ResourceAbstract
{
    const DEFAULT_REGISTRY_KEY = 'Zend_Translate';

    /**
     * @var Zend_Translate
     */
    protected $_translate;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Translate
     */
    public function init()
    {
        return $this->getTranslate();
    }

    /**
     * Retrieve translate object
     *
     * @return Zend_Translate
     * @throws Zend_Application_Resource_Exception if registry key was used
     *          already but is no instance of Zend_Translate
     */
    public function getTranslate()
    {
        if (null === $this->_translate) {
            $options = $this->getOptions();

            if (!isset($options['data'])) {
                throw new Zend_Application_Resource_Exception('No translation source data provided.');
            }

            $adapter = isset($options['adapter']) ? $options['adapter'] : Zend_Translate::AN_ARRAY;
            $locale  = isset($options['locale'])  ? $options['locale']  : null;
            $translateOptions = isset($options['options']) ? $options['options'] : array();

            $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
                 ? $options['registry_key']
                 : self::DEFAULT_REGISTRY_KEY;

            if(Zend_Registry::isRegistered($key)) {
                $translate = Zend_Registry::get($key);
                if(!$translate instanceof Zend_Translate) {
                    require_once 'Zend/Application/Resource/Exception.php';
                    throw new Zend_Application_Resource_Exception($key
                                   . ' already registered in registry but is '
                                   . 'no instance of Zend_Translate');
                }

                $translate->addTranslation($options['data'], $locale, $options);
                $this->_translate = $translate;
            } else {
                $this->_translate = new Zend_Translate(
                    $adapter, $options['data'], $locale, $translateOptions
                );

                Zend_Registry::set($key, $this->_translate);
            }
        }

        return $this->_translate;
    }
}
