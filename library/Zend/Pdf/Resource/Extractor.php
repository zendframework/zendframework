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
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\Resource;
use Zend\Pdf\InternalType;
use Zend\Pdf\ObjectFactory;
use Zend\Pdf;


/**
 * Resource extractor class is used to detach resources from original PDF document.
 *
 * It provides resources sharing, so different pages or other PDF resources can share
 * its dependent resources (e.g. fonts or images) or other resources still use them without duplication.
 * It also reduces output PDF size, required memory for PDF processing and
 * processing time.
 *
 * The same extractor may be used for different source documents, several
 * extractors may be used for constracting one target document, but extractor
 * must not be shared between target documents.
 *
 * @package    Zend_Pdf
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Extractor
{
    /**
     * PDF objects factory.
     *
     * @var \Zend\Pdf\ObjectFactory
     */
    protected $_factory;

    /**
     * Reusable list of already processed objects
     *
     * @var array
     */
    protected $_processed;

    /**
     * Object constructor.
     */
    public function __construct()
    {
        $this->_factory   = Pdf\ObjectFactory::createFactory(1);
        $this->_processed = array();
    }

    /**
     * Clone page, extract it and dependent objects from the current document,
     * so it can be used within other docs
     *
     * return \Zend\Pdf\Page
     */
    public function clonePage(Pdf\Page $page)
    {
        return $page->clonePage($this->_factory, $this->_processed);
    }
}
