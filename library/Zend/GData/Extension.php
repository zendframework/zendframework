<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData;

use Zend\GData\App\Extension\AbstractExtension;

/**
 * Represents a Gdata extension
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gdata
 */
class Extension extends AbstractExtension
{

    protected $_rootNamespace = 'gd';

    public function __construct()
    {
        /* NOTE: namespaces must be registered before calling parent */
        $this->registerNamespace('gd',
                'http://schemas.google.com/g/2005');
        $this->registerNamespace('openSearch',
                'http://a9.com/-/spec/opensearchrss/1.0/', 1, 0);
        $this->registerNamespace('openSearch',
                'http://a9.com/-/spec/opensearch/1.1/', 2, 0);
        $this->registerNamespace('rss',
                'http://blogs.law.harvard.edu/tech/rss');

        parent::__construct();
    }

}
