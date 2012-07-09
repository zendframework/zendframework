<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace Zend\Dojo\Form\Decorator;

/**
 * AccordionContainer
 *
 * Render a dijit AccordionContainer
 *
 * @package    Zend_Dojo
 * @subpackage Form_Decorator
 */
class AccordionContainer extends DijitContainer
{
    /**
     * View helper
     * @var string
     */
    protected $_helper = 'AccordionContainer';
}
