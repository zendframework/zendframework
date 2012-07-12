<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\Resource\Image;

use Zend\Pdf;
use Zend\Pdf\Exception;
use Zend\Pdf\InternalType;

/**
 * JPEG image
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Image
 */
class Jpeg extends AbstractImage
{

    protected $_width;
    protected $_height;
    protected $_imageProperties;

    /**
     * Object constructor
     *
     * @param string $imageFileName
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public function __construct($imageFileName)
    {
        if (!function_exists('gd_info')) {
            throw new Exception\RuntimeException('Image extension is not installed.');
        }

        $gd_options = gd_info();
        if ( (!isset($gd_options['JPG Support'])  || $gd_options['JPG Support']  != true)  &&
             (!isset($gd_options['JPEG Support']) || $gd_options['JPEG Support'] != true)  ) {
            throw new Exception\RuntimeException('JPG support is not configured properly.');
        }

        if (!is_readable($imageFileName)) {
            throw new Exception\IOException( "File '$imageFileName' is not readable." );
        }
        if (($imageInfo = getimagesize($imageFileName)) === false) {
            throw new Exception\CorruptedImageException('Corrupted image.');
        }
        if ($imageInfo[2] != IMAGETYPE_JPEG && $imageInfo[2] != IMAGETYPE_JPEG2000) {
            throw new Exception\DomainException('ImageType is not JPG');
        }

        parent::__construct();

        switch ($imageInfo['channels']) {
            case 3:
                $colorSpace = 'DeviceRGB';
                break;
            case 4:
                $colorSpace = 'DeviceCMYK';
                break;
            default:
                $colorSpace = 'DeviceGray';
                break;
        }

        $imageDictionary = $this->_resource->dictionary;
        $imageDictionary->Width            = new InternalType\NumericObject($imageInfo[0]);
        $imageDictionary->Height           = new InternalType\NumericObject($imageInfo[1]);
        $imageDictionary->ColorSpace       = new InternalType\NameObject($colorSpace);
        $imageDictionary->BitsPerComponent = new InternalType\NumericObject($imageInfo['bits']);
        if ($imageInfo[2] == IMAGETYPE_JPEG) {
            $imageDictionary->Filter       = new InternalType\NameObject('DCTDecode');
        } elseif ($imageInfo[2] == IMAGETYPE_JPEG2000){
            $imageDictionary->Filter       = new InternalType\NameObject('JPXDecode');
        }

        if (($imageFile = @fopen($imageFileName, 'rb')) === false ) {
            throw new Exception\IOException("Can not open '$imageFileName' file for reading.");
        }
        $byteCount = filesize($imageFileName);
        $this->_resource->value = '';
        while ( $byteCount > 0 && ($nextBlock = fread($imageFile, $byteCount)) != false ) {
            $this->_resource->value .= $nextBlock;
            $byteCount -= strlen($nextBlock);
        }
        fclose($imageFile);
        $this->_resource->skipFilters();

    $this->_width = $imageInfo[0];
    $this->_height = $imageInfo[1];
    $this->_imageProperties = array();
    $this->_imageProperties['bitDepth'] = $imageInfo['bits'];
    $this->_imageProperties['jpegImageType'] = $imageInfo[2];
    $this->_imageProperties['jpegColorType'] = $imageInfo['channels'];
    }

    /**
     * Image width
     */
    public function getPixelWidth()
    {
        return $this->_width;
    }

    /**
     * Image height
     */
    public function getPixelHeight()
    {
        return $this->_height;
    }

    /**
     * Image properties
     */
    public function getProperties()
    {
        return $this->_imageProperties;
    }
}
