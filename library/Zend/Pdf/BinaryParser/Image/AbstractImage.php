<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\BinaryParser\Image;

use Zend\Pdf\BinaryParser;

/**
 * \Zend\Pdf\Image related file parsers abstract class.
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Image
 */
abstract class AbstractImage extends BinaryParser\AbstractBinaryParser
{
    /**
     * Image Type
     *
     * @var integer
     */
    protected $imageType;

    /**
     * Object constructor.
     *
     * Validates the data source and enables debug logging if so configured.
     *
     * @param \Zend\Pdf\BinaryParser\DataSource\AbstractDataSource $dataSource
     */
    public function __construct(\Zend\Pdf\BinaryParser\DataSource\AbstractDataSource $dataSource)
    {
        parent::__construct($dataSource);
        $this->imageType = \Zend\Pdf\Image::TYPE_UNKNOWN;
    }
}
