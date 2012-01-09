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
 * @package    Zend_Amf
 * @subpackage Parse_Amf0
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Amf\Parser\Amf0;

use Zend\Amf\Parser\AbstractSerializer,
    Zend\Amf\Parser,
    Zend\Amf,
    Zend\Date;

/**
 * Serializer PHP misc types back to there corresponding AMF0 Type Marker.
 *
 * @uses       Zend\Amf\Constants
 * @uses       Zend\Amf\Exception
 * @uses       Zend\Amf\Parser\Amf3\Serializer
 * @uses       Zend\Amf\Parser\Serializer
 * @uses       Zend\Amf\Parser\TypeLoader
 * @package    Zend_Amf
 * @subpackage Parse_Amf0
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Serializer extends AbstractSerializer
{
    /**
     * @var string Name of the class to be returned
     */
    protected $_className = '';

    /**
     * An array of reference objects
     * @var array
     */
    protected $_referenceObjects = array();

    /**
     * Determine type and serialize accordingly
     *
     * Checks to see if the type was declared and then either
     * auto negotiates the type or relies on the user defined markerType to
     * serialize the data into amf
     *
     * @param  mixed $data
     * @param  mixed $markerType
     * @param  mixed $dataByVal
     * @return Zend\Amf\Parser\Amf0\Serializer
     * @throws Zend\Amf\Exception for unrecognized types or data
     */
    public function writeTypeMarker(&$data, $markerType = null, $dataByVal = false)
    {
        // Workaround for PHP5 with E_STRICT enabled complaining about "Only 
        // variables should be passed by reference" 
        if ((null === $data) && ($dataByVal !== false)) {
            $data = &$dataByVal;
        }
        if (null !== $markerType) {
            //try to reference the given object
            if (!$this->writeObjectReference($data, $markerType)) {
                // Write the Type Marker to denote the following action script data type
                $this->_stream->writeByte($markerType);
                switch($markerType) {
                    case Amf\Constants::AMF0_NUMBER:
                        $this->_stream->writeDouble($data);
                        break;
                    case Amf\Constants::AMF0_BOOLEAN:
                        $this->_stream->writeByte($data);
                        break;
                    case Amf\Constants::AMF0_STRING:
                        $this->_stream->writeUTF($data);
                        break;
                    case Amf\Constants::AMF0_OBJECT:
                        $this->writeObject($data);
                        break;
                    case Amf\Constants::AMF0_NULL:
                        break;
                    case Amf\Constants::AMF0_REFERENCE:
                        $this->_stream->writeInt($data);
                        break;
                    case Amf\Constants::AMF0_MIXEDARRAY:
                        // Write length of numeric keys as zero.
                        $this->_stream->writeLong(0);
                        $this->writeObject($data);
                        break;
                    case Amf\Constants::AMF0_ARRAY:
                        $this->writeArray($data);
                        break;
                    case Amf\Constants::AMF0_DATE:
                        $this->writeDate($data);
                        break;
                    case Amf\Constants::AMF0_LONGSTRING:
                        $this->_stream->writeLongUTF($data);
                        break;
                    case Amf\Constants::AMF0_TYPEDOBJECT:
                        $this->writeTypedObject($data);
                        break;
                    case Amf\Constants::AMF0_AMF3:
                        $this->writeAmf3TypeMarker($data);
                        break;
                    default:
                        throw new Amf\Exception("Unknown Type Marker: " . $markerType);
                }
            }
        } else {
            if(is_resource($data)) {
                $data = Parser\TypeLoader::handleResource($data);
            }
            switch (true) {
                case (is_int($data) || is_float($data)):
                    $markerType = Amf\Constants::AMF0_NUMBER;
                    break;
                case (is_bool($data)):
                    $markerType = Amf\Constants::AMF0_BOOLEAN;
                    break;
                case (is_string($data) && (strlen($data) > 65536)):
                    $markerType = Amf\Constants::AMF0_LONGSTRING;
                    break;
                case (is_string($data)):
                    $markerType = Amf\Constants::AMF0_STRING;
                    break;
                case (is_object($data)):
                    if (($data instanceof \DateTime) || ($data instanceof Date\Date)) {
                        $markerType = Amf\Constants::AMF0_DATE;
                    } else {

                        if($className = $this->getClassName($data)){
                            //Object is a Typed object set classname
                            $markerType = Amf\Constants::AMF0_TYPEDOBJECT;
                            $this->_className = $className;
                        } else {
                            // Object is a generic classname
                            $markerType = Amf\Constants::AMF0_OBJECT;
                        }
                        break;
                    }
                    break;
                case (null === $data):
                    $markerType = Amf\Constants::AMF0_NULL;
                    break;
                case (is_array($data)):
                    // check if it is an associative array
                    $i = 0;
                    foreach (array_keys($data) as $key) {
                        // check if it contains non-integer keys
                        if (!is_numeric($key) || intval($key) != $key) {
                            $markerType = Amf\Constants::AMF0_OBJECT;
                            break;
                            // check if it is a sparse indexed array
                         } else if ($key != $i) {
                             $markerType = Amf\Constants::AMF0_MIXEDARRAY;
                             break;
                         }
                         $i++;
                    }
                    // Dealing with a standard numeric array
                    if(!$markerType){
                        $markerType = Amf\Constants::AMF0_ARRAY;
                        break;
                    }
                    break;
                default:
                    throw new Amf\Exception('Unsupported data type: ' . gettype($data));
            }

            $this->writeTypeMarker($data, $markerType);
        }
        return $this;
    }

    /**
     * Check if the given object is in the reference table, write the reference if it exists,
     * otherwise add the object to the reference table
     *
     * @param mixed $object object reference to check for reference
     * @param $markerType AMF type of the object to write
     * @param mixed $objectByVal object to check for reference
     * @return Boolean true, if the reference was written, false otherwise
     */
    protected function writeObjectReference(&$object, $markerType, $objectByVal = false) 
    {
        // Workaround for PHP5 with E_STRICT enabled complaining about "Only 
        // variables should be passed by reference"
        if ((null === $object) && ($objectByVal !== false)) {
            $object = &$objectByVal;
        }

        if ($markerType == Amf\Constants::AMF0_OBJECT 
            || $markerType == Amf\Constants::AMF0_MIXEDARRAY 
            || $markerType == Amf\Constants::AMF0_ARRAY
            || $markerType == Amf\Constants::AMF0_TYPEDOBJECT 
        ) {
            $ref = array_search($object, $this->_referenceObjects,true);
            //handle object reference
            if ($ref !== false){
                $this->writeTypeMarker($ref,Amf\Constants::AMF0_REFERENCE);
                return true;
            }

            $this->_referenceObjects[] = $object;
        }

        return false;
    }

    /**
     * Write a PHP array with string or mixed keys.
     *
     * @param object $data
     * @return Zend\Amf\Parser\Amf0\Serializer
     */
    public function writeObject($object)
    {
        // Loop each element and write the name of the property.
        foreach ($object as $key => &$value) {
            // skip variables starting with an _ private transient
            if( $key[0] == "_") continue;
            $this->_stream->writeUTF($key);
            $this->writeTypeMarker($value);
        }

        // Write the end object flag
        $this->_stream->writeInt(0);
        $this->_stream->writeByte(Amf\Constants::AMF0_OBJECTTERM);
        return $this;
    }

    /**
     * Write a standard numeric array to the output stream. If a mixed array
     * is encountered call writeTypeMarker with mixed array.
     *
     * @param array $array
     * @return Zend\Amf\Parser\Amf0\Serializer
     */
    public function writeArray(&$array)
    {
        $length = count($array);
        if (!$length < 0) {
            // write the length of the array
            $this->_stream->writeLong(0);
        } else {
            // Write the length of the numeric array
            $this->_stream->writeLong($length);
            for ($i=0; $i<$length; $i++) {
                $value = isset($array[$i]) ? $array[$i] : null;
                $this->writeTypeMarker($value);
            }
        }
        return $this;
    }

    /**
     * Convert the DateTime into an AMF Date
     *
     * @param  DateTime|\Zend\Date\Date $data
     * @return Zend\Amf\Parser\Amf0\Serializer
     */
    public function writeDate($data)
    {
        if ($data instanceof \DateTime) {
            $dateString = $data->format('U');
        } elseif ($data instanceof Date\Date) {
            $dateString = $data->toString('U');
        } else {
            throw new Amf\Exception('Invalid date specified; must be a DateTime or Zend_Date object');
        }
        $dateString *= 1000;

        // Make the conversion and remove milliseconds.
        $this->_stream->writeDouble($dateString);

        // Flash does not respect timezone but requires it.
        $this->_stream->writeInt(0);

        return $this;
    }

    /**
     * Write a class mapped object to the output stream.
     *
     * @param  object $data
     * @return Zend\Amf\Parser\Amf0\Serializer
     */
    public function writeTypedObject($data)
    {
        $this->_stream->writeUTF($this->_className);
        $this->writeObject($data);
        return $this;
    }

    /**
     * Encountered and AMF3 Type Marker use AMF3 serializer. Once AMF3 is
     * encountered it will not return to AMf0.
     *
     * @param  string $data
     * @return Zend\Amf\Parser\Amf0\Serializer
     */
    public function writeAmf3TypeMarker(&$data)
    {
        $serializer = new Parser\Amf3\Serializer($this->_stream);
        $serializer->writeTypeMarker($data);
        return $this;
    }

    /**
     * Find if the class name is a class mapped name and return the
     * respective classname if it is.
     *
     * @param object $object
     * @return false|string $className
     */
    protected function getClassName($object)
    {
        //Check to see if the object is a typed object and we need to change
        $className = '';
        switch (true) {
            // the return class mapped name back to actionscript class name.
            case Parser\TypeLoader::getMappedClassName(get_class($object)):
                $className = Parser\TypeLoader::getMappedClassName(get_class($object));
                break;
                // Check to see if the user has defined an explicit Action Script type.
            case isset($object->_explicitType):
                $className = $object->_explicitType;
                break;
                // Check if user has defined a method for accessing the Action Script type
            case method_exists($object, 'getASClassName'):
                $className = $object->getASClassName();
                break;
                // No return class name is set make it a generic object
            case ($object instanceof \stdClass):
                $className = '';
                break;
        // By default, use object's class name
            default:
        $className = get_class($object);
                break;
        }
        if(!$className == '') {
            return $className;
        } else {
            return false;
        }
    }
}
