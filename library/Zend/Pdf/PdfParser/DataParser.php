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
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\PdfParser;
use Zend\Pdf\Exception;
use Zend\Pdf;
use Zend\Pdf\InternalType;
use Zend\Pdf\InternalType\IndirectObjectReference;
use Zend\Pdf\ObjectFactory;

/**
 * PDF string parser
 *
 * @uses       \Zend\Pdf\InternalType
 * @uses       \Zend\Pdf\InternalType\IndirectObjectReference
 * @uses       \Zend\Pdf\ObjectFactory
 * @uses       \Zend\Pdf\Exception
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DataParser
{
    /**
     * Source PDF
     *
     * @var string
     */
    public $data = '';

    /**
     * Current position in a data
     *
     * @var integer
     */
    public $offset = 0;

    /**
     * Current reference context
     *
     * @var \Zend\Pdf\InternalType\IndirectObjectReference\Context
     */
    private $_context = null;

    /**
     * Array of elements of the currently parsed object/trailer
     *
     * @var array
     */
    private $_elements = array();

    /**
     * PDF objects factory.
     *
     * @var \Zend\Pdf\ObjectFactory
     */
    private $_objFactory = null;


    /**
     * Clean up resources.
     *
     * Clear current state to remove cyclic object references
     */
    public function cleanUp()
    {
        $this->_context = null;
        $this->_elements = array();
        $this->_objFactory = null;
    }

    /**
     * Character with code $chCode is white space
     *
     * @param integer $chCode
     * @return boolean
     */
    public static function isWhiteSpace($chCode)
    {
        if ($chCode == 0x00 || // null character
            $chCode == 0x09 || // Tab
            $chCode == 0x0A || // Line feed
            $chCode == 0x0C || // Form Feed
            $chCode == 0x0D || // Carriage return
            $chCode == 0x20    // Space
           ) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Character with code $chCode is a delimiter character
     *
     * @param integer $chCode
     * @return boolean
     */
    public static function isDelimiter($chCode )
    {
        if ($chCode == 0x28 || // '('
            $chCode == 0x29 || // ')'
            $chCode == 0x3C || // '<'
            $chCode == 0x3E || // '>'
            $chCode == 0x5B || // '['
            $chCode == 0x5D || // ']'
            $chCode == 0x7B || // '{'
            $chCode == 0x7D || // '}'
            $chCode == 0x2F || // '/'
            $chCode == 0x25    // '%'
           ) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Skip white space
     *
     * @param boolean $skipComment
     */
    public function skipWhiteSpace($skipComment = true)
    {
        if ($skipComment) {
            while (true) {
                $this->offset += strspn($this->data, "\x00\t\n\f\r ", $this->offset);

                if ($this->offset < strlen($this->data)  &&  $this->data[$this->offset] == '%') {
                    // Skip comment
                    $this->offset += strcspn($this->data, "\r\n", $this->offset);
                } else {
                    // Non white space character not equal to '%' is found
                    return;
                }
            }
        } else {
            $this->offset += strspn($this->data, "\x00\t\n\f\r ", $this->offset);
        }

//        /** Original (non-optimized) implementation. */
//
//        while ($this->offset < strlen($this->data)) {
//            if (strpos("\x00\t\n\f\r ", $this->data[$this->offset]) !== false) {
//                $this->offset++;
//            } else if (ord($this->data[$this->offset]) == 0x25 && $skipComment) { // '%'
//                $this->skipComment();
//            } else {
//                return;
//            }
//        }
    }


    /**
     * Skip comment
     */
    public function skipComment()
    {
        while ($this->offset < strlen($this->data))
        {
            if (ord($this->data[$this->offset]) != 0x0A || // Line feed
                ord($this->data[$this->offset]) != 0x0d    // Carriage return
               ) {
                $this->offset++;
            } else {
                return;
            }
        }
    }


    /**
     * Read comment line
     *
     * @return string
     */
    public function readComment()
    {
        $this->skipWhiteSpace(false);

        /** Check if it's a comment line */
        if ($this->data[$this->offset] != '%') {
            return '';
        }

        for ($start = $this->offset;
             $this->offset < strlen($this->data);
             $this->offset++) {
            if (ord($this->data[$this->offset]) == 0x0A || // Line feed
                ord($this->data[$this->offset]) == 0x0d    // Carriage return
               ) {
                break;
            }
        }

        return substr($this->data, $start, $this->offset-$start);
    }


    /**
     * Returns next lexeme from a pdf stream
     *
     * @return string
     */
    public function readLexeme()
    {
        // $this->skipWhiteSpace();
        while (true) {
            $this->offset += strspn($this->data, "\x00\t\n\f\r ", $this->offset);

            if ($this->offset < strlen($this->data)  &&  $this->data[$this->offset] == '%') {
                $this->offset += strcspn($this->data, "\r\n", $this->offset);
            } else {
                break;
            }
        }

        if ($this->offset >= strlen($this->data)) {
            return '';
        }

        if ( /* self::isDelimiter( ord($this->data[$start]) ) */
             strpos('()<>[]{}/%', $this->data[$this->offset]) !== false ) {

            switch (substr($this->data, $this->offset, 2)) {
                case '<<':
                    $this->offset += 2;
                    return '<<';
                    break;

                case '>>':
                    $this->offset += 2;
                    return '>>';
                    break;

                default:
                    return $this->data[$this->offset++];
                    break;
            }
        } else {
            $start = $this->offset;
            $this->offset += strcspn($this->data, "()<>[]{}/%\x00\t\n\f\r ", $this->offset);

            return substr($this->data, $start, $this->offset - $start);
        }
    }


    /**
     * Read elemental object from a PDF stream
     *
     * @return \Zend\Pdf\InternalType\AbstractTypeObject
     * @throws \Zend\Pdf\Exception
     */
    public function readElement($nextLexeme = null)
    {
        if ($nextLexeme === null) {
            $nextLexeme = $this->readLexeme();
        }

        /**
         * Note: readElement() method is a public method and could be invoked from other classes.
         * We should care about _elements member management only if readElement() is invoked
         * by self::getObject() method.
         */
        switch ($nextLexeme) {
            case '(':
                return ($this->_elements[] = $this->_readString());

            case '<':
                return ($this->_elements[] = $this->_readBinaryString());

            case '/':
                return ($this->_elements[] = new InternalType\NameObject(
                                                    InternalType\NameObject::unescape( $this->readLexeme() )
                                                                      ));

            case '[':
                return ($this->_elements[] = $this->_readArray());

            case '<<':
                return ($this->_elements[] = $this->_readDictionary());

            case ')':
                // fall through to next case
            case '>':
                // fall through to next case
            case ']':
                // fall through to next case
            case '>>':
                // fall through to next case
            case '{':
                // fall through to next case
            case '}':
                throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Offset - 0x%X.',
                                                $this->offset));

            default:
                if (strcasecmp($nextLexeme, 'true') == 0) {
                    return ($this->_elements[] = new InternalType\BooleanObject(true));
                } else if (strcasecmp($nextLexeme, 'false') == 0) {
                    return ($this->_elements[] = new InternalType\BooleanObject(false));
                } else if (strcasecmp($nextLexeme, 'null') == 0) {
                    return ($this->_elements[] = new InternalType\NullObject());
                }

                $ref = $this->_readReference($nextLexeme);
                if ($ref !== null) {
                    return ($this->_elements[] = $ref);
                }

                return ($this->_elements[] = $this->_readNumeric($nextLexeme));
        }
    }


    /**
     * Read string PDF object
     * Also reads trailing ')' from a pdf stream
     *
     * @return \Zend\Pdf\InternalType\StringObject
     * @throws \Zend\Pdf\Exception
     */
    private function _readString()
    {
        $start = $this->offset;
        $openedBrackets = 1;

        $this->offset += strcspn($this->data, '()\\', $this->offset);

        while ($this->offset < strlen($this->data)) {
            switch (ord( $this->data[$this->offset] )) {
                case 0x28: // '(' - opened bracket in the string, needs balanced pair.
                    $this->offset++;
                    $openedBrackets++;
                    break;

                case 0x29: // ')' - pair to the opened bracket
                    $this->offset++;
                    $openedBrackets--;
                    break;

                case 0x5C: // '\\' - escape sequence, skip next char from a check
                    $this->offset += 2;
            }

            if ($openedBrackets == 0) {
                break; // end of string
            }

            $this->offset += strcspn($this->data, '()\\', $this->offset);
        }
        if ($openedBrackets != 0) {
            throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Unexpected end of file while string reading. Offset - 0x%X. \')\' expected.', $start));
        }

        return new InternalType\StringObject(InternalType\StringObject::unescape( substr($this->data,
                                                                                     $start,
                                                                                     $this->offset - $start - 1) ));
    }


    /**
     * Read binary string PDF object
     * Also reads trailing '>' from a pdf stream
     *
     * @return \Zend\Pdf\InternalType\BinaryStringObject
     * @throws \Zend\Pdf\Exception
     */
    private function _readBinaryString()
    {
        $start = $this->offset;

        $this->offset += strspn($this->data, "\x00\t\n\f\r 0123456789abcdefABCDEF", $this->offset);

        if ($this->offset >= strlen($this->data) - 1) {
            throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Unexpected end of file while reading binary string. Offset - 0x%X. \'>\' expected.', $start));
        }

        if ($this->data[$this->offset++] != '>') {
            throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Unexpected character while binary string reading. Offset - 0x%X.', $this->offset));
        }

        return new InternalType\BinaryStringObject(
                       InternalType\BinaryStringObject::unescape( substr($this->data,
                                                                        $start,
                                                                        $this->offset - $start - 1) ));
    }


    /**
     * Read array PDF object
     * Also reads trailing ']' from a pdf stream
     *
     * @return \Zend\Pdf\InternalType\ArrayObject
     * @throws \Zend\Pdf\Exception
     */
    private function _readArray()
    {
        $elements = array();

        while ( strlen($nextLexeme = $this->readLexeme()) != 0 ) {
            if ($nextLexeme != ']') {
                $elements[] = $this->readElement($nextLexeme);
            } else {
                return new InternalType\ArrayObject($elements);
            }
        }

        throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Unexpected end of file while array reading. Offset - 0x%X. \']\' expected.', $this->offset));
    }


    /**
     * Read dictionary PDF object
     * Also reads trailing '>>' from a pdf stream
     *
     * @return \Zend\Pdf\InternalType\DictionaryObject
     * @throws \Zend\Pdf\Exception
     */
    private function _readDictionary()
    {
        $dictionary = new InternalType\DictionaryObject();

        while ( strlen($nextLexeme = $this->readLexeme()) != 0 ) {
            if ($nextLexeme != '>>') {
                $nameStart = $this->offset - strlen($nextLexeme);

                $name  = $this->readElement($nextLexeme);
                $value = $this->readElement();

                if (!$name instanceof InternalType\NameObject) {
                    throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Name object expected while dictionary reading. Offset - 0x%X.', $nameStart));
                }

                $dictionary->add($name, $value);
            } else {
                return $dictionary;
            }
        }

        throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Unexpected end of file while dictionary reading. Offset - 0x%X. \'>>\' expected.', $this->offset));
    }


    /**
     * Read reference PDF object
     *
     * @param string $nextLexeme
     * @return \Zend\Pdf\InternalType\IndirectObjectReference
     */
    private function _readReference($nextLexeme = null)
    {
        $start = $this->offset;

        if ($nextLexeme === null) {
            $objNum = $this->readLexeme();
        } else {
            $objNum = $nextLexeme;
        }
        if (!ctype_digit($objNum)) { // it's not a reference
            $this->offset = $start;
            return null;
        }

        $genNum = $this->readLexeme();
        if (!ctype_digit($genNum)) { // it's not a reference
            $this->offset = $start;
            return null;
        }

        $rMark  = $this->readLexeme();
        if ($rMark != 'R') { // it's not a reference
            $this->offset = $start;
            return null;
        }

        $ref = new IndirectObjectReference((int)$objNum, (int)$genNum, $this->_context, $this->_objFactory);

        return $ref;
    }


    /**
     * Read numeric PDF object
     *
     * @param string $nextLexeme
     * @return \Zend\Pdf\InternalType\NumericObject
     */
    private function _readNumeric($nextLexeme = null)
    {
        if ($nextLexeme === null) {
            $nextLexeme = $this->readLexeme();
        }

        return new InternalType\NumericObject($nextLexeme);
    }


    /**
     * Read inderect object from a PDF stream
     *
     * @param integer $offset
     * @param \Zend\Pdf\InternalType\IndirectObjectReference\Context $context
     * @return \Zend\Pdf\InternalType\IndirectObject
     */
    public function getObject($offset, IndirectObjectReference\Context $context)
    {
        if ($offset === null ) {
            return new InternalType\NullObject();
        }

        // Save current offset to make getObject() reentrant
        $offsetSave = $this->offset;

        $this->offset    = $offset;
        $this->_context  = $context;
        $this->_elements = array();

        $objNum = $this->readLexeme();
        if (!ctype_digit($objNum)) {
            throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Offset - 0x%X. Object number expected.', $this->offset - strlen($objNum)));
        }

        $genNum = $this->readLexeme();
        if (!ctype_digit($genNum)) {
            throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Offset - 0x%X. Object generation number expected.', $this->offset - strlen($genNum)));
        }

        $objKeyword = $this->readLexeme();
        if ($objKeyword != 'obj') {
            throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Offset - 0x%X. \'obj\' keyword expected.', $this->offset - strlen($objKeyword)));
        }

        $objValue = $this->readElement();

        $nextLexeme = $this->readLexeme();

        if( $nextLexeme == 'endobj' ) {
            /**
             * Object is not generated by factory (thus it's not marked as modified object).
             * But factory is assigned to the obect.
             */
            $obj = new InternalType\IndirectObject($objValue, (int)$objNum, (int)$genNum, $this->_objFactory);

            foreach ($this->_elements as $element) {
                $element->setParentObject($obj);
            }

            // Restore offset value
            $this->offset = $offsetSave;

            return $obj;
        }

        /**
         * It's a stream object
         */
        if ($nextLexeme != 'stream') {
            throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Offset - 0x%X. \'endobj\' or \'stream\' keywords expected.', $this->offset - strlen($nextLexeme)));
        }

        if (!$objValue instanceof InternalType\DictionaryObject) {
            throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Offset - 0x%X. Stream extent must be preceded by stream dictionary.', $this->offset - strlen($nextLexeme)));
        }

        /**
         * References are automatically dereferenced at this moment.
         */
        $streamLength = $objValue->Length->value;

        /**
         * 'stream' keyword must be followed by either cr-lf sequence or lf character only.
         * This restriction gives the possibility to recognize all cases exactly
         */
        if ($this->data[$this->offset] == "\r" &&
            $this->data[$this->offset + 1] == "\n"    ) {
            $this->offset += 2;
        } else if ($this->data[$this->offset] == "\n"    ) {
            $this->offset++;
        } else {
            throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Offset - 0x%X. \'stream\' must be followed by either cr-lf sequence or lf character only.', $this->offset - strlen($nextLexeme)));
        }

        $dataOffset = $this->offset;

        $this->offset += $streamLength;

        $nextLexeme = $this->readLexeme();
        if ($nextLexeme != 'endstream') {
            throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Offset - 0x%X. \'endstream\' keyword expected.', $this->offset - strlen($nextLexeme)));
        }

        $nextLexeme = $this->readLexeme();
        if ($nextLexeme != 'endobj') {
            throw new Exception\CorruptedPdfException(sprintf('PDF file syntax error. Offset - 0x%X. \'endobj\' keyword expected.', $this->offset - strlen($nextLexeme)));
        }

        $obj = new InternalType\StreamObject(substr($this->data,
                                                         $dataOffset,
                                                         $streamLength),
                                             (int)$objNum,
                                             (int)$genNum,
                                             $this->_objFactory,
                                             $objValue);

        foreach ($this->_elements as $element) {
            $element->setParentObject($obj);
        }

        // Restore offset value
        $this->offset = $offsetSave;

        return $obj;
    }


    /**
     * Get length of source string
     *
     * @return integer
     */
    public function getLength()
    {
        return strlen($this->data);
    }

    /**
     * Get source string
     *
     * @return string
     */
    public function getString()
    {
        return $this->data;
    }


    /**
     * Parse integer value from a binary stream
     *
     * @param string $stream
     * @param integer $offset
     * @param integer $size
     * @return integer
     */
    public static function parseIntFromStream($stream, $offset, $size)
    {
        $value = 0;
        for ($count = 0; $count < $size; $count++) {
            $value *= 256;
            $value += ord($stream[$offset + $count]);
        }

        return $value;
    }



    /**
     * Set current context
     *
     * @param \Zend\Pdf\InternalType\IndirectObjectReference\Context $context
     */
    public function setContext(IndirectObjectReference\Context $context)
    {
        $this->_context = $context;
    }

    /**
     * Object constructor
     *
     * Note: PHP duplicates string, which is sent by value, only of it's updated.
     * Thus we don't need to care about overhead
     *
     * @param string $pdfString
     * @param \Zend\Pdf\ObjectFactory $factory
     */
    public function __construct($source, ObjectFactory $factory)
    {
        $this->data         = $source;
        $this->_objFactory  = $factory;
    }
}
