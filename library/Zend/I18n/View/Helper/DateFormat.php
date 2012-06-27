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
 * @package    Zend_I18n
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\I18n\View\Helper;

use DateTime;
use IntlDateFormatter;
use Zend\View\Helper\AbstractHelper;
use Zend\I18n\Exception;

/**
 * View helper for formatting dates.
 *
 * @category   Zend
 * @package    Zend_I18n
 * @subpackage View
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DateFormat extends AbstractHelper
{
    /**
     * Formatter instances.
     *
     * @var array
     */
    protected $formatters = array();

    /**
     * Add a formatter.
     *
     * @param  string            $name
     * @param  IntlDateFormatter $formatter
     * @return DateFormat
     */
    public function addFormatter($name, IntlDateFormatter $formatter)
    {
        $this->formatters[$name] = $formatter;
        return $this;
    }

    /**
     * Format a date.
     *
     * @param  DateTime|integer|array $date
     * @param  string                 $formatterName
     * @return string
     */
    public function __invoke($date, $formatterName)
    {
        if (!isset($this->formatters[$formatterName])) {
            throw new Exception\RuntimeException(sprintf(
                'No formatter with name %s found',
                $formatterName
            ));
        }

        // DateTime support for IntlDateFormatter::format() was only added in 5.3.4
        if ($date instanceof DateTime
            && version_compare(PHP_VERSION, '5.3.4', '<')
        ) {
            $date = $date->getTimestamp();
        }

        return $this->formatters[$formatterName]
                    ->format($date);
    }
}
