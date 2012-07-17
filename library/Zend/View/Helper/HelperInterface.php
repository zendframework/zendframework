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
use Zend\View\Renderer\RendererInterface as Renderer;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
interface HelperInterface
{
    /**
     * Set the View object
     *
     * @param  Renderer $view
     * @return HelperInterface
     */
    public function setView(Renderer $view);

    /**
     * Get the View object
     *
     * @return Renderer
     */
    public function getView();

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
    public function setTranslator(Translator $translator, $textDomain);

    /**
     * Returns translator used in helper
     *
     * @return Translator|null
     */
    public function getTranslator();

    /**
     * Checks if the helper has a translator
     *
     * @return bool
     */
    public function hasTranslator();

    /**
     * Sets whether translator is enabled and should be used
     *
     * @param  bool $enabled [optional] whether translator should be used.
     *                       Default is true.
     * @return AbstractHelper
     */
    public function setTranslatorEnabled($enabled);

    /**
     * Returns whether translator is enabled and should be used
     *
     * @return bool
     */
    public function isTranslatorEnabled();

    /**
     * Set translation text domain
     *
     * @param  string $textDomain
     * @return AbstractValidator
     */
    public function setTranslatorTextDomain($textDomain);

    /**
     * Return the translation text domain
     *
     * @return string
     */
    public function getTranslatorTextDomain();

}
