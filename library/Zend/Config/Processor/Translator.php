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
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Config\Processor;

use Zend\Config\Config,
    Zend\Config\Exception\InvalidArgumentException,
    Zend\Translator\Translator as ZendTranslator,
    Zend\Locale\Locale,
    Traversable,
    ArrayObject;

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Translator implements ProcessorInterface
{
    /**
     * @var ZendTranslator
     */
    protected $translator;

    /**
     * @var Locale|string|null
     */
    protected $locale = null;

    /**
     * Translator uses the supplied Zend\Translator\Translator to find and
     * translate language strings in config.
     *
     * @param  ZendTranslator $translator
     * @param  Locale|string|null $locale
     * @return ZendTranslator
     */
    public function __construct(ZendTranslator $translator, $locale = null)
    {
        $this->setTranslator($translator);
        $this->setLocale($locale);
    }

    /**
     * @return ZendTranslator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param ZendTranslator $translator
     */
    public function setTranslator(ZendTranslator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @return Locale|string|null
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param Locale|string|null $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * Process
     *
     * @param Config $config
     * @return Config
     * @throws InvalidArgumentException
     */
    public function process(Config $config)
    {
        if ($config->isReadOnly()) {
            throw new InvalidArgumentException('Cannot parse config because it is read-only');
        }

        /**
         * Walk through config and replace values
         */
        foreach ($config as $key => $val) {
            if ($val instanceof Config) {
                $this->process($val);
            } else {
                $config->$key = $this->translator->translate($val, $this->locale);
            }
        }

        return $config;
    }

    /**
     * Process a single value
     *
     * @param $value
     * @return mixed
     */
    public function processValue($value)
    {
        return $this->translator->translate($value, $this->locale);
    }

}
