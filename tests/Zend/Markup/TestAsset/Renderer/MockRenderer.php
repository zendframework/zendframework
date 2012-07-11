<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace ZendTest\Markup\TestAsset\Renderer;

use Zend\Markup\Renderer\AbstractRenderer;
use Zend\Filter\FilterChain;

/**
 * HTML renderer
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 */
class MockRenderer extends AbstractRenderer
{

    /**
     * Set the default filter
     *
     * @return void
     */
    public function addDefaultFilters()
    {
        $this->_defaultFilter = new FilterChain();
    }
}
