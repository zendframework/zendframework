<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\I18n\View\Helper;

use Locale;
use Zend\I18n\Exception;
use Zend\I18n\Translator\Plural\Rule as PluralRule;
use Zend\View\Helper\AbstractHelper;

/**
 * Helper for rendering text based on a count number (like the I18n plural translation helper, but when translation
 * is not needed).
 *
 * Please note that we did not write any hard-coded rules for languages, as languages can evolve, we prefered to
 * let the developer define the rules himself, instead of potentially break applications if we change rules in the
 * future.
 *
 * However, you can find most of the up-to-date plural rules for most languages in those links:
 *      - http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html
 *      - https://developer.mozilla.org/en-US/docs/Localization_and_Plurals
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage View
 */
class Plural extends AbstractHelper
{
    /**
     * Locale to use instead of the default
     *
     * @var string
     */
    protected $locale;

    /**
     * Associative array that associate a locale to a plural rule
     *
     * @var array
     */
    protected $rules;


    /**
     * Set locale to use instead of the default.
     *
     * @param  string $locale
     * @return DateFormat
     */
    public function setLocale($locale)
    {
        $this->locale = (string) $locale;
        return $this;
    }

    /**
     * Get the locale to use.
     *
     * @return string|null
     */
    public function getLocale()
    {
        if ($this->locale === null) {
            $this->locale = Locale::getDefault();
        }

        return $this->locale;
    }

    /**
     * Add a plural rule for a specific locale. If a rule already exists for this locale, it is overriden
     *
     * @param  string            $locale
     * @param  PluralRule|string $pluralRule
     * @return self
     */
    public function addPluralRule($locale, $pluralRule)
    {
        if (is_string($pluralRule)) {
            $pluralRule = PluralRule::fromString($pluralRule);
        }

        $this->rules[str_replace('-', '_', $locale)] = $pluralRule;

        return $this;
    }

    /**
     * Get the plural rule for the given locale. It first checks the complete locale for an exact match, if
     * none found, it tries to find the plural rule only using the language code
     *
     * @param  string $locale
     * @throws Exception\InvalidArgumentException
     * @return PluralRule
     */
    public function getPluralRule($locale)
    {
        $locale = str_replace('-', '_', $locale);

        if (isset($this->rules[$locale])) {
            return $this->rules[$locale];
        }

        $language = strtok($locale, '_');

        if (isset($this->rules[$language])) {
            return $this->rules[$language];
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'No plural rule was found for the %s locale',
            $locale
        ));
    }

    /**
     * This function checks if there is a rule for this locale. It makes a strict comparison, so if the locale
     * contains both a lang and region, the registered locale must have both
     *
     * @param string $locale
     * @return bool
     */
    public function hasPluralRule($locale)
    {
        $locale = str_replace('-', '_', $locale);

        return isset($this->rules[$locale]);
    }

    /**
     * Given an array of strings, a number and, if wanted, an optional locale (the default one is used
     * otherwise), this picks the right string according to plural rules of the locale
     *
     * @param  array|string $strings
     * @param  int|float    $number
     * @param  string       $locale
     * @throws Exception\OutOfBoundsException
     * @return string
     */
    public function __invoke($strings, $number, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $rule        = $this->getPluralRule($locale);
        $pluralIndex = $rule->evaluate($number);

        if (!is_array($strings)) {
            $strings = (array) $strings;
        }

        return $strings[$pluralIndex];
    }
}

