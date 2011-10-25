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

use finfo,
    Zend\Loader;

/**
 * Validator for the mime type of a file
 *
 * @uses      finfo
 * @uses      \Zend\Loader
 * @uses      \Zend\Validator\File\MimeType
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class ExcludeMimeType extends MimeType
{
    const FALSE_TYPE   = 'fileExcludeMimeTypeFalse';
    const NOT_DETECTED = 'fileExcludeMimeTypeNotDetected';
    const NOT_READABLE = 'fileExcludeMimeTypeNotReadable';

    /**
     * Returns true if the mimetype of the file does not matche the given ones. Also parts
     * of mimetypes can be checked. If you give for example "image" all image
     * mime types will not be accepted like "image/gif", "image/jpeg" and so on.
     *
     * @param  string $value Real file to check for mimetype
     * @param  array  $file  File data from \Zend\File\Transfer\Transfer
     * @return boolean
     */
    public function isValid($value, $file = null)
    {
        if ($file === null) {
            $file = array(
                'type' => null,
                'name' => $value,
            );
        }

        // Is file readable ?
        if (!Loader::isReadable($value)) {
            return $this->_throw($file, self::NOT_READABLE);
        }

        $mimefile = $this->getMagicFile();
        if (class_exists('finfo', false)) {
            $const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
            if (!$this->isMagicFileDisabled() && (!empty($mimefile) && empty($this->_finfo))) {
                $this->_finfo = @finfo_open($const, $mimefile);
            }

            if (empty($this->_finfo)) {
                $this->_finfo = @finfo_open($const);
            }

            $this->_type = null;
            if (!empty($this->_finfo)) {
                $this->_type = finfo_file($this->_finfo, $value);
            }
        }

        if (empty($this->_type) &&
            (function_exists('mime_content_type') && ini_get('mime_magic.magicfile'))) {
                $this->_type = mime_content_type($value);
        }

        if (empty($this->_type) && $this->getHeaderCheck()) {
            $this->_type = $file['type'];
        }

        if (empty($this->_type)) {
            return $this->_throw($file, self::NOT_DETECTED);
        }

        $mimetype = $this->getMimeType(true);
        if (in_array($this->_type, $mimetype)) {
            return $this->_throw($file, self::FALSE_TYPE);
        }

        $types = explode('/', $this->_type);
        $types = array_merge($types, explode('-', $this->_type));
        $types = array_merge($types, explode(';', $this->_type));
        foreach($mimetype as $mime) {
            if (in_array($mime, $types)) {
                return $this->_throw($file, self::FALSE_TYPE);
            }
        }

        return true;
    }
}
