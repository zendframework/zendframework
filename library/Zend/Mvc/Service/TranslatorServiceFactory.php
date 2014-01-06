<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\I18n\Translator\Translator;
use Zend\Mvc\I18n\DummyTranslator;
use Zend\Mvc\I18n\Translator as MvcTranslator;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Overrides the translator factory from the i18n component in order to
 * replace it with the bridge class from this namespace.
 */
class TranslatorServiceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator 
     * @return MvcTranslator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator->has('Zend\I18n\Translator\TranslatorInterface')) {
            return new MvcTranslator($serviceLocator->get('Zend\I18n\Translator\TranslatorInterface'));
        }

        if ($serviceLocator->has('Config')) {
            $config = $serviceLocator->get('Config');
            if (isset($config['translator']) && !empty($config['translator'])) {
                return new MvcTranslator(Translator::factory($config['translator']));
            }
        }

        return new MvcTranslator(new DummyTranslator());
    }
}
