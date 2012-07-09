<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Media;

use Zend\GData\Media;

/**
 * The Gdata flavor of an Atom Feed with media support
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Media
 */
class Feed extends \Zend\GData\Feed
{

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend\GData\Media\Entry';

    /**
     * Create a new instance.
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Media::$namespaces);
        parent::__construct($element);
    }

}
