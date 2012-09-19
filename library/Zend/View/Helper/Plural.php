<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Helper for helping rendering text based on a count number (like the i18n plural translation helper, but
 * when translation is not needed)
 *
 * @package    Zend_View
 * @subpackage Helper
 */
class Plural extends AbstractHelper
{
    /**
     * @param string $singular  String to use if singular
     * @param string $plural    String to use if plural
     * @param int    $number    The number that is used to decide if it's singular or plurial
     * @return string
     */
    public function __invoke($singular, $plural, $number)
    {
        if ($number > 1) {
            return $plural;
        }

        return $singular;
    }
}

