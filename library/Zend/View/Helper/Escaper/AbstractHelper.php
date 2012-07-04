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
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Helper\Escaper;

use Zend\View\Helper;
use Zend\View\Exception;
use Zend\Escaper;

/**
 * Helper for escaping values
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractHelper extends Helper\AbstractHelper
{

	/**
	 * @var Escaper\Escaper
	 */
	protected $escaper = null;

	/**
     * @var string Encoding
     */
    protected $encoding = 'UTF-8';

	public function setEscaper(Escaper\Escaper $escaper)
	{
		$this->escaper = $escaper;
		$this->encoding = $escaper->getEncoding();
		return $this;
	}

	public function getEscaper()
	{
		if (null === $this->escaper) {
			$this->setEscaper(new Escaper\Escaper($this->getEncoding()));
		}
		return $this->escaper;
	}

	/**
     * Set the encoding to use for escape operations
     * 
     * @param  string $encoding 
     * @return AbstractEscaper
     */
    public function setEncoding($encoding)
    {
    	if (!is_null($this->escaper)) {
    		throw new Exception\InvalidArgumentException(
    			'Encoding cannot be changed once a Zend\Escaper\Escaper object has been'
    			. ' instantiated by or injected into this Helper.'
    		);
    	}
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Get the encoding to use for escape operations
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

}