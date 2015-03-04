<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail;

use Traversable;
use Zend\Mail\Message;

class MessageFactory
{
    /**
     * @param array|Traversable $options
     * @return Zend\Mail\Message
     */
    public static function getInstance($options = array())
    {
       if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '"%s" expects an array or Traversable; received "%s"',
                __METHOD__,
                (is_object($options) ? get_class($options) : gettype($options))
            ));
        }

        $mail = new Message();
        foreach ($options as $key => $value) {
            $setter = 'set' . str_replace(' ',  '', ucwords(strtr($key, array('-' => ' ', '_' => ' '))));
            if (method_exists($mail, $setter)) {
                $mail->{$setter}($value);
            }
        }

        return $mail;
    }
}