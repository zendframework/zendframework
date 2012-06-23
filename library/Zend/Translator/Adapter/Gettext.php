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
 * @package    Zend_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Translator\Adapter;

use Zend\Translator\Adapter\AbstractAdapter,
    Zend\Translator,
    Zend\Translator\Exception\InvalidArgumentException,
    Zend\Translator\Exception\InvalidFileTypeException;

/**
 * @category   Zend
 * @package    Zend_Translator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Gettext extends AbstractAdapter
{
    // Internal variables
    private $_bigEndian   = false;
    private $_file        = false;
    private $_adapterInfo = array();

    /**
     * Read values from the MO file
     *
     * @param  string  $bytes
     */
    private function _readMOData($bytes)
    {
        if ($this->_bigEndian === false) {
            return unpack('V' . $bytes, fread($this->_file, 4 * $bytes));
        } else {
            return unpack('N' . $bytes, fread($this->_file, 4 * $bytes));
        }
    }

    /**
     * Load translation data (MO file reader)
     *
     * @param  string  $filename  MO file to add, full path must be given for access
     * @param  string  $locale    New Locale/Language to set, identical with locale identifier,
     *                            see Zend_Locale for more information
     * @param  array   $option    OPTIONAL Options to use
     * @throws \Zend\Translator\Exception\InvalidArgumentException
     * @throws \Zend\Translator\Exception\InvalidFileTypeException
     * @return array
     */
    protected function _loadTranslationData($filename, $locale, array $options = array())
    {
        $result           = array();
        $this->_bigEndian = false;
        $this->_file      = @fopen($filename, 'rb');
        if (!$this->_file) {
            throw new InvalidArgumentException('Error opening translation file \'' . $filename . '\'.');
        }
        if (@filesize($filename) < 10) {
            @fclose($this->_file);
            throw new InvalidFileTypeException('\'' . $filename . '\' is not a gettext file');
        }

        // get Endian
        $input = $this->_readMOData(1);
        if (strtolower(substr(dechex($input[1]), -8)) == "950412de") {
            $this->_bigEndian = false;
        } else if (strtolower(substr(dechex($input[1]), -8)) == "de120495") {
            $this->_bigEndian = true;
        } else {
            @fclose($this->_file);
            throw new InvalidFileTypeException('\'' . $filename . '\' is not a gettext file');
        }
        // read revision - not supported for now
        $input = $this->_readMOData(1);

        // number of bytes
        $input = $this->_readMOData(1);
        $total = $input[1];

        // number of original strings
        $input = $this->_readMOData(1);
        $OOffset = $input[1];

        // number of translation strings
        $input = $this->_readMOData(1);
        $TOffset = $input[1];

        // fill the original table
        fseek($this->_file, $OOffset);
        $origtemp = $this->_readMOData(2 * $total);
        fseek($this->_file, $TOffset);
        $transtemp = $this->_readMOData(2 * $total);

        for($count = 0; $count < $total; ++$count) {
            if ($origtemp[$count * 2 + 1] != 0) {
                fseek($this->_file, $origtemp[$count * 2 + 2]);
                $original = @fread($this->_file, $origtemp[$count * 2 + 1]);
                $original = explode("\0", $original);
            } else {
                $original[0] = '';
            }

            if ($transtemp[$count * 2 + 1] != 0) {
                fseek($this->_file, $transtemp[$count * 2 + 2]);
                $translate = fread($this->_file, $transtemp[$count * 2 + 1]);
                $translate = explode("\0", $translate);
                if ((count($original) > 1) && (count($translate) > 1)) {
                    $result[$locale][$original[0]] = $translate;
                    array_shift($original);
                    foreach ($original as $orig) {
                        $result[$locale][$orig] = '';
                    }
                } else {
                    $result[$locale][$original[0]] = $translate[0];
                }
            }
        }

        @fclose($this->_file);
        $result[$locale][''] = trim($result[$locale]['']);
        if (empty($result[$locale][''])) {
            $this->_adapterInfo[$filename] = 'No adapter information available';
        } else {
            $this->_adapterInfo[$filename] = $result[$locale][''];
        }

        unset($result[$locale]['']);
        return $result;
    }

    /**
     * Returns the adapter informations
     *
     * @return array Each loaded adapter information as array value
     */
    public function getAdapterInfo()
    {
        return $this->_adapterInfo;
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString()
    {
        return "Gettext";
    }
}
