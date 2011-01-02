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
 * @subpackage Zend_PDF_Fonts
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Resource\Font\Simple\Standard;
use Zend\Pdf\InternalType;
use Zend\Pdf;

/**
 * Abstract class definition for the standard 14 Type 1 PDF fonts.
 *
 * The standard 14 PDF fonts are guaranteed to be availble in any PDF viewer
 * implementation. As such, they do not require much data for the font's
 * resource dictionary. The majority of the data provided by subclasses is for
 * the benefit of our own layout code.
 *
 * The standard fonts and the corresponding subclasses that manage them:
 * <ul>
 *  <li>Courier - {@link \Zend\Pdf\Resource\Font\Simple\Standard\Courier}
 *  <li>Courier-Bold - {@link \Zend\Pdf\Resource\Font\Simple\Standard\CourierBold}
 *  <li>Courier-Oblique - {@link \Zend\Pdf\Resource\Font\Simple\Standard\CourierOblique}
 *  <li>Courier-BoldOblique - {@link \Zend\Pdf\Resource\Font\Simple\Standard\CourierBoldOblique}
 *  <li>Helvetica - {@link \Zend\Pdf\Resource\Font\Simple\Standard\Helvetica}
 *  <li>Helvetica-Bold - {@link \Zend\Pdf\Resource\Font\Simple\Standard\HelveticaBold}
 *  <li>Helvetica-Oblique - {@link \Zend\Pdf\Resource\Font\Simple\Standard\HelveticaOblique}
 *  <li>Helvetica-BoldOblique - {@link \Zend\Pdf\Resource\Font\Simple\Standard\HelveticaBoldOblique}
 *  <li>Symbol - {@link \Zend\Pdf\Resource\Font\Simple\Standard\Symbol}
 *  <li>Times - {@link \Zend\Pdf\Resource\Font\Simple\Standard\Times}
 *  <li>Times-Bold - {@link \Zend\Pdf\Resource\Font\Simple\Standard\TimesBold}
 *  <li>Times-Italic - {@link \Zend\Pdf\Resource\Font\Simple\Standard\TimesItalic}
 *  <li>Times-BoldItalic - {@link \Zend\Pdf\Resource\Font\Simple\Standard\TimesBoldItalic}
 *  <li>ZapfDingbats - {@link \Zend\Pdf\Resource\Font\Simple\Standard\ZapfDingbats}
 * </ul>
 *
 * Font objects should be normally be obtained from the factory methods
 * {@link \Zend\Pdf\Font::fontWithName} and {@link \Zend\Pdf\Font::fontWithPath}.
 *
 * @uses       \Zend\Pdf\InternalType\NameObject
 * @uses       \Zend\Pdf\Font
 * @uses       \Zend\Pdf\Resource\Font\Simple\AbstractSimple
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Fonts
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractStandard extends \Zend\Pdf\Resource\Font\Simple\AbstractSimple
{
    /**** Public Interface ****/


    /* Object Lifecycle */

    /**
     * Object constructor
     */
    public function __construct()
    {
        $this->_fontType = Pdf\Font::TYPE_STANDARD;

        parent::__construct();
        $this->_resource->Subtype  = new InternalType\NameObject('Type1');
    }
}
