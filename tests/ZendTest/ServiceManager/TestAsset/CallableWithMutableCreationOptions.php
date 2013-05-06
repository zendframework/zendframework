<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager\TestAsset;

use stdClass;
use Zend\ServiceManager\MutableCreationOptionsInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * implements multiple interface invokable object mock
 */
class CallableWithMutableCreationOptions implements MutableCreationOptionsInterface
{
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }

    public function __invoke(ServiceLocatorInterface $serviceLocator, $cName, $rName)
    {
        return new stdClass;
    }
}
