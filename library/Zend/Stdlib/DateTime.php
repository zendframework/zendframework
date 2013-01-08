<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace Zend\Stdlib;

use DateTimeZone;

/**
 * DateTime
 *
 * An extension of the \DateTime object.
 *
 * @category   Zend
 * @package    Zend_Stdlib
 */
class DateTime extends \DateTime {
    
    /**
     * Creates a DateTime object from a string and (optional) timezone.
     * 
     * @param string $time
     * @param DateTimeZone $timezone
     * @return mixed
     */
    public static function createISO8601Date($time, DateTimeZone $timezone = null){
        $format = self::ISO8601;
        if (isset($time[19]) && $time[19] === '.') {
            $format = 'Y-m-d\TH:i:s.uO';
        }
        
        if( $timezone !== null )
            return self::createFromFormat($format, $time, $timezone);

        return self::createFromFormat($format, $time);
    }
    
}