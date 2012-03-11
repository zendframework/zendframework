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
 * @package    Zend_Escaper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Escaper;

use Zend\Escaper\Exception;

/**
 * Context specific methods for use in secure output escaping
 *
 * @package    Zend_Escaper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Escaper
{

    protected $encoding = 'UTF-8';

    protected $htmlSpecialCharsFlags = ENT_QUOTES;

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
            $this->encoding = $encoding;
        }
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $this->htmlSpecialCharsFlags = ENT_QUOTES | ENT_SUBSTITUTE;
        }
    }

    public function getEncoding()
    {
        return $this->encoding;
    }

    public function escapeHtml($string)
    {
        $result = htmlspecialchars($string, $this->htmlSpecialCharsFlags, $this->getEncoding());
        return $result;
    }

    public function escapeJs($string)
    {
        $string = $this->toUtf8($string);
        if (strlen($string) == 0 || ctype_digit($string)) {
            return $string;
        }
        $result = preg_replace_callback(
            '/[^a-zA-Z0-9,\._]/Su',
            function ($matches) {
                $chr = $matches[0];
                if (strlen($chr) == 1) {
                    return sprintf('\\x%s', strtoupper(substr('00' . bin2hex($chr), -2)));
                } else {
                    $chr = $this->convertEncoding($chr, 'UTF-16BE', 'UTF-8');
                    return sprintf('\\u%s', strtoupper(substr('0000' . bin2hex($chr), -4)));
                }
            },
            $string
        );
        return $this->fromUtf8($result);
    }

    public function escapeUrl($string)
    {
        return rawurlencode($string);
    }

    public function escapeCss($string)
    {
        $string = $this->toUtf8($string);
        if (strlen($string) == 0 || ctype_digit($string)) {
            return $string;
        }
        $result = preg_replace_callback(
            '/[^a-zA-Z0-9]/Su',
            function ($matches) {
                $chr = $matches[0];
                if (strlen($chr) == 1) {
                    $hex = ltrim(strtoupper(bin2hex($chr)), '0');
                    if (strlen($hex) == 0) $hex = '0';
                    return sprintf('\\%s ', $hex);
                } else {
                    $chr = $this->convertEncoding($chr, 'UTF-16BE', 'UTF-8');
                    return sprintf('\\%s ', ltrim(strtoupper(bin2hex($chr)), '0'));
                }
            },
            $string
        );
        return $this->fromUtf8($result);
    }

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

    protected function fromUtf8($string)
    {
        if ($this->getEncoding() === 'utf-8') {
            return $string;
        }
        return $this->convertEncoding($string, $this->getEncoding(), 'UTF-8');
    }

    protected function isUtf8($string)
    {
        if (strlen($string) == 0) {
            return true;
        } elseif (preg_match('/^./su', $string) == 1) {
            return true;
        }
        return false;
    }

    protected function convertEncoding($string, $to, $from)
    {
        if (function_exists('iconv')) {
            return iconv($from, $to, $string);
        } elseif (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($string, $to, $from);
        }
        throw new Exception\RuntimeException(
            get_called_class()
            . ' requires either the iconv or mbstring extension to be installed'
            . ' when escaping for non UTF-8 strings.'
        );
    }
}