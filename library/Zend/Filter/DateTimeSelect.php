<?php

namespace Zend\Filter;

class DateTimeSelect extends DateSelect
{
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
            $value = sprintf('%s-%s-%s %s:%s:%s',
                $value['year'], $value['month'], $value['day'],
                $value['hour'], $value['minute'], $value['second']
            );
        }

        return $value;
    }
}
