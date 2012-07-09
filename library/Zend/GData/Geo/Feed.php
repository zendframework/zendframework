<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
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
