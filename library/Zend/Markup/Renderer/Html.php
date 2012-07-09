<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace Zend\Markup\Renderer;

use Traversable;
use Zend\Markup\Renderer\Markup\Html\Root as RootMarkup;
use Zend\Stdlib\ArrayUtils;

/**
 * HTML renderer
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer
 */
class Html extends AbstractRenderer
{

    /**
     * Constructor
     *
     * @param  array|Traversable $options
     * @return void
     */
    public function __construct($options = array())
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (isset($options['markups'])) {
            if (!isset($options['markups']['Zend_Markup_Root'])) {
                $options['markups'] = array(
                    'Zend_Markup_Root' => new RootMarkup()
                );
            }
        }

        parent::__construct($options);
    }
}
