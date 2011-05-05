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
 * @category  Zend
 * @package   Zend_Measure
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Measure;
use Zend\Locale;
use Zend\Locale\Math;

/**
 * Class for handling number conversions
 *
 * This class can only handle numbers without precision
 *
 * @uses       Zend\Locale
 * @uses       Zend\Locale\Math
 * @category   Zend
 * @package    Zend_Measure
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Number extends AbstractMeasure
{
    const STANDARD = 'DECIMAL';

    const BINARY      = 'BINARY';
    const TERNARY     = 'TERNARY';
    const QUATERNARY  = 'QUATERNARY';
    const QUINARY     = 'QUINARY';
    const SENARY      = 'SENARY';
    const SEPTENARY   = 'SEPTENARY';
    const OCTAL       = 'OCTAL';
    const NONARY      = 'NONARY';
    const DECIMAL     = 'DECIMAL';
    const DUODECIMAL  = 'DUODECIMAL';
    const HEXADECIMAL = 'HEXADECIMAL';
    const ROMAN       = 'ROMAN';

    /**
     * Calculations for all number units
     *
     * @var array
     */
    protected $_units = array(
        'BINARY'      => array(2,  '⑵'),
        'TERNARY'     => array(3,  '⑶'),
        'QUATERNARY'  => array(4,  '⑷'),
        'QUINARY'     => array(5,  '⑸'),
        'SENARY'      => array(6,  '⑹'),
        'SEPTENARY'   => array(7,  '⑺'),
        'OCTAL'       => array(8,  '⑻'),
        'NONARY'      => array(9,  '⑼'),
        'DECIMAL'     => array(10, '⑽'),
        'DUODECIMAL'  => array(12, '⑿'),
        'HEXADECIMAL' => array(16, '⒃'),
        'ROMAN'       => array(99, ''),
        'STANDARD'    => 'DECIMAL'
    );

    /**
     * Definition of all roman signs
     *
     * @var array $_roman
     */
    private static $_roman = array(
        'I' => 1,
        'A' => 4,
        'V' => 5,
        'B' => 9,
        'X' => 10,
        'E' => 40,
        'L' => 50,
        'F' => 90,
        'C' => 100,
        'G' => 400,
        'D' => 500,
        'H' => 900,
        'M' => 1000,
        'J' => 4000,
        'P' => 5000,
        'K' => 9000,
        'Q' => 10000,
        'N' => 40000,
        'R' => 50000,
        'W' => 90000,
        'S' => 100000,
        'Y' => 400000,
        'T' => 500000,
        'Z' => 900000,
        'U' => 1000000
    );

    /**
     * Convertion table for roman signs
     *
     * @var array $_romanconvert
     */
    private static $_romanconvert = array(
        '/_V/' => '/P/',
        '/_X/' => '/Q/',
        '/_L/' => '/R/',
        '/_C/' => '/S/',
        '/_D/' => '/T/',
        '/_M/' => '/U/',
        '/IV/' => '/A/',
        '/IX/' => '/B/',
        '/XL/' => '/E/',
        '/XC/' => '/F/',
        '/CD/' => '/G/',
        '/CM/' => '/H/',
        '/M_V/'=> '/J/',
        '/MQ/' => '/K/',
        '/QR/' => '/N/',
        '/QS/' => '/W/',
        '/ST/' => '/Y/',
        '/SU/' => '/Z/'
    );

    /**
     * Zend\Measure\AbstractMeasure is an abstract class for the different measurement types
     *
     * @param  integer                   $value  Value
     * @param  string                    $type   (Optional) A Zend\Measure\Number Type
     * @param  string|Zend\Locale\Locale $locale (Optional) A Zend\Locale\Locale
     * @throws Zend\Measure\Exception When language is unknown
     * @throws Zend\Measure\Exception When type is unknown
     */
    public function __construct($value, $type, $locale = null)
    {
        if (($type !== null) and (Locale\Locale::isLocale($type))) {
            $locale = $type;
            $type = null;
        }

        if ($locale === null) {
            $locale = new Locale\Locale();
        }

        if (!Locale\Locale::isLocale($locale, true)) {
            if (!Locale\Locale::isLocale($locale, false)) {
                throw new Exception("Language (" . (string) $locale . ") is unknown");
            }

            $locale = new Locale\Locale($locale);
        }

        $this->_locale = (string) $locale;

        if ($type === null) {
            $type = $this->_units['STANDARD'];
        }

        if (isset($this->_units[$type]) === false) {
            throw new Exception("Type ($type) is unknown");
        }

        $this->setValue($value, $type, $this->_locale);
    }

    /**
     * Set a new value
     *
     * @param  integer                   $value  Value
     * @param  string                    $type   (Optional) A Zend\Measure\Number Type
     * @param  string|Zend\Locale\Locale $locale (Optional) A Zend\Locale\Locale Type
     * @throws Zend\Measure\Exception
     */
    public function setValue($value, $type = null, $locale = null)
    {
        if (empty($locale)) {
            $locale = $this->_locale;
        }

        if (empty($this->_units[$type])) {
            throw new Exception('unknown type of number:' . $type);
        }

        switch($type) {
            case 'BINARY':
                preg_match('/[01]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;

            case 'TERNARY':
                preg_match('/[012]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;

            case 'QUATERNARY':
                preg_match('/[0123]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;

            case 'QUINARY':
                preg_match('/[01234]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;

            case 'SENARY':
                preg_match('/[012345]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;

            case 'SEPTENARY':
                preg_match('/[0123456]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;

            case 'OCTAL':
                preg_match('/[01234567]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;

            case 'NONARY':
                preg_match('/[012345678]+/', $value, $ergebnis);
                $value = $ergebnis[0];
                break;

            case 'DUODECIMAL':
                preg_match('/[0123456789AB]+/', strtoupper($value), $ergebnis);
                $value = $ergebnis[0];
                break;

            case 'HEXADECIMAL':
                preg_match('/[0123456789ABCDEF]+/', strtoupper($value), $ergebnis);
                $value = $ergebnis[0];
                break;

            case 'ROMAN':
                preg_match('/[IVXLCDM_]+/', strtoupper($value), $ergebnis);
                $value = $ergebnis[0];
                break;

            default:
                try {
                    $value = Locale\Format::getInteger($value, array('locale' => $locale));
                } catch (\Exception $e) {
                    throw new Exception($e->getMessage(), $e->getCode(), $e);
                }
                if (call_user_func(Math::$comp, $value, 0) < 0) {
                    $value = call_user_func(Math::$sqrt, call_user_func(Math::$pow, $value, 2));
                }
                break;
        }

        $this->_value = $value;
        $this->_type  = $type;
    }

    /**
     * Convert input to decimal value string
     *
     * @param  integer $input Input string
     * @param  string  $type  Type from which to convert to decimal
     * @return string
     */
    private function _toDecimal($input, $type)
    {
        $value = '';
        // Convert base xx values
        if ($this->_units[$type][0] <= 16) {
            $split  = str_split($input);
            $length = strlen($input);
            for ($x = 0; $x < $length; ++$x) {
                $split[$x] = hexdec($split[$x]);
                $value     = call_user_func(Math::$add, $value,
                            call_user_func(Math::$mul, $split[$x],
                            call_user_func(Math::$pow, $this->_units[$type][0], ($length - $x - 1))));
            }
        }

        // Convert roman numbers
        if ($type === 'ROMAN') {
            $input = strtoupper($input);
            $input = preg_replace(array_keys(self::$_romanconvert), array_values(self::$_romanconvert), $input);

            $split = preg_split('//', strrev($input), -1, PREG_SPLIT_NO_EMPTY);

            for ($x =0; $x < count($split); $x++) {
                if ($split[$x] == '/') {
                    continue;
                }

                $num = self::$_roman[$split[$x]];
                if (($x > 0 and ($split[$x-1] != '/') and ($num < self::$_roman[$split[$x-1]]))) {
                    $num -= $num;
                }

                $value += $num;
            }

            str_replace('/', '', $value);
        }

        return $value;
    }

    /**
     * Convert input to type value string
     *
     * @param  integer $value Input string
     * @param  string  $type  Type to convert to
     * @return string
     * @throws Zend\Measure\Exception When more than 200 digits are calculated
     */
    private function _fromDecimal($value, $type)
    {
        $tempvalue = $value;
        if ($this->_units[$type][0] <= 16) {
            $newvalue = '';
            $count    = 200;
            $base     = $this->_units[$type][0];

            while (call_user_func(Math::$comp, $value, 0, 25) <> 0) {
                $target = call_user_func(Math::$mod, $value, $base);

                $newvalue = strtoupper(dechex($target)) . $newvalue;

                $value = call_user_func(Math::$sub, $value, $target, 0);
                $value = call_user_func(Math::$div, $value, $base, 0);

                --$count;
                if ($count === 0) {
                    throw new Exception("Your value '$tempvalue' cannot be processed because it extends 200 digits");
                }
            }

            if ($newvalue === '') {
                $newvalue = '0';
            }
        }

        if ($type === 'ROMAN') {
            $i        = 0;
            $newvalue = '';
            $romanval = array_values(array_reverse(self::$_roman));
            $romankey = array_keys(array_reverse(self::$_roman));
            $count    = 200;
            while (call_user_func(Math::$comp, $value, 0, 25) <> 0) {
                while ($value >= $romanval[$i]) {
                    $value    -= $romanval[$i];
                    $newvalue .= $romankey[$i];

                    if ($value < 1) {
                        break;
                    }

                    --$count;
                    if ($count === 0) {
                        throw new Exception("Your value '$tempvalue' cannot be processed because it extends 200 digits");
                    }
                }

                $i++;
            }

            $newvalue = str_replace('/', '', preg_replace(array_values(self::$_romanconvert), array_keys(self::$_romanconvert), $newvalue));
        }

        return $newvalue;
    }

    /**
     * Set a new type, and convert the value
     *
     * @param  string $type New type to set
     * @throws Zend\Measure\Exception When a unknown type is given
     * @return void
     */
    public function setType($type)
    {
        if (empty($this->_units[$type]) === true) {
            throw new Exception('Unknown type of number:' . $type);
        }

        $value = $this->_toDecimal($this->getValue(-1), $this->getType(-1));
        $value = $this->_fromDecimal($value, $type);

        $this->_value = $value;
        $this->_type  = $type;
    }

    /**
     * Alias function for setType returning the converted unit
     * Default is 0 as this class only handles numbers without precision
     *
     * @param  string  $type  Type to convert to
     * @param  integer $round (Optional) Precision to add, will always be 0
     * @return string
     */
    public function convertTo($type, $round = 0, $locale = null)
    {
        $this->setType($type);
        return $this->toString($round, $locale);
    }
}
