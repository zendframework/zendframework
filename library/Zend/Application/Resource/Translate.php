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
 * @namespace
 */
namespace Zend\Application\Resource;

use Zend\Application\ResourceException,
    Zend\Registry,
    Zend\Translator\Translator;

/**
 * Resource for setting translation options
 *
 * @uses       \Zend\Application\ResourceException
 * @uses       \Zend\Application\Resource\AbstractResource
 * @uses       \Zend\Registry
 * @uses       \Zend\Translator\Translator
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Translate extends AbstractResource
{
    const DEFAULT_REGISTRY_KEY = 'Zend_Translate';

    /**
     * @var \Zend\Translator\Translator
     */
    protected $_translate;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return \Zend\Translator\Translator
     */
    public function init()
    {
        return $this->getTranslate();
    }

    /**
     * Retrieve translate object
     *
     * @return \Zend\Translator\Translator
     * @throws \Zend\Application\ResourceException if registry key was used
     *          already but is no instance of Zend_Translate
     */
    public function getTranslate()
    {
        if (null === $this->_translate) {
            $options = $this->getOptions();

            if (!isset($options['data'])) {
                throw new ResourceException('No translation source data provided.');
            }

            $adapter = isset($options['adapter']) ? $options['adapter'] : Translator::AN_ARRAY;
            $locale  = isset($options['locale'])  ? $options['locale']  : null;
            $translateOptions = isset($options['options']) ? $options['options'] : array();

            $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
                 ? $options['registry_key']
                 : self::DEFAULT_REGISTRY_KEY;

            if(Registry::isRegistered($key)) {
                $translate = Registry::get($key);
                if(!$translate instanceof Translator) {
                    throw new ResourceException($key
                                   . ' already registered in registry but is '
                                   . 'no instance of Zend_Translate');
                }

                $translate->addTranslation($options['data'], $locale, $options);
                $this->_translate = $translate;
            } else {
                $this->_translate = new Translator(
                    $adapter, $options['data'], $locale, $translateOptions
                );

                Registry::set($key, $this->_translate);
            }
        }

        return $this->_translate;
    }
}
