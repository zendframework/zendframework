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
 */

namespace Zend\GData\Geo;

use Zend\GData\Geo;

/**
 * Feed for Gdata Geographic data entries.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Geo
 */
class Feed extends \Zend\GData\Feed
{

    /**
     * The classname for individual feed elements.
     *
     * @var string
     */
    protected $_entryClassName = 'Zend\GData\Geo\Entry';

    public function __construct($element = null)
    {
        $this->registerAllNamespaces(Geo::$namespaces);
        parent::__construct($element);
    }

}
