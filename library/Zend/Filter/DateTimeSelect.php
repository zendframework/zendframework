<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

class DateTimeSelect extends AbstractDateDropdown
{
    protected $format = '%6$s-%4$s-%1$s %2$s:%3$s:%5$s';

    public function filter($value)
    {
        if (is_array($value)) {
            if ($this->nullOnEmpty() && (
                    empty($value['year']) ||
                    empty($value['month']) ||
                    empty($value['day']) ||
                    empty($value['hour']) ||
                    empty($value['minute']) ||
                    (isset($value['second']) && empty($value['second']))
                )
            ) {
                return null;
            }

            if ($this->nullOnAllEmpty() && (
                    empty($value['year']) &&
                    empty($value['month']) &&
                    empty($value['day']) &&
                    empty($value['hour']) &&
                    empty($value['minute']) &&
                    (!isset($value['second']) || empty($value['second']))

                )
            ) {
                return null;
            }

            if (!isset($value['second'])) {
                $value['second'] = '00';
            }

            ksort($value);

            $value = vsprintf($this->format, $value);
        }

        return $value;
    }
}
