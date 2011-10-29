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
 * @package   Zend_Validate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Validator\File;

/**
 * Validator which checks if the file already exists in the directory
 *
 * @uses      \Zend\Validator\File\MimeType
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class IsImage extends MimeType
{
    /**
     * @const string Error constants
     */
    const FALSE_TYPE   = 'fileIsImageFalseType';
    const NOT_DETECTED = 'fileIsImageNotDetected';
    const NOT_READABLE = 'fileIsImageNotReadable';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::FALSE_TYPE   => "File '%value%' is no image, '%type%' detected",
        self::NOT_DETECTED => "The mimetype of file '%value%' could not be detected",
        self::NOT_READABLE => "File '%value%' is not readable or does not exist",
    );

    /**
     * Sets validator options
     *
     * @param  string|array|\Zend\Config\Config $mimetype
     * @return void
     */
    public function __construct($options = array())
    {
        // http://de.wikipedia.org/wiki/Liste_von_Dateiendungen
        // http://www.iana.org/assignments/media-types/image/
        $default = array(
            'application/cdf',
            'application/dicom',
            'application/fractals',
            'application/postscript',
            'application/vnd.hp-hpgl',
            'application/vnd.oasis.opendocument.graphics',
            'application/x-cdf',
            'application/x-cmu-raster',
            'application/x-ima',
            'application/x-inventor',
            'application/x-koan',
            'application/x-portable-anymap',
            'application/x-world-x-3dmf',
            'image/bmp',
            'image/c',
            'image/cgm',
            'image/fif',
            'image/gif',
            'image/jpeg',
            'image/jpm',
            'image/jpx',
            'image/jp2',
            'image/naplps',
            'image/pjpeg',
            'image/png',
            'image/svg',
            'image/svg+xml',
            'image/tiff',
            'image/vnd.adobe.photoshop',
            'image/vnd.djvu',
            'image/vnd.fpx',
            'image/vnd.net-fpx',
            'image/x-cmu-raster',
            'image/x-cmx',
            'image/x-coreldraw',
            'image/x-cpi',
            'image/x-emf',
            'image/x-ico',
            'image/x-icon',
            'image/x-jg',
            'image/x-ms-bmp',
            'image/x-niff',
            'image/x-pict',
            'image/x-pcx',
            'image/x-png',
            'image/x-portable-anymap',
            'image/x-portable-bitmap',
            'image/x-portable-greymap',
            'image/x-portable-pixmap',
            'image/x-quicktime',
            'image/x-rgb',
            'image/x-tiff',
            'image/x-unknown',
            'image/x-windows-bmp',
            'image/x-xpmi',
        );

        if (is_array($options) && array_key_exists('magicFile', $options)) {
            $this->setMagicFile($options['magicFile']);
            unset($options['magicFile']);
        }

        if (is_array($options) && array_key_exists('enableHeaderCheck', $options)) {
            $this->enableHeaderCheck($options['enableHeaderCheck']);
            unset($options['enableHeaderCheck']);
        }

        if (is_array($options) && !array_key_exists('mimeType', $options)) {
            $options['mimeType'] = $options;
        }

        if (empty($options)) {
            $options = array('mimeType' => $default);
        }

        if (!is_array($options)) {
            $options = array('mimeType' => $options);
        }

        parent::__construct($options);
    }

    /**
     * Throws an error of the given type
     * Duplicates parent method due to OOP Problem with late static binding in PHP 5.2
     *
     * @param  string $file
     * @param  string $errorType
     * @return false
     */
    protected function _throw($file, $errorType)
    {
        if ($file !== null) {
            if (is_array($file)) {
                if(array_key_exists('name', $file)) {
                    $this->value = basename($file['name']);
                }
            } else if (is_string($file)) {
                $this->value = basename($file);
            }
        }

        switch($errorType) {
            case MimeType::FALSE_TYPE :
                $errorType = self::FALSE_TYPE;
                break;
            case MimeType::NOT_DETECTED :
                $errorType = self::NOT_DETECTED;
                break;
            case MimeType::NOT_READABLE :
                $errorType = self::NOT_READABLE;
                break;
        }

        $this->error($errorType);
        return false;
    }
}
