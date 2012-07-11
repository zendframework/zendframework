<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Escaper
 */

namespace Zend\Escaper;

use Zend\Escaper\Exception;

/**
 * Context specific methods for use in secure output escaping
 *
 * @package    Zend_Escaper
 */
class Escaper
{

    /**
     * Current encoding for escaping. If not UTF-8, we convert strings from this encoding
     * pre-escaping and back to this encoding post-escaping.
     * 
     * @var string
     */
    protected $encoding = 'utf-8';

    /**
     * Holds the value of the special flags passed as second parameter to
     * htmlspecialchars(). We modify these for PHP 5.4 to take advantage
     * of the new ENT_SUBSTITUTE flag for correctly dealing with invalid
     * UTF-8 sequences.
     * 
     * @var string
     */
    protected $htmlSpecialCharsFlags = ENT_QUOTES;

    /**
     * Static Matcher which escapes characters for HTML Attribute contexts
     * 
     * @var Closure
     */
    protected $htmlAttrMatcher = null;

    /**
     * Static Matcher which escapes characters for Javascript contexts
     * 
     * @var Closure
     */
    protected $jsMatcher = null;

    /**
     * Static Matcher which escapes characters for CSS Attribute contexts
     * 
     * @var Closure
     */
    protected $cssMatcher = null;

    /**
     * List of all encoding supported by this class
     * 
     * @var array
     */
    protected $supportedEncodings = array(
        'iso-8859-1',   'iso8859-1',    'iso-8859-5',   'iso8859-5',
        'iso-8859-15',  'iso8859-15',   'utf-8',        'cp866',
        'ibm866',       '866',          'cp1251',       'windows-1251',
        'win-1251',     '1251',         'cp1252',       'windows-1252',
        '1252',         'koi8-r',       'koi8-ru',      'koi8r',
        'big5',         '950',          'gb2312',       '936',
        'big5-hkscs',   'shift_jis',    'sjis',         'sjis-win',
        'cp932',        '932',          'euc-jp',       'eucjp',
        'eucjp-win',    'macroman'
    );

    /**
     * Constructor: Single parameter allows setting of global encoding for use by
     * the current object. If PHP 5.4 is detected, additional ENT_SUBSTITUTE flag
     * is set for htmlspecialchars() calls.
     * 
     * @param string $encoding
     */
    public function __construct($encoding = null)
    {
        if (!is_null($encoding)) {
            if (empty($encoding)) {
                throw new Exception\InvalidArgumentException(
                    get_called_class() . ' constructor parameter does not allow a NULL or '
                    . 'blank string value'
                );
            }
            if (!in_array(strtolower($encoding), $this->supportedEncodings)) {
                throw new Exception\InvalidArgumentException(
                    'Value of \'' . $encoding . '\' passed to ' . get_called_class()
                    . ' constructor parameter is invalid. Provide an encoding supported by htmlspecialchars()'
                );
            }
            $this->encoding = strtolower($encoding);
        }
        if (version_compare(PHP_VERSION, '5.4') >= 0) {
            $this->htmlSpecialCharsFlags = ENT_QUOTES | ENT_SUBSTITUTE;
        }
    }

    /**
     * Return the encoding that all output/input is expected to be encoded in.
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Escape a string for the HTML Body context where there are very few characters
     * of special meaning. Internally this will use htmlspecialchars().
     * 
     * @param string $string
     * @return string
     */
    public function escapeHtml($string)
    {
        $result = htmlspecialchars($string, $this->htmlSpecialCharsFlags, $this->encoding);
        return $result;
    }

    /**
     * Escape a string for the HTML Attribute context. We use an extended set of characters
     * to escape that are not covered by htmlspecialchars() to cover cases where an attribute
     * might be unquoted or quoted illegally (e.g. backticks are valid quotes for IE).
     * 
     * @param string $string
     * @return string
     */
    public function escapeHtmlAttr($string)
    {
        $string = $this->toUtf8($string);
        if (strlen($string) == 0 || ctype_digit($string)) {
            return $string;
        }
        $result = preg_replace_callback(
            '/[^a-zA-Z0-9,\.\-_]/Su',
            array($this, 'htmlAttrMatcher'),
            $string
        );
        return $this->fromUtf8($result);
    }

    /**
     * Escape a string for the Javascript context. This does not use json_encode(). An extended
     * set of characters are escaped beyond ECMAScript's rules for Javascript literal string
     * escaping in order to prevent misinterpretation of Javascript as HTML leading to the
     * injection of special characters and entities. The escaping used should be tolerant
     * of cases where HTML escaping was not applied on top of Javascript escaping correctly.
     * Backslash escaping is not used as it still leaves the escaped character as-is and so
     * is not useful in a HTML context.
     * 
     * @param string $string
     * @return string
     */
    public function escapeJs($string)
    {
        $string = $this->toUtf8($string);
        if (strlen($string) == 0 || ctype_digit($string)) {
            return $string;
        }
        $result = preg_replace_callback(
            '/[^a-zA-Z0-9,\._]/Su',
            array($this, 'jsMatcher'),
            $string
        );
        return $this->fromUtf8($result);
    }

    /**
     * Escape a string for the URI or Parameter contexts. This should not be used to escape
     * an entire URI - only a subcomponent being inserted. The function is a simple proxy
     * to rawurlencode() which now implements RFC 3986 since PHP 5.3 completely.
     * 
     * @param string $string
     * @return string
     */
    public function escapeUrl($string)
    {
        return rawurlencode($string);
    }

    /**
     * Escape a string for the CSS context. CSS escaping can be applied to any string being
     * inserted into CSS and escapes everything except alphanumerics.
     * 
     * @param string $string
     * @return string
     */
    public function escapeCss($string)
    {
        $string = $this->toUtf8($string);
        if (strlen($string) == 0 || ctype_digit($string)) {
            return $string;
        }
        $result = preg_replace_callback(
            '/[^a-zA-Z0-9]/Su',
            array($this, 'cssMatcher'),
            $string
        );
        return $this->fromUtf8($result);
    }

    /**
     * Callback function for preg_replace_callback that applies HTML Attribute
     * escaping to all matches.
     * 
     * @param array $matches
     * @return string
     */
    public function htmlAttrMatcher($matches)
    {
        $chr = $matches[0];
        $ord = ord($chr);
        /**
         * The following replaces characters undefined in HTML with the
         * hex entity for the Unicode replacement character.
         */
        if (($ord <= 0x1f && $chr != "\t" && $chr != "\n" && $chr != "\r")
        || ($ord >= 0x7f && $ord <= 0x9f)) {
            return '&#xFFFD;';
        }
        /**
         * Check if the current character to escape has a name entity we should
         * replace it with while grabbing the hex value of the character.
         */
        if (strlen($chr) == 1) {
            $hex = strtoupper(substr('00' . bin2hex($chr), -2));
        } else {
            $chr = $this->convertEncoding($chr, 'UTF-16BE', 'UTF-8');
            $hex = strtoupper(substr('0000' . bin2hex($chr), -4));
        }
        $int = hexdec($hex);
        if (array_key_exists($int, $this->htmlNamedEntityMap)) {
            return sprintf('&%s;', $this->htmlNamedEntityMap[$int]);
        }
        /**
         * Per OWASP recommendations, we'll use hex entities for any other
         * characters where a named entity does not exist.
         */
        return sprintf('&#x%s;', $hex);
    }

    /**
     * Callback function for preg_replace_callback that applies Javascript
     * escaping to all matches.
     * 
     * @param array $matches
     * @return string
     */
    public function jsMatcher($matches)
    {
        $chr = $matches[0];
        if (strlen($chr) == 1) {
            return sprintf('\\x%s', strtoupper(substr('00' . bin2hex($chr), -2)));
        } else {
            $chr = $this->convertEncoding($chr, 'UTF-16BE', 'UTF-8');
            return sprintf('\\u%s', strtoupper(substr('0000' . bin2hex($chr), -4)));
        }
    }

    /**
     * Callback function for preg_replace_callback that applies CSS
     * escaping to all matches.
     * 
     * @param array $matches
     * @return string
     */
    public function cssMatcher($matches)
    {
        $chr = $matches[0];
        if (strlen($chr) == 1) {
            $hex = ltrim(strtoupper(bin2hex($chr)), '0');
            if (strlen($hex) == 0) $hex = '0';
            return sprintf('\\%s ', $hex);
        } else {
            $chr = $this->convertEncoding($chr, 'UTF-16BE', 'UTF-8');
            return sprintf('\\%s ', ltrim(strtoupper(bin2hex($chr)), '0'));
        }
    }

    /**
     * Converts a string to UTF-8 from the base encoding. The base encoding is set via this
     * class' constructor.
     * 
     * @param string $string
     * @return string
     */
    protected function toUtf8($string)
    {
        if ($this->getEncoding() === 'utf-8') {
            $result = $string;
        } else {
            $result = $this->convertEncoding($string, 'UTF-8', $this->getEncoding());
        }
        if (!$this->isUtf8($result)) {
            throw new Exception\RuntimeException(sprintf(
                'String to be escaped was not valid UTF-8 or could not be converted: %s', $result
            ));
        }
        return $result;
    }

    /**
     * Converts a string from UTF-8 to the base encoding. The base encoding is set via this
     * class' constructor.
     * @param string $string
     * @return string
     */
    protected function fromUtf8($string)
    {
        if ($this->getEncoding() === 'utf-8') {
            return $string;
        }
        return $this->convertEncoding($string, $this->getEncoding(), 'UTF-8');
    }

    /**
     * Checks if a given string appears to be valid UTF-8 or not.
     * 
     * @param string $string
     * @return bool
     */
    protected function isUtf8($string)
    {
        if (strlen($string) == 0) {
            return true;
        } elseif (preg_match('/^./su', $string) == 1) {
            return true;
        }
        return false;
    }

    /**
     * Encoding conversion helper which wraps iconv and mbstring where they exist or throws
     * and exception where neither is available.
     * 
     * @param string $string
     * @return string
     */
    protected function convertEncoding($string, $to, $from)
    {
        $result = '';
        if (function_exists('iconv')) {
            $result = iconv($from, $to, $string);
        } elseif (function_exists('mb_convert_encoding')) {
            $result = mb_convert_encoding($string, $to, $from);
        } else {
            throw new Exception\RuntimeException(
                get_called_class()
                . ' requires either the iconv or mbstring extension to be installed'
                . ' when escaping for non UTF-8 strings.'
            );
        }
        if ($result === false) {
            return ''; // return non-fatal blank string on encoding errors from users
        } else {
            return $result;
        }
    }

    /**
     * Entity Map mapping Unicode codepoints to any available named HTML entities.
     *
     * While HTML supports far more named entities, the lowest common denominator
     * has become HTML5's XML Serialisation which is restricted to the those named
     * entities that XML supports. Using HTML entities would result in this error:
     *     XML Parsing Error: undefined entity
     * 
     * @var array
     */
    protected $htmlNamedEntityMap = array(
        34 => 'quot',         /* quotation mark */
        38 => 'amp',          /* ampersand */
        60 => 'lt',           /* less-than sign */
        62 => 'gt',           /* greater-than sign */
    );
}
