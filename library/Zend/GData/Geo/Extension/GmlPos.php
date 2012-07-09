<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Geo\Extension;

/**
 * Represents the gml:pos element used by the Gdata Geo extensions.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Geo
 */
class GmlPos extends \Zend\GData\Extension
{

    protected $_rootNamespace = 'gml';
    protected $_rootElement = 'pos';

    /**
     * Constructs a new Zend_Gdata_Geo_Extension_GmlPos object.
     *
     * @param string $text (optional) The value to use for this element.
     */
    public function __construct($text = null)
    {
        $this->registerAllNamespaces(\Zend\GData\Geo::$namespaces);
        parent::__construct();
        $this->setText($text);
    }

}
