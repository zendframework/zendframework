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
 * @package    Zend_Soap
 * @subpackage WSDL
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Soap\WSDL\Strategy;
use Zend\Soap\WSDL;

/**
 * Zend_Soap_WSDL_Strategy_Composite
 *
 * @uses       \Zend\Soap\WSDL\Exception
 * @uses       \Zend\Soap\WSDL\Strategy\StrategyInterface
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage WSDL
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Composite implements StrategyInterface
{
    /**
     * Typemap of Complex Type => Strategy pairs.
     *
     * @var array
     */
    protected $_typeMap = array();

    /**
     * Default Strategy of this composite
     *
     * @var string|\Zend\Soap\WSDL\Strategy\StrategyInterface
     */
    protected $_defaultStrategy;

    /**
     * Context WSDL file that this composite serves
     *
     * @var \Zend\Soap\WSDL\WSDL|null
     */
    protected $_context;

    /**
     * Construct Composite WSDL Strategy.
     *
     * @throws \Zend\Soap\WSDL\Exception
     * @param array $typeMap
     * @param string|\Zend\Soap\WSDL\Strategy\StrategyInterface $defaultStrategy
     */
    public function __construct(array $typeMap=array(), $defaultStrategy="Zend_Soap_WSDL_Strategy_DefaultComplexType")
    {
        foreach($typeMap AS $type => $strategy) {
            $this->connectTypeToStrategy($type, $strategy);
        }
        $this->_defaultStrategy = $defaultStrategy;
    }

    /**
     * Connect a complex type to a given strategy.
     *
     * @throws \Zend\Soap\WSDL\Exception
     * @param  string $type
     * @param  string|\Zend\Soap\WSDL\Strategy\StrategyInterface $strategy
     * @return \Zend\Soap\WSDL\Strategy\Composite
     */
    public function connectTypeToStrategy($type, $strategy)
    {
        if(!is_string($type)) {
            throw new WSDL\Exception('Invalid type given to Composite Type Map.');
        }
        $this->_typeMap[$type] = $strategy;
        return $this;
    }

    /**
     * Return default strategy of this composite
     *
     * @throws \Zend\Soap\WSDL\Exception
     * @param  string $type
     * @return \Zend\Soap\WSDL\Strategy\StrategyInterface
     */
    public function getDefaultStrategy()
    {
        $strategy = $this->_defaultStrategy;
        if(is_string($strategy) && class_exists($strategy)) {
            $strategy = new $strategy;
        }
        if( !($strategy instanceof StrategyInterface) ) {
            throw new WSDL\Exception(
                'Default Strategy for Complex Types is not a valid strategy object.'
            );
        }
        $this->_defaultStrategy = $strategy;
        return $strategy;
    }

    /**
     * Return specific strategy or the default strategy of this type.
     *
     * @throws \Zend\Soap\WSDL\Exception
     * @param  string $type
     * @return \Zend\Soap\WSDL\Strategy\StrategyInterface
     */
    public function getStrategyOfType($type)
    {
        if(isset($this->_typeMap[$type])) {
            $strategy = $this->_typeMap[$type];

            if(is_string($strategy) && class_exists($strategy)) {
                $strategy = new $strategy();
            }

            if( !($strategy instanceof StrategyInterface) ) {
                throw new WSDL\Exception(
                    "Strategy for Complex Type '$type' is not a valid strategy object."
                );
            }
            $this->_typeMap[$type] = $strategy;
        } else {
            $strategy = $this->getDefaultStrategy();
        }
        return $strategy;
    }

    /**
     * Method accepts the current WSDL context file.
     *
     * @param \Zend\Soap\WSDL\WSDL $context
     */
    public function setContext(WSDL\WSDL $context)
    {
        $this->_context = $context;
        return $this;
    }

    /**
     * Create a complex type based on a strategy
     *
     * @throws \Zend\Soap\WSDL\Exception
     * @param  string $type
     * @return string XSD type
     */
    public function addComplexType($type)
    {
        if(!($this->_context instanceof WSDL\WSDL) ) {
            throw new WSDL\Exception(
                "Cannot add complex type '$type', no context is set for this composite strategy."
            );
        }

        $strategy = $this->getStrategyOfType($type);
        $strategy->setContext($this->_context);
        return $strategy->addComplexType($type);
    }
}
