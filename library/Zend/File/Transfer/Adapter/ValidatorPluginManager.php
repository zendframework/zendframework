<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_File_Transfer
 */

namespace Zend\File\Transfer\Adapter;

use Zend\Validator\ValidatorPluginManager as BaseManager;

/**
 * @category   Zend
 * @package    Zend_File_Transfer
 */
class ValidatorPluginManager extends BaseManager
{
    protected $aliases = array(
        'count'            =>'filecount',
        'crc32'            =>'filecrc32',
        'excludeextension' =>'fileexcludeextension',
        'excludemimetype'  =>'fileexcludemimetype',
        'exists'           =>'fileexists',
        'extension'        =>'fileextension',
        'filessize'        =>'filefilessize',
        'hash'             =>'filehash',
        'imagesize'        =>'fileimagesize',
        'iscompressed'     =>'fileiscompressed',
        'isimage'          =>'fileisimage',
        'md5'              =>'filemd5',
        'mimetype'         =>'filemimetype',
        'notexists'        =>'filenotexists',
        'sha1'             =>'filesha1',
        'size'             =>'filesize',
        'upload'           =>'fileupload',
        'wordcount'        =>'filewordcount',
    );
}
