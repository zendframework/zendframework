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
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Helper;

use Zend\Paginator;
use Zend\View;
use Zend\View\Exception;

/**
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PaginationControl extends AbstractHelper
{
    /**
     * Default view partial
     *
     * @var string|array
     */
    protected static $_defaultViewPartial = null;

    /**
     * Sets the default view partial.
     *
     * @param string|array $partial View partial
     */
    public static function setDefaultViewPartial($partial)
    {
        self::$_defaultViewPartial = $partial;
    }

    /**
     * Gets the default view partial
     *
     * @return string|array
     */
    public static function getDefaultViewPartial()
    {
        return self::$_defaultViewPartial;
    }

    /**
     * Render the provided pages.  This checks if $view->paginator is set and,
     * if so, uses that.  Also, if no scrolling style or partial are specified,
     * the defaults will be used (if set).
     *
     * @param  \Zend\Paginator\Paginator (Optional) $paginator
     * @param  string $scrollingStyle (Optional) Scrolling style
     * @param  string $partial (Optional) View partial
     * @param  array|string $params (Optional) params to pass to the partial
     * @return string
     * @throws Exception\RuntimeException if no paginator or no view partial provided
     * @throws Exception\InvalidArgumentException if partial is invalid array
     */
    public function __invoke(Paginator\Paginator $paginator = null, $scrollingStyle = null, $partial = null, $params = null)
    {
        if ($paginator === null) {
            if (isset($this->view->paginator) and $this->view->paginator !== null and $this->view->paginator instanceof Paginator\Paginator) {
                $paginator = $this->view->paginator;
            } else {
                throw new Exception\RuntimeException('No paginator instance provided or incorrect type');
            }
        }

        if ($partial === null) {
            if (self::$_defaultViewPartial === null) {
                throw new Exception\RuntimeException('No view partial provided and no default set');
            }

            $partial = self::$_defaultViewPartial;
        }

        $pages = get_object_vars($paginator->getPages($scrollingStyle));

        if ($params !== null) {
            $pages = array_merge($pages, (array) $params);
        }

        if (is_array($partial)) {
            if (count($partial) != 2) {
                throw new Exception\InvalidArgumentException(
                    'A view partial supplied as an array must contain two values: the filename and its module'
                );
            }

            if ($partial[1] !== null) {
                $partialHelper = $this->view->plugin('partial');
                return $partialHelper($partial[0], $pages);
            }

            $partial = $partial[0];
        }

        $partialHelper = $this->view->plugin('partial');
        return $partialHelper($partial, $pages);
    }
}
