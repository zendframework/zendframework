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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Validator\File;

use Traversable,
    Zend\Stdlib\ArrayUtils;

/**
 * Validator which checks if the file already exists in the directory
 *
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class IsCompressed extends MimeType
{
    /**
     * @const string Error constants
     */
    const FALSE_TYPE   = 'fileIsCompressedFalseType';
    const NOT_DETECTED = 'fileIsCompressedNotDetected';
    const NOT_READABLE = 'fileIsCompressedNotReadable';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::FALSE_TYPE   => "File '%value%' is not compressed, '%type%' detected",
        self::NOT_DETECTED => "The mimetype of file '%value%' could not be detected",
        self::NOT_READABLE => "File '%value%' is not readable or does not exist",
    );

    /**
     * Sets validator options
     *
     * @param  string|array|Traversable $compression
     */
    public function __construct($options = array())
    {
        // http://de.wikipedia.org/wiki/Liste_von_Dateiendungen
        $default = array(
            'application/arj',
            'application/gnutar',
            'application/lha',
            'application/lzx',
            'application/vnd.ms-cab-compressed',
            'application/x-ace-compressed',
            'application/x-arc',
            'application/x-archive',
            'application/x-arj',
            'application/x-bzip',
            'application/x-bzip2',
            'application/x-cab-compressed',
            'application/x-compress',
            'application/x-compressed',
            'application/x-cpio',
            'application/x-debian-package',
            'application/x-eet',
            'application/x-gzip',
            'application/x-java-pack200',
            'application/x-lha',
            'application/x-lharc',
            'application/x-lzh',
            'application/x-lzma',
            'application/x-lzx',
            'application/x-rar',
            'application/x-sit',
            'application/x-stuffit',
            'application/x-tar',
            'application/zip',
            'application/zoo',
            'multipart/x-gzip',
        );

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (empty($options)) {
            $options = array('mimeType' => $default);
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
    protected function createError($file, $errorType)
    {
        if ($file !== null) {
            if (is_array($file)) {
                if(array_key_exists('name', $file)) {
                    $file = $file['name'];
                }
            } 

            if (is_string($file)) {
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
