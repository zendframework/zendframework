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
 * @package    Zend_Form
 * @subpackage Hydrator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form\Hydrator;

use Zend\Form\Exception;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage Hydrator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ArraySerializable implements HydratorInterface
{
    /**
     * Hydrate an object implementing ArraySerializableInterface
     *
     * Hydrates an object implementing ArraySerializableInterface by passing 
     * $data to its exchangeArray() method.
     * 
     * @param  array $data 
     * @param  object $object 
     * @return void
     * @throws Exception\UnexpectedValueException for an $object not implementing ArraySerializableInterface
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof ArraySerializableInterface) {
            throw new Exception\UnexpectedValueException(sprintf(
                '%s expects the provided object to be of type Zend\Stlib\ArraySerializable; received "%s"',
                __METHOD__,
                get_class($object)
            ));
        }
        $object->exchangeArray($data);
    }
}
