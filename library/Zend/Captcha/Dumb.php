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
 * @package    Zend_Captcha
 * @subpackage Adapter
 */

namespace Zend\Captcha;

/**
 * Example dumb word-based captcha
 *
 * Note that only rendering is necessary for word-based captcha
 *
 * @todo       This likely needs its own validation since it expects the word entered to be the strrev of the word stored.
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage Adapter
*/
class Dumb extends AbstractWord
{
    /**
     * CAPTCHA label
     * @type string
     */
    protected $label = 'Please type this word backwards';

    /**
     * Set the label for the CAPTCHA
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Retrieve the label for the CAPTCHA
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Retrieve optional view helper name to use when rendering this captcha
     * 
     * @return string
     */
    public function getHelperName()
    {
        return 'captcha/dumb';
    }
}
