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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Amf\Adobe;

use Zend\Amf\Exception,
    Zend\Code\Reflection\ClassReflection,
    Zend\Code\Reflection\PropertyReflection,
    Zend\Server\Reflection,
    Zend\Server\Reflection\ReflectionClass as ServerReflectionClass,
    SplFileInfo;

/**
 * This class implements a service for generating AMF service descriptions as XML.
 *
 * @uses       Zend\Amf\Parser\TypeLoader
 * @uses       Zend\Loader
 * @uses       Zend\Reflection\ReflectionClass
 * @uses       Zend\Server\Reflection
 * @package    Zend_Amf
 * @subpackage Adobe
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Introspector
{
    /**
     * Options used:
     * - server: instance of Zend_Amf_Server to use
     * - directories: directories where class files may be looked up
     *
     * @var array Introspector options
     */
    protected $_options;

    /**
     * @var DOMElement DOM element to store types
     */
    protected $_types;

    /**
     * @var array Map of the known types
     */
    protected $_typesMap = array();

    /**
     * @var DOMDocument XML document to store data
     */
    protected $_xml;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->_xml = new \DOMDocument('1.0', 'utf-8');
    }

    /**
     * Create XML definition on an AMF service class
     *
     * @param  string $serviceClass Service class name
     * @param  array $options invocation options
     * @return string XML with service class introspection
     */
    public function introspect($serviceClass, $options = array())
    {
        $this->_options = $options;

        if (strpbrk($serviceClass, '\\/<>')) {
            return $this->_returnError('Invalid service name');
        }

        // Transform com.foo.Bar into com\foo\Bar
        $serviceClass = str_replace('.' , '\\', $serviceClass);

        // Introspect!
        if (!class_exists($serviceClass)) {
            if (!$this->_loadClass($serviceClass)) {
                return $this->_returnError('Invalid service name; class does not exist');
            }
        }

        $serv = $this->_xml->createElement('service-description');
        $serv->setAttribute('xmlns', 'http://ns.adobe.com/flex/service-description/2008');

        $this->_types = $this->_xml->createElement('types');
        $this->_ops   = $this->_xml->createElement('operations');

        $r = Reflection::reflectClass($serviceClass);
        $this->_addService($r, $this->_ops);

        $serv->appendChild($this->_types);
        $serv->appendChild($this->_ops);
        $this->_xml->appendChild($serv);

        return $this->_xml->saveXML();
    }

    /**
     * Authentication handler
     *
     * @param  \Zend\Acl\Acl $acl
     * @return unknown_type
     */
    public function initAcl(\Zend\Acl\Acl $acl)
    {
        return false; // we do not need auth for this class
    }

    /**
     * Generate map of public class attributes
     *
     * @param  string $typename type name
     * @param  DOMElement $typexml target XML element
     * @return void
     */
    protected function _addClassAttributes($typename, \DOMElement $typexml)
    {
        // Do not try to autoload here because _phpTypeToAS should
        // have already attempted to load this class
        if (!class_exists($typename, false)) {
            return;
        }

        $rc = new ClassReflection($typename);
        foreach ($rc->getProperties() as $prop) {
            if (!$prop->isPublic()) {
                continue;
            }

            $propxml = $this->_xml->createElement('property');
            $propxml->setAttribute('name', $prop->getName());

            $type = $this->_registerType($this->_getPropertyType($prop));
            $propxml->setAttribute('type', $type);

            $typexml->appendChild($propxml);
        }
    }

    /**
     * Build XML service description from reflection class
     *
     * @param  \Zend\Server\Reflection\ReflectionClass $refclass
     * @param  DOMElement $target target XML element
     * @return void
     */
    protected function _addService(ServerReflectionClass $refclass, \DOMElement $target)
    {
        foreach ($refclass->getMethods() as $method) {
            if (!$method->isPublic()
                || $method->isConstructor()
                || ('__' == substr($method->name, 0, 2))
            ) {
                continue;
            }

            foreach ($method->getPrototypes() as $proto) {
                $op = $this->_xml->createElement('operation');
                $op->setAttribute('name', $method->getName());

                $rettype = $this->_registerType($proto->getReturnType());
                $op->setAttribute('returnType', $rettype);

                foreach ($proto->getParameters() as $param) {
                    $arg = $this->_xml->createElement('argument');
                    $arg->setAttribute('name', $param->getName());

                    $type = $param->getType();
                    if ($type == 'mixed' && ($pclass = $param->getClass())) {
                        $type = $pclass->getName();
                    }

                    $ptype = $this->_registerType($type);
                    $arg->setAttribute('type', $ptype);

                    if($param->isDefaultValueAvailable()) {
                        $arg->setAttribute('defaultvalue', $param->getDefaultValue());
                    }

                    $op->appendChild($arg);
                }

                $target->appendChild($op);
            }
        }
    }

    /**
     * Extract type of the property from DocBlock
     *
     * @param  \Zend\Code\Reflection\PropertyReflection $prop reflection property object
     * @return string Property type
     */
    protected function _getPropertyType(PropertyReflection $prop)
    {
        $docBlock = $prop->getDocBlock();

        if (!$docBlock) {
            return 'Unknown';
        }

        if (!$docBlock->hasTag('var')) {
            return 'Unknown';
        }

        $tag = $docBlock->getTag('var');
        return trim($tag->getContent());
    }

    /**
     * Get the array of service directories
     *
     * @return array Service class directories
     */
    protected function _getServicePath()
    {
        if (isset($this->_options['server'])) {
            return $this->_options['server']->getDirectory();
        }

        if (isset($this->_options['directories'])) {
            return $this->_options['directories'];
        }

        return array();
    }

    /**
     * Map from PHP type name to AS type name
     *
     * @param  string $typename PHP type name
     * @return string AS type name
     */
    protected function _phpTypeToAS($typename)
    {
        if (class_exists($typename)) {
            $vars = get_class_vars($typename);

            if (isset($vars['_explicitType'])) {
                return $vars['_explicitType'];
            }
        }

        if (false !== ($asname = \Zend\Amf\Parser\TypeLoader::getMappedClassName($typename))) {
            return $asname;
        }

        return $typename;
    }

    /**
     * Register new type on the system
     *
     * @param  string $typename type name
     * @return string New type name
     */
    protected function _registerType($typename)
    {
        // Known type - return its AS name
        if (isset($this->_typesMap[$typename])) {
            return $this->_typesMap[$typename];
        }

        // Standard types
        if (in_array($typename, array('void', 'null', 'mixed', 'unknown_type'))) {
            return 'Unknown';
        }

        if (in_array($typename, array('int', 'integer', 'bool', 'boolean', 'float', 'string', 'object', 'Unknown', 'stdClass', 'array'))) {
            return $typename;
        }

        // Resolve and store AS name
        $asTypeName = $this->_phpTypeToAS($typename);
        $this->_typesMap[$typename] = $asTypeName;

        // Create element for the name
        $typeEl = $this->_xml->createElement('type');
        $typeEl->setAttribute('name', $asTypeName);
        $this->_addClassAttributes($typename, $typeEl);
        $this->_types->appendChild($typeEl);

        return $asTypeName;
    }

   /**
     * Return error with error message
     *
     * @param  string $msg Error message
     * @return string
     */
    protected function _returnError($msg)
    {
        return "ERROR: $msg";
    }

    /**
     * Load a service class from the service path
     * 
     * @param  string $class 
     * @return bool
     */
    protected function _loadClass($class)
    {
        $file = str_replace(array('\\', '_'), DIRECTORY_SEPARATOR, $class) . '.php';
        foreach ($this->_getServicePath() as $path) {
            $fileinfo = new SplFileInfo($path . DIRECTORY_SEPARATOR . $file);
            if ($fileinfo->isReadable()) {
                require_once $fileinfo->getRealPath();
                return true;
            }
        }
        return false;
    }
}
