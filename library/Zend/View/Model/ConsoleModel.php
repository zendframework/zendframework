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
 * @package    Zend_View
 * @subpackage Model
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Model;


/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Model
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ConsoleModel extends ViewModel
{
    const RESULT = 'result';

    /**
     * Console output doesn't support containers.
     *
     * @var string
     */
    protected $captureTo = null;

    /**
     * Console output should always be terminal.
     *
     * @var bool
     */
    protected $terminate = true;

    /**
     * Set error level to return after the application ends.
     *
     * @param int $errorLevel
     */
    public function setErrorLevel($errorLevel)
    {
        $this->options['errorLevel'] = $errorLevel;
    }

    /**
     * @return int
     */
    public function getErrorLevel()
    {
        if (array_key_exists('errorLevel', $this->options)) {
            return $this->options['errorLevel'];
        }
    }

    /**
     * Set result text.
     *
     * @param string  $text
     * @return \Zend\View\Model\ConsoleModel
     */
    public function setResult($text)
    {
        $this->setVariable(self::RESULT, $text);
        return $this;
    }

    /**
     * Get result text.
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->getVariable(self::RESULT);
    }
}
