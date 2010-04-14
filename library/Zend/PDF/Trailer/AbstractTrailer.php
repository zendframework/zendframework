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
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\Trailer;
use Zend\PDF;
use Zend\PDF\InternalType;

/**
 * PDF file trailer
 *
 * @uses       \Zend\PDF\InternalType\DictionaryObject
 * @uses       \Zend\PDF\Exception
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractTrailer
{
    private static $_allowedKeys = array('Size', 'Prev', 'Root', 'Encrypt', 'Info', 'ID', 'Index', 'W', 'XRefStm', 'DocChecksum');

    /**
     * Trailer dictionary.
     *
     * @var \Zend\PDF\InternalType\DictionaryObject
     */
    private $_dict;

    /**
     * Check if key is correct
     *
     * @param string $key
     * @throws \Zend\PDF\Exception
     */
    private function _checkDictKey($key)
    {
        if ( !in_array($key, self::$_allowedKeys) ) {
            /** @todo Make warning (log entry) instead of an exception */
            throw new PDF\Exception("Unknown trailer dictionary key: '$key'.");
        }
    }


    /**
     * Object constructor
     *
     * @param \Zend\PDF\InternalType\DictionaryObject $dict
     */
    public function __construct(InternalType\DictionaryObject $dict)
    {
        $this->_dict   = $dict;

        foreach ($this->_dict->getKeys() as $dictKey) {
            $this->_checkDictKey($dictKey);
        }
    }

    /**
     * Get handler
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->_dict->$property;
    }

    /**
     * Set handler
     *
     * @param string $property
     * @param  mixed $value
     */
    public function __set($property, $value)
    {
        $this->_checkDictKey($property);
        $this->_dict->$property = $value;
    }

    /**
     * Return string trailer representation
     *
     * @return string
     */
    public function toString()
    {
        return "trailer\n" . $this->_dict->toString() . "\n";
    }


    /**
     * Get length of source PDF
     *
     * @return string
     */
    abstract public function getPDFLength();

    /**
     * Get PDF String
     *
     * @return string
     */
    abstract public function getPDFString();

    /**
     * Get header of free objects list
     * Returns object number of last free object
     *
     * @return integer
     */
    abstract public function getLastFreeObject();
}
