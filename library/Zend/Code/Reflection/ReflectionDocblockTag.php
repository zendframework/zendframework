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
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Code\Reflection;

use Zend\Code\Reflection;

/**
 * @uses       Reflector
 * @uses       \Zend\Loader
 * @uses       \Zend\Code\Reflection\Exception
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ReflectionDocblockTag implements Reflection
{

    const TRIM_WHITESPACE = 'trimWhitespace';
    
    /**
     * @var array Rules and regexs to parse tags
     */
    protected static $typeRules = array(
        array(
            'param',
            '#^@(?<name>param)\s(?<type>\s*[\w|\\\|]+)(?:\s(?<variable>\s*\$\S*))?(?:\s(?<description>.*))?#s'
            ),
        array(
            'return',
            '#^@(?<name>return)\s(?<type>\s*[\w|\\\|]+)(?:\s(?<description>.*))?#'
            ),
        array(
            'tag',
            '#^@(?<name>\w+)(?:\s(?<description>(?:.*)+))?#'
            )
        );
    
    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var array
     */
    protected $values = array();

    /**
     * Export reflection
     *
     * Required by Reflector
     *
     * @todo   What should this do?
     * @return void
     */
    public static function export()
    {
    }

    /**
     * Constructor
     *
     * @param string $tagDocblockLine
     * @return ReflectionDocblockTag
     */
    public function __construct($tagDocblockLine)
    {
        $this->parse($tagDocblockLine);
    }

    /**
     * Get annotation tag name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get annotation tag description
     *
     * @return string
     */
    public function __call($methodName, $params)
    {
        if (strtolower(substr($methodName, 0, 3)) !== 'get') {
            throw new Exception\BadMethodCallException('Method ' . $methodName . ' is not supported');
        }
        
        $name = substr($methodName, 3);
        $value = $this->{$name};
        if (in_array(self::TRIM_WHITESPACE, $params)) {
            $value = trim($value);
        }
        return $value;
    }
    
    /**
     * __get()
     * 
     * @param string $name
     * @return multitype:
     */
    public function __get($name)
    {
        if (!$this->__isset($name)) {
            throw new Exception\InvalidArgumentException('Property by name ' . $name . ' does not exist');
        }
        
        return $this->values[strtolower($name)];
    }

    /**
     * __isset()
     * 
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return array_key_exists(strtolower($name), $this->values);
    }

    public function toString()
    {
        $str = "Docblock Tag [ * @"
            . $this->name
            . " ]".PHP_EOL;

        return $str;
    }

    /**
     * Serialize to string
     *
     * Required by Reflector
     *
     * @todo   What should this do?
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
    
    protected function parse($docblockLine)
    {
        foreach (self::$typeRules as $typeRule) {
            $name = $typeRule[0];
            $regex = $typeRule[1];
            $matches = array();
            if (preg_match($regex, $docblockLine, $matches)) {
                break;
            }
        }

        if (!$matches) {
            throw new Exception\RuntimeException('Could not parse the supplied tag line (' . $docblockLine . ')');
        }

        foreach ($matches as $name => $value) {
            if (is_string($name)) {
                if ($name == 'name') {
                    $this->name = $value;
                } else {
                    $this->values[strtolower($name)] = $value;
                }
            }
        }
    }
    
}
