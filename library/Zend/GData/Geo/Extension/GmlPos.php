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
 * @package    Zend_Gdata
 * @subpackage Geo
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\Geo\Extension;

/**
 * Represents the gml:pos element used by the Gdata Geo extensions.
 *
 * @uses       \Zend\GData\Extension
 * @uses       \Zend\GData\Geo
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Geo
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
