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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\InternalType;
use Zend\Pdf\Exception;
use Zend\Pdf\InternalType\StreamFilter\Compression as CompressionFilter;
use Zend\Pdf\ObjectFactory;
use Zend\Pdf;

/**
 * PDF file 'stream object' element implementation
 *
 * @uses       \Zend\Pdf\InternalType\AbstractTypeObject
 * @uses       \Zend\Pdf\InternalType\DictionaryObject
 * @uses       \Zend\Pdf\InternalType\NumericObject
 * @uses       \Zend\Pdf\InternalType\IndirectObject
 * @uses       \Zend\Pdf\InternalType\StreamContent
 * @uses       \Zend\Pdf\Exception
 * @uses       \Zend\Pdf\InternalType\StreamFilter
 * @uses       \Zend\Pdf\InternalType\StreamFilter\Compression;
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StreamObject extends IndirectObject
{
    /**
     * StreamObject dictionary
     * Required enries:
     * Length
     *
     * @var \Zend\Pdf\InternalType\DictionaryObject
     */
    private $_dictionary;

    /**
     * Flag which signals, that stream is decoded
     *
     * @var boolean
     */
    private $_streamDecoded;

    /**
     * Stored original stream object dictionary.
     * Used to decode stream at access time.
     *
     * The only properties affecting decoding are sored here.
     *
     * @var array|null
     */
    private $_initialDictionaryData = null;

    /**
     * Object constructor
     *
     * @param mixed $val
     * @param integer $objNum
     * @param integer $genNum
     * @param \Zend\Pdf\ObjectFactory $factory
     * @param \Zend\Pdf\InternalType\DictionaryObject|null $dictionary
     * @throws \Zend\Pdf\Exception
     */
    public function __construct($val, $objNum, $genNum, ObjectFactory $factory, $dictionary = null)
    {
        parent::__construct(new StreamContent($val), $objNum, $genNum, $factory);

        if ($dictionary === null) {
            $this->_dictionary    = new DictionaryObject();
            $this->_dictionary->Length = new NumericObject(strlen( $val ));
            $this->_streamDecoded = true;
        } else {
            $this->_dictionary    = $dictionary;
            $this->_streamDecoded = false;
        }
    }


    /**
     * Extract dictionary data which are used to store information and to normalize filters
     * information before defiltering.
     *
     * @return array
     */
    private function _extractDictionaryData()
    {
        $dictionaryArray = array();

        $dictionaryArray['Filter']      = array();
        $dictionaryArray['DecodeParms'] = array();
        if ($this->_dictionary->Filter === null) {
            // Do nothing.
        } else if ($this->_dictionary->Filter->getType() == AbstractTypeObject::TYPE_ARRAY) {
            foreach ($this->_dictionary->Filter->items as $id => $filter) {
                $dictionaryArray['Filter'][$id]      = $filter->value;
                $dictionaryArray['DecodeParms'][$id] = array();

                if ($this->_dictionary->DecodeParms !== null ) {
                    if ($this->_dictionary->DecodeParms->items[$id] !== null &&
                        $this->_dictionary->DecodeParms->items[$id]->value !== null ) {
                        foreach ($this->_dictionary->DecodeParms->items[$id]->getKeys() as $paramKey) {
                            $dictionaryArray['DecodeParms'][$id][$paramKey] =
                                  $this->_dictionary->DecodeParms->items[$id]->$paramKey->value;
                        }
                    }
                }
            }
        } else if ($this->_dictionary->Filter->getType() != AbstractTypeObject::TYPE_NULL) {
            $dictionaryArray['Filter'][0]      = $this->_dictionary->Filter->value;
            $dictionaryArray['DecodeParms'][0] = array();
            if ($this->_dictionary->DecodeParms !== null ) {
                foreach ($this->_dictionary->DecodeParms->getKeys() as $paramKey) {
                    $dictionaryArray['DecodeParms'][0][$paramKey] =
                          $this->_dictionary->DecodeParms->$paramKey->value;
                }
            }
        }

        if ($this->_dictionary->F !== null) {
            $dictionaryArray['F'] = $this->_dictionary->F->value;
        }

        $dictionaryArray['FFilter']      = array();
        $dictionaryArray['FDecodeParms'] = array();
        if ($this->_dictionary->FFilter === null) {
            // Do nothing.
        } else if ($this->_dictionary->FFilter->getType() == AbstractTypeObject::TYPE_ARRAY) {
            foreach ($this->_dictionary->FFilter->items as $id => $filter) {
                $dictionaryArray['FFilter'][$id]      = $filter->value;
                $dictionaryArray['FDecodeParms'][$id] = array();

                if ($this->_dictionary->FDecodeParms !== null ) {
                    if ($this->_dictionary->FDecodeParms->items[$id] !== null &&
                        $this->_dictionary->FDecodeParms->items[$id]->value !== null) {
                        foreach ($this->_dictionary->FDecodeParms->items[$id]->getKeys() as $paramKey) {
                            $dictionaryArray['FDecodeParms'][$id][$paramKey] =
                                  $this->_dictionary->FDecodeParms->items[$id]->items[$paramKey]->value;
                        }
                    }
                }
            }
        } else {
            $dictionaryArray['FFilter'][0]      = $this->_dictionary->FFilter->value;
            $dictionaryArray['FDecodeParms'][0] = array();
            if ($this->_dictionary->FDecodeParms !== null ) {
                foreach ($this->_dictionary->FDecodeParms->getKeys() as $paramKey) {
                    $dictionaryArray['FDecodeParms'][0][$paramKey] =
                          $this->_dictionary->FDecodeParms->items[$paramKey]->value;
                }
            }
        }

        return $dictionaryArray;
    }

    /**
     * Decode stream
     *
     * @throws \Zend\Pdf\Exception
     */
    private function _decodeStream()
    {
        if ($this->_initialDictionaryData === null) {
            $this->_initialDictionaryData = $this->_extractDictionaryData();
        }

        /**
         * All applied stream filters must be processed to decode stream.
         * If we don't recognize any of applied filetrs an exception should be thrown here
         */
        if (isset($this->_initialDictionaryData['F'])) {
            /** @todo Check, how external files can be processed. */
            throw new Exception\NotImplementedException('External filters are not supported now.');
        }

        foreach ($this->_initialDictionaryData['Filter'] as $id => $filterName ) {
            $valueRef = &$this->_value->value->getRef();
            $this->_value->value->touch();
            switch ($filterName) {
                case 'ASCIIHexDecode':
                    $valueRef = StreamFilter\AsciiHex::decode($valueRef);
                    break;

                case 'ASCII85Decode':
                    $valueRef = StreamFilter\Ascii85::decode($valueRef);
                    break;

                case 'FlateDecode':
                    $valueRef = CompressionFilter\Flate::decode($valueRef,
                                                                $this->_initialDictionaryData['DecodeParms'][$id]);
                    break;

                case 'LZWDecode':
                    $valueRef = CompressionFilter\Lzw::decode($valueRef,
                                                              $this->_initialDictionaryData['DecodeParms'][$id]);
                    break;

                case 'RunLengthDecode':
                    $valueRef = StreamFilter\RunLength::decode($valueRef);
                    break;

                default:
                    throw new Exception\CorruptedPdfException('Unknown stream filter: \'' . $filterName . '\'.');
            }
        }

        $this->_streamDecoded = true;
    }

    /**
     * Encode stream
     *
     * @throws \Zend\Pdf\Exception
     */
    private function _encodeStream()
    {
        /**
         * All applied stream filters must be processed to encode stream.
         * If we don't recognize any of applied filetrs an exception should be thrown here
         */
        if (isset($this->_initialDictionaryData['F'])) {
            /** @todo Check, how external files can be processed. */
            throw new Exception\NotImplementedException('External filters are not supported now.');
        }

        $filters = array_reverse($this->_initialDictionaryData['Filter'], true);

        foreach ($filters as $id => $filterName ) {
            $valueRef = &$this->_value->value->getRef();
            $this->_value->value->touch();
            switch ($filterName) {
                case 'ASCIIHexDecode':
                    $valueRef = StreamFilter\AsciiHex::encode($valueRef);
                    break;

                case 'ASCII85Decode':
                    $valueRef = StreamFilter\Ascii85::encode($valueRef);
                    break;

                case 'FlateDecode':
                    $valueRef = CompressionFilter\Flate::encode($valueRef,
                                                                $this->_initialDictionaryData['DecodeParms'][$id]);
                    break;

                case 'LZWDecode':
                    $valueRef = CompressionFilter\Lzw::encode($valueRef,
                                                              $this->_initialDictionaryData['DecodeParms'][$id]);
                    break;

                 case 'RunLengthDecode':
                    $valueRef = StreamFilter\RunLength::encode($valueRef);
                    break;

               default:
                    throw new Exception\CorruptedPdfException('Unknown stream filter: \'' . $filterName . '\'.');
            }
        }

        $this->_streamDecoded = false;
    }

    /**
     * Get handler
     *
     * @param string $property
     * @return mixed
     * @throws \Zend\Pdf\Exception
     */
    public function __get($property)
    {
        if ($property == 'dictionary') {
            /**
             * If stream is note decoded yet, then store original decoding options (do it only once).
             */
            if (( !$this->_streamDecoded ) && ($this->_initialDictionaryData === null)) {
                $this->_initialDictionaryData = $this->_extractDictionaryData();
            }

            return $this->_dictionary;
        }

        if ($property == 'value') {
            if (!$this->_streamDecoded) {
                $this->_decodeStream();
            }

            return $this->_value->value->getRef();
        }

        throw new Exception\RuntimeException('Unknown stream object property requested.');
    }


    /**
     * Set handler
     *
     * @param string $property
     * @param  mixed $value
     */
    public function __set($property, $value)
    {
        if ($property == 'value') {
            $valueRef = &$this->_value->value->getRef();
            $valueRef = $value;
            $this->_value->value->touch();

            $this->_streamDecoded = true;

            return;
        }

        throw new Exception\RuntimeException('Unknown stream object property: \'' . $property . '\'.');
    }


    /**
     * Treat stream data as already encoded
     */
    public function skipFilters()
    {
        $this->_streamDecoded = false;
    }


    /**
     * Call handler
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!$this->_streamDecoded) {
            $this->_decodeStream();
        }

        call_user_func_array(array($this->_value, $method), $args);
    }

    /**
     * Detach PDF object from the factory (if applicable), clone it and attach to new factory.
     *
     * @param \Zend\Pdf\ObjectFactory $factory  The factory to attach
     * @param array &$processed  List of already processed indirect objects, used to avoid objects duplication
     * @param integer $mode  Cloning mode (defines filter for objects cloning)
     * @returns \Zend\Pdf\InternalType\AbstractTypeObject
     */
    public function makeClone(ObjectFactory $factory, array &$processed, $mode)
    {
        $id = spl_object_hash($this);
        if (isset($processed[$id])) {
            // Do nothing if object is already processed
            // return it
            return $processed[$id];
        }

        $streamValue      = $this->_value;
        $streamDictionary = $this->_dictionary->makeClone($factory, $processed, $mode);

        // Make new empty instance of stream object and register it in $processed container
        $processed[$id] = $clonedObject = $factory->newStreamObject('');

        // Copy current object data and state
        $clonedObject->_dictionary            = $this->_dictionary->makeClone($factory, $processed, $mode);
        $clonedObject->_value                 = $this->_value->makeClone($factory, $processed, $mode);
        $clonedObject->_initialDictionaryData = $this->_initialDictionaryData;
        $clonedObject->_streamDecoded         = $this->_streamDecoded;

        return  $clonedObject;
    }

    /**
     * Dump object to a string to save within PDF file
     *
     * $factory parameter defines operation context.
     *
     * @param \Zend\Pdf\ObjectFactory $factory
     * @return string
     */
    public function dump(ObjectFactory $factory)
    {
        $shift = $factory->getEnumerationShift($this->_factory);

        if ($this->_streamDecoded) {
            $this->_initialDictionaryData = $this->_extractDictionaryData();
            $this->_encodeStream();
        } else if ($this->_initialDictionaryData != null) {
            $newDictionary   = $this->_extractDictionaryData();

            if ($this->_initialDictionaryData !== $newDictionary) {
                $this->_decodeStream();
                $this->_initialDictionaryData = $newDictionary;
                $this->_encodeStream();
            }
        }

        // Update stream length
        $this->dictionary->Length->value = $this->_value->length();

        return  $this->_objNum + $shift . " " . $this->_genNum . " obj \n"
             .  $this->dictionary->toString($factory) . "\n"
             .  $this->_value->toString($factory) . "\n"
             . "endobj\n";
    }

    /**
     * Clean up resources, used by object
     */
    public function cleanUp()
    {
        $this->_dictionary = null;
        $this->_value      = null;
    }
}
