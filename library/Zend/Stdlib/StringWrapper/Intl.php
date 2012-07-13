<?php

namespace Zend\Stdlib\StringWrapper;

class Intl extends AbstractStringWrapper
{

    /**
     * List of supported character sets (upper case)
     *
     * @var string[]
     */
    protected $charsets = array('UTF-8');

    /**
     * Constructor
     *
     * @throws Exception\ExtensionNotLoadedException
     */
    public function __construct()
    {
        if (!extension_loaded('intl')) {
            throw new Exception\ExtensionNotLoadedException(
                'PHP extension "intl" is required for this wrapper'
            );
        }
    }

    public function strlen($str, $charset = 'UTF-8')
    {
        if (strcasecmp($charset, 'UTF-8') != 0) {
            trigger_error("Character set '{$charset}' not supported by intl");
            return false;
        }

        return grapheme_strlen($str);
    }

    public function substr($str, $offset = 0, $length = null, $charset = 'UTF-8')
    {
        if (strcasecmp($charset, 'UTF-8') != 0) {
            trigger_error("Character set '{$charset}' not supported by intl");
            return false;
        }

        return grapheme_substr($str, $offset, $length);
    }

    public function strpos($haystack, $needle, $offset = 0, $charset = 'UTF-8')
    {
        if (strcasecmp($charset, 'UTF-8') != 0) {
            trigger_error("Character set '{$charset}' not supported by intl");
            return false;
        }

        return grapheme_strpos($haystack, $needle, $offset);
    }

    public function convert($str, $toCharset, $fromCharset = 'UTF-8')
    {
        if (strcasecmp($toCharset, $fromCharset) != 0) {
            trigger_error("Can't convert '{$fromCharset}' to '{$toCharset}' using intl", E_WARNING);
            return false;
        }

        return true;
    }
}
