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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Application\Resource;

use Zend\Registry,
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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
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
                throw new Exception\InitializationException('No translation source data provided.');
            }

            if (empty($options['adapter'])) {
                $options['adapter'] = Translator::AN_ARRAY;
            }

            if (!empty($options['data'])) {
                $options['content'] = $options['data'];
                unset($options['data']);
            }

            if (isset($options['options'])) {
                foreach($options['options'] as $key => $value) {
                    $options[$key] = $value;
                }
            }

            if (!empty($options['cache']) && is_string($options['cache'])) {
                $bootstrap = $this->getBootstrap();
                if ($bootstrap instanceof \Zend\Application\ResourceBootstrapper &&
                    $bootstrap->hasPluginResource('CacheManager')
                ) {
                    $cacheManager = $bootstrap->bootstrap('CacheManager')
                        ->getResource('CacheManager');
                    if (null !== $cacheManager &&
                        $cacheManager->hasCache($options['cache'])
                    ) {
                        $options['cache'] = $cacheManager->getCache($options['cache']);
                    }
                }
            }

            $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
                 ? $options['registry_key']
                 : self::DEFAULT_REGISTRY_KEY;
            unset($options['registry_key']);

            if(Registry::isRegistered($key)) {
                $translate = Registry::get($key);
                if(!$translate instanceof Translator) {
                    throw new Exception\InitializationException($key
                                   . ' already registered in registry but is '
                                   . 'no instance of Zend_Translate');
                }

                $translate->addTranslation($options);
                $this->_translate = $translate;
            } else {
                $this->_translate = new Translator($options);
                Registry::set($key, $this->_translate);
            }
        }

        return $this->_translate;
    }
}
