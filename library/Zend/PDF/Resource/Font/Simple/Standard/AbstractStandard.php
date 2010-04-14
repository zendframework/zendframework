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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\Resource\Font\Simple\Standard;
use Zend\PDF\InternalType;
use Zend\PDF;

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
 *  <li>Courier - {@link \Zend\PDF\Resource\Font\Simple\Standard\Courier}
 *  <li>Courier-Bold - {@link \Zend\PDF\Resource\Font\Simple\Standard\CourierBold}
 *  <li>Courier-Oblique - {@link \Zend\PDF\Resource\Font\Simple\Standard\CourierOblique}
 *  <li>Courier-BoldOblique - {@link \Zend\PDF\Resource\Font\Simple\Standard\CourierBoldOblique}
 *  <li>Helvetica - {@link \Zend\PDF\Resource\Font\Simple\Standard\Helvetica}
 *  <li>Helvetica-Bold - {@link \Zend\PDF\Resource\Font\Simple\Standard\HelveticaBold}
 *  <li>Helvetica-Oblique - {@link \Zend\PDF\Resource\Font\Simple\Standard\HelveticaOblique}
 *  <li>Helvetica-BoldOblique - {@link \Zend\PDF\Resource\Font\Simple\Standard\HelveticaBoldOblique}
 *  <li>Symbol - {@link \Zend\PDF\Resource\Font\Simple\Standard\Symbol}
 *  <li>Times - {@link \Zend\PDF\Resource\Font\Simple\Standard\Times}
 *  <li>Times-Bold - {@link \Zend\PDF\Resource\Font\Simple\Standard\TimesBold}
 *  <li>Times-Italic - {@link \Zend\PDF\Resource\Font\Simple\Standard\TimesItalic}
 *  <li>Times-BoldItalic - {@link \Zend\PDF\Resource\Font\Simple\Standard\TimesBoldItalic}
 *  <li>ZapfDingbats - {@link \Zend\PDF\Resource\Font\Simple\Standard\ZapfDingbats}
 * </ul>
 *
 * Font objects should be normally be obtained from the factory methods
 * {@link \Zend\PDF\Font::fontWithName} and {@link \Zend\PDF\Font::fontWithPath}.
 *
 * @uses       \Zend\PDF\InternalType\NameObject
 * @uses       \Zend\PDF\Font
 * @uses       \Zend\PDF\Resource\Font\Simple\AbstractSimple
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Fonts
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractStandard extends \Zend\PDF\Resource\Font\Simple\AbstractSimple
{
    /**** Public Interface ****/


    /* Object Lifecycle */

    /**
     * Object constructor
     */
    public function __construct()
    {
        $this->_fontType = PDF\Font::TYPE_STANDARD;

        parent::__construct();
        $this->_resource->Subtype  = new InternalType\NameObject('Type1');
    }
}
