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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Model;

use Traversable;
use Zend\Json\Json;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Model
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class JsonModel extends ViewModel
{
    /**
     * JSON probably won't need to be captured into a 
     * a parent container by default.
     * 
     * @var string
     */
    protected $captureTo = null;

    /**
     * JSONP callback (if set, wraps the return in a function call)
     * 
     * @var string
     */
    protected $jsonpCallback = null;

    /**
     * JSON is usually terminal
     * 
     * @var bool
     */
    protected $terminate = true;

    /**
     * Set the JSONP callback function name
     * 
     * @param  string $callback 
     * @return JsonModel
     */
    public function setJsonpCallback($callback)
    {
        $this->jsonpCallback = $callback;
        return $this;
    }

    /**
     * Serialize to JSON
     * 
     * @return string
     */
    public function serialize()
    {
        $variables = $this->getVariables();
        if ($variables instanceof Traversable) {
            $variables = ArrayUtils::iteratorToArray($variables);
        }

        if(!is_null($this->jsonpCallback))
        {
            return $this->jsonpCallback.'('.Json::encode($variables).');';
        } else {
            return Json::encode($variables);
        }
    }
}
