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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\BinaryParser\Image;
use Zend\PDF\BinaryParser;

/**
 * FileParser for Zend_PDF_Image subclasses.
 *
 * @uses       \Zend\PDF\BinaryParser\AbstractBinaryParser
 * @uses       \Zend\PDF\Image
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Image
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
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
     * @param \Zend\PDF\BinaryParser\DataSource\AbstractDataSource $dataSource
     */
    public function __construct(\Zend\PDF\BinaryParser\DataSource\AbstractDataSource $dataSource)
    {
        parent::__construct($dataSource);
        $this->imageType = \Zend\PDF\Image::TYPE_UNKNOWN;
    }
}
