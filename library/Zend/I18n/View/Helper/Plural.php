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
use Zend\View\Helper\AbstractHelper;

/**
 * Helper for helping rendering text based on a count number (like the i18n plural translation helper, but
 * when translation is not needed). This helper uses different pre-defined plural forms in order to choose
 * the right string. Some references:
 *      http://unicode.org/repos/cldr-tmp/trunk/diff/supplemental/language_plural_rules.html
 *      https://developer.mozilla.org/en-US/docs/Localization_and_Plurals
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
     * List of languages code (eventually a full locale code) associated to a plural form
     *
     * @var array
     */
    protected $pluralForms = array(
        // Plural form n°0 (families: Asian, Persian, Turkic/Altaic, Thai, Lao...)
        'bo'    => 0,
        'dz'    => 0,
        'id'    => 0,
        'ja'    => 0,
        'jv'    => 0,
        'ka'    => 0,
        'km'    => 0,
        'kn'    => 0,
        'ko'    => 0,
        'ms'    => 0,
        'th'    => 0,
        'tr'    => 0,
        'vi'    => 0,
        'zh'    => 0,

        // Plural form n°1 (families: Germanic, Semitic, Romanic...)
        'af'    => 1,
        'az'    => 1,
        'bn'    => 1,
        'bg'    => 1,
        'ca'    => 1,
        'da'    => 1,
        'de'    => 1,
        'el'    => 1,
        'en'    => 1,
        'eo'    => 1,
        'es'    => 1,
        'et'    => 1,
        'eu'    => 1,
        'fa'    => 1,
        'fi'    => 1,
        'fo'    => 1,
        'fur'   => 1,
        'fy'    => 1,
        'gl'    => 1,
        'gu'    => 1,
        'ha'    => 1,
        'he'    => 1,
        'hu'    => 1,
        'it'    => 1,
        'ku'    => 1,
        'lb'    => 1,
        'ml'    => 1,
        'mn'    => 1,
        'mr'    => 1,
        'nah'   => 1,
        'nb'    => 1,
        'ne'    => 1,
        'nl'    => 1,
        'nn'    => 1,
        'no'    => 1,
        'om'    => 1,
        'or'    => 1,
        'pa'    => 1,
        'pap'   => 1,
        'ps'    => 1,
        'pt'    => 1,
        'so'    => 1,
        'sq'    => 1,
        'sv'    => 1,
        'sw'    => 1,
        'ta'    => 1,
        'te'    => 1,
        'tk'    => 1,
        'ur'    => 1,
        'zu'    => 1,

        // Plural form n°2 (romanic: French, Brazilian Portuguese...)
        'am'    => 2,
        'bh'    => 2,
        'fil'   => 2,
        'fr'    => 2,
        'gun'   => 2,
        'hi'    => 2,
        'ln'    => 2,
        'mg'    => 2,
        'nso'   => 2,
        'xbr'   => 2,
        'ti'    => 2,
        'wa'    => 2,
        'pt_BR' => 2,

        // Plural form n°3 (baltic: Latvian)
        'lv'    => 3,

        // Plural form n°4 (celtic: Scottish Gaelic)
        'gd'    => 4,

        // Plural form n°5 (romanic: Romanian)
        'ro'    => 5,

        // Plural form n°6 (baltic: Lithunian)
        'lt'    => 6,

        // Plural form n°7 (slavic: Bosnian, Croatian, Serbian, Russian, Ukrainian)
        'be'    => 7,
        'bs'    => 7,
        'hr'    => 7,
        'ru'    => 7,
        'sr'    => 7,
        'uk'    => 7,

        // Plural form n°8 (slavic: Slovak, Czech)
        'cs'    => 8,
        'sk'    => 8,

        // Plural form n°9 (slavic: Polish)
        'pl'    => 9,

        // Plural form n°10 (slavic: Slovenian, Sorbian)
        'sl'    => 10,
        'sb'    => 10,

        // Plural form n°11 (gaelic: Irish Gaelic)
        'gd_IE' => 11,

        // Plural form n°12 (semitic: Arab)
        'ar'    => 12,

        // Plural form n°13 (semitic: Maltese)
        'mt'    => 13,

        // Plural form n°14 (slavic: Macedonian)
        'mk'    => 14,

        // Plural form n°15 (Icelandic)
        'is'    => 15,

        // Plural form n°16 (celtic: Breton)
        'br'    => 16
    );


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
        if (is_string($strings)) {
            $strings = (array) $strings;
        }

        if ($locale === null) {
            $locale = $this->getLocale();
        }

        $pluralForm   = $this->getPluralForm($locale);
        $pluralNumber = $this->getPluralNumber($number, $pluralForm);

        if (!isset($strings[$pluralNumber])) {
            throw new Exception\OutOfBoundsException(sprintf(
                'The plural number %s for the locale %s has not been specified',
                $pluralNumber,
                $locale
            ));
        }

        return $strings[$pluralNumber];
    }

    /**
     * Locale to use to determine the plural form
     *
     * @param  string $locale
     * @throws Exception\InvalidArgumentException
     * @return string
     */
    protected function getPluralForm($locale)
    {
        $locale = strtolower(str_replace('-', '_', $locale));

        // Note that we first check the complete locale code, as some countries have different way to handle plurals,
        // for instance Brazilian Portuguese handles it differently from normal Portuguese

        if (isset($this->pluralForms[$locale])) {
            return $this->pluralForms[$locale];
        }

        $languageCode = strtok($locale, '_');

        if (isset($this->pluralForms[$languageCode])) {
            return $this->pluralForms[$languageCode];
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plural form could not be found for locale %s. Please check if this locale exists',
            $locale
        ));
    }

    /**
     * Get the plural number
     *
     * @param  int    $number
     * @param  string $pluralForm
     * @throws Exception\RuntimeException
     * @return int
     */
    protected function getPluralNumber($number, $pluralForm)
    {
        switch($pluralForm) {
            case 0:
                return 0;
            case 1:
                return ($number == 1) ? 0 : 1;
            case 2:
                return (($number == 0) || ($number == 1)) ? 0 : 1;
            case 3:
                return ($number == 0) ? 0 : ((($number % 10 == 1) && ($number % 100 != 11)) ? 1 : 2);
            case 4:
                return (($number == 1 || $number == 11) ? 0 : (($number == 2 || $number == 12) ? 1 : ((($number > 2 && $number < 11)) || ($number > 12 && $number < 20)) ? 2 : 3));
            case 5:
                return ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 0) && ($number % 100 < 20))) ? 1 : 2);
            case 6:
                return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);
            case 7:
                return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);
            case 8:
                return ($number == 1) ? 0 : ((($number >= 2) && ($number <= 4)) ? 1 : 2);
            case 9:
                return ($number == 1) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 12) || ($number % 100 > 14))) ? 1 : 2);
            case 10:
                return ($number % 100 == 1) ? 0 : (($number % 100 == 2) ? 1 : ((($number % 100 == 3) || ($number % 100 == 4)) ? 2 : 3));
            case 11:
                return ($number == 1) ? 0 : ($number == 2) ? 1 : ($number > 2 && $number < 7) ? 2 : ($number > 6 && $number < 11) ? 3 : 4;
            case 12:
                return ($number == 0) ? 0 : (($number == 1) ? 1 : (($number == 2) ? 2 : ((($number >= 3) && ($number <= 10)) ? 3 : ((($number >= 11) && ($number <= 99)) ? 4 : 5))));
            case 13:
                return ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 1) && ($number % 100 < 11))) ? 1 : ((($number % 100 > 10) && ($number % 100 < 20)) ? 2 : 3));
            case 14:
                return ($number % 10 == 1) ? 0 : 1;
            case 15:
                return (($number % 10) == 1 && $number != 11) ? 0 : 1;
            case 16:
                // Breton is just crazy to write (especially with ternaries). If someone want to do it... ;-) Here are the rules :
                /**
                 * n == 1    => 0
                 * n mod 10 is 1 and n mod 100 not in 11,71,91  => 1;
                 * mod 10 is 2 and n mod 100 not in 12,72,92  => 2;
                 * mod 10 in 3..4,9 and n mod 100 not in 10..19,70..79,90..99  => 3;
                 * n mod 1000000 is 0 and n is not 0  => 4
                 * everything else  => 5
                 */
                throw new Exception\RuntimeException('Breton is not implemented yet');
            default:
                return 0;
        }
    }
}

