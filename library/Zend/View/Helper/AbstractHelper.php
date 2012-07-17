<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper;

use Zend\I18n\Translator\Translator;
use Zend\View\Helper\HelperInterface;
use Zend\View\Renderer\RendererInterface as Renderer;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
abstract class AbstractHelper implements HelperInterface
{
    /**
     * View object
     *
     * @var Renderer
     */
    protected $view = null;

    /**
     * Translator (optional)
     *
     * @var Translator
     */
    protected $translator;

    /**
     * Translator text domain (optional)
     *
     * @var string
     */
    protected $translatorTextDomain;

    /**
     * Whether translator should be used
     *
     * @var bool
     */
    protected $translatorEnabled = true;

    /**
     * Default translation object for all validate objects
     * @var Translator
     */
    protected static $defaultTranslator;

    /**
     * Default text domain to be used with translator
     * @var string
     */
    protected static $defaultTranslatorTextDomain = 'default';

    /**
     * Set the View object
     *
     * @param  Renderer $view
     * @return AbstractHelper
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Get the view object
     *
     * @return null|Renderer
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Sets translator to use in helper
     *
     * Implements {@link HelperInterface::setTranslator()}.
     *
     * @param  mixed $translator  [optional] translator.  Expects an object of
     *                            type {@link Translator\Adapter\AbstractAdapter}
     *                            or {@link Translator\Translator}, or null.
     *                            Default is null, which sets no translator.
     * @param  string $textDomain
     * @return AbstractHelper
     */
    public function setTranslator(Translator $translator = null, $textDomain = null)
    {
        $this->translator = $translator;
        if (null !== $textDomain) {
            $this->setTranslatorTextDomain($textDomain);
        }
        return $this;
    }

    /**
     * Returns translator used in helper
     *
     * @return Translator|null
     */
    public function getTranslator()
    {
        if (! $this->isTranslatorEnabled()) {
            return null;
        }

        if (null === $this->translator) {
            $this->translator = self::getDefaultTranslator();
        }

        return $this->translator;
    }

    /**
     * Checks if the helper has a translator
     *
     * @return bool
     */
    public function hasTranslator()
    {
        return (bool) $this->getTranslator();
    }

    /**
     * Sets whether translator is enabled and should be used
     *
     * @param  bool $enabled [optional] whether translator should be used.
     *                       Default is true.
     * @return AbstractHelper
     */
    public function setTranslatorEnabled($enabled = true)
    {
        $this->translatorEnabled = (bool) $enabled;
        return $this;
    }

    /**
     * Returns whether translator is enabled and should be used
     *
     * @return bool
     */
    public function isTranslatorEnabled()
    {
        return $this->translatorEnabled;
    }

    /**
     * Set translation text domain
     *
     * @param  string $textDomain
     * @return AbstractValidator
     */
    public function setTranslatorTextDomain($textDomain = 'default')
    {
        $this->translatorTextDomain = $textDomain;
        return $this;
    }

    /**
     * Return the translation text domain
     *
     * @return string
     */
    public function getTranslatorTextDomain()
    {
        if (null === $this->translatorTextDomain) {
            $this->translatorTextDomain = self::getDefaultTranslatorTextDomain();
        }
        return $this->translatorTextDomain;
    }

    /**
     * Set default translation object for all validate objects
     *
     * @param  Translator|null $translator
     * @param  string          $textDomain (optional)
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public static function setDefaultTranslator(
        Translator $translator = null, $textDomain = null
    ) {
        self::$defaultTranslator = $translator;
        if (null !== $textDomain) {
            self::setDefaultTranslatorTextDomain($textDomain);
        }
    }

    /**
     * Get default translation object for all validate objects
     *
     * @return Translator|null
     */
    public static function getDefaultTranslator()
    {
        return self::$defaultTranslator;
    }

    /**
     * Is there a default translation object set?
     *
     * @return boolean
     */
    public static function hasDefaultTranslator()
    {
        return (bool) self::$defaultTranslator;
    }

    /**
     * Set default translation text domain for all validate objects
     *
     * @param  string $textDomain
     * @return void
     */
    public static function setDefaultTranslatorTextDomain($textDomain = 'default')
    {
        self::$defaultTranslatorTextDomain = $textDomain;
    }

    /**
     * Get default translation text domain for all validate objects
     *
     * @return string
     */
    public static function getDefaultTranslatorTextDomain()
    {
        return self::$defaultTranslatorTextDomain;
    }

}
