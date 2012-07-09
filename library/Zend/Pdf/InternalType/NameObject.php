<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\InternalType;

use Zend\Pdf;
use Zend\Pdf\Exception;

/**
 * PDF file 'name' element implementation
 *
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class NameObject extends AbstractTypeObject
{
    /**
     * Object value
     *
     * @var string
     */
    public $value;


    /**
     * Object constructor
     *
     * @param string $val
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function __construct($val)
    {
        settype($val, 'string');
        if (strpos($val,"\x00") !== false) {
            throw new Exception\RuntimeException('Null character is not allowed in PDF Names');
        }
        $this->value   = (string)$val;
    }


    /**
     * Return type of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return AbstractTypeObject::TYPE_NAME;
    }


    /**
     * Escape string according to the PDF rules
     *
     * @param string $inStr
     * @return string
     */
    public static function escape($inStr)
    {
        $outStr = '';

        /** @todo switch implementation to strspn() usage!!!!!!! */
        for ($count = 0; $count < strlen($inStr); $count++) {
            $nextCode = ord($inStr[$count]);

            switch ($inStr[$count]) {
                case '(':
                // fall through to next case
                case ')':
                // fall through to next case
                case '<':
                // fall through to next case
                case '>':
                // fall through to next case
                case '[':
                // fall through to next case
                case ']':
                // fall through to next case
                case '{':
                // fall through to next case
                case '}':
                // fall through to next case
                case '/':
                // fall through to next case
                case '%':
                // fall through to next case
                case '\\':
                // fall through to next case
                case '#':
                    $outStr .= sprintf('#%02X', $nextCode);
                    break;

                default:
                    if ($nextCode >= 33 && $nextCode <= 126 ) {
                        // Visible ASCII symbol
                        $outStr .= $inStr[$count];
                    } else {
                        $outStr .= sprintf('#%02X', $nextCode);
                    }
            }
        }

        return $outStr;
    }


    /**
     * Unescape string according to the PDF rules
     *
     * @param string $inStr
     * @return string
     */
    public static function unescape($inStr)
    {
        $outStr = '';

        /** @todo switch implementation to strspn() usage!!!!!!! */
        for ($count = 0; $count < strlen($inStr); $count++) {
            if ($inStr[$count] != '#' )  {
                $outStr .= $inStr[$count];
            } else {
                // Escape sequence
                $outStr .= chr(base_convert(substr($inStr, $count+1, 2), 16, 10 ));
                $count +=2;
            }
        }
        return $outStr;
    }


    /**
     * Return object as string
     *
     * @param \Zend\Pdf\ObjectFactory $factory
     * @return string
     */
    public function toString(Pdf\ObjectFactory $factory = null)
    {
        return '/' . self::escape((string)$this->value);
    }
}
