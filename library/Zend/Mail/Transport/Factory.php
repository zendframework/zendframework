<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mail\Transport;

use Traversable;
use Zend\Stdlib\ArrayUtils;

abstract class Factory
{
    /**
     * @var array Known transport types
     */
    protected static $classMap = array(
        'file'      => 'Zend\Mail\Transport\File',
        'inmemory'  => 'Zend\Mail\Transport\InMemory',
        'memory'    => 'Zend\Mail\Transport\InMemory',
        'null'      => 'Zend\Mail\Transport\InMemory',
        'sendmail'  => 'Zend\Mail\Transport\Sendmail',
        'smtp'      => 'Zend\Mail\Transport\Smtp',
    );

    /**
     * @param array $spec
     * @return TransportInterface
     * @throws Exception\InvalidArgumentException
     * @throws Exception\DomainException
     */
    public static function create($spec = array())
    {
        if ($spec instanceof Traversable) {
            $spec = ArrayUtils::iteratorToArray($spec);
        }

        if (! is_array($spec)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($spec) ? get_class($spec) : gettype($spec))
            ));
        }

        $type = isset($spec['type']) ? $spec['type'] : 'sendmail';

        $normalizedType = strtolower($type);

        if (isset(static::$classMap[$normalizedType])) {
            $type = static::$classMap[$normalizedType];
        }

        if (! class_exists($type)) {
            throw new Exception\DomainException(sprintf(
                '%s expects the "type" attribute to resolve to an existing class; received "%s"',
                __METHOD__,
                $type
            ));
        }

        $transport = new $type;

        if (! $transport instanceof TransportInterface) {
            throw new Exception\DomainException(sprintf(
                '%s expects the "type" attribute to resolve to a valid'
                . ' Zend\Mail\Transport\TransportInterface instance; received "%s"',
                __METHOD__,
                $type
            ));
        }

        if ($transport instanceof Smtp && isset($spec['options'])) {
            $transport->setOptions(new SmtpOptions($spec['options']));
        }

        if ($transport instanceof File && isset($spec['options'])) {
            $transport->setOptions(new FileOptions($spec['options']));
        }

        return $transport;
    }
}
