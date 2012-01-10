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
 * @subpackage Zend_PDF_Image
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\BinaryParser\Image;
use Zend\Pdf\BinaryParser;

/**
 * \Zend\Pdf\Image related file parsers abstract class.
 *
 * @uses       \Zend\Pdf\BinaryParser\AbstractBinaryParser
 * @uses       \Zend\Pdf\Image
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Image
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
