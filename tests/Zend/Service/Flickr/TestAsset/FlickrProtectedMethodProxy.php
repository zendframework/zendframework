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
 * @package    Zend_Service_Flickr
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Flickr\TestAsset;

use Zend\Service\Flickr\Flickr;

/**
 * @category   Zend
 * @package    Zend_Service_Flickr
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Flickr
 */
class FlickrProtectedMethodProxy extends Flickr
{
    public function proxyValidateUserSearch(array $options)
    {
        $this->validateUserSearch($options);
    }

    public function proxyValidateTagSearch(array $options)
    {
        $this->validateTagSearch($options);
    }

    public function proxyValidateGroupPoolGetPhotos(array $options)
    {
        $this->validateGroupPoolGetPhotos($options);
    }

    public function proxyCompareOptions(array $options, array $validOptions)
    {
        $this->compareOptions($options, $validOptions);
    }
}
