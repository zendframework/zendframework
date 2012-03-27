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

use Zend\Feed\Writer\Feed,
    Zend\Feed\Writer\FeedFactory;

/**
 * Marker view model for indicating feed data.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Model
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FeedModel extends ViewModel
{
    /**
     * @var Feed
     */
    protected $feed;

    /**
     * @var false|string
     */
    protected $type = false;

    public function getFeed()
    {
        if ($this->feed instanceof Feed) {
            return $this->feed;
        }

        if (!$this->type) {
            $options   = $this->getOptions();
            if (isset($options['feed_type'])) {
                $this->type = $options['feed_type'];
            }
        }

        $variables = $this->getVariables();
        $feed      = FeedFactory::factory($variables);
        $this->setFeed($feed);

        return $this->feed;
    }

    /**
     * Set the feed object
     * 
     * @param  Feed $feed 
     * @return FeedModel
     */
    public function setFeed(Feed $feed)
    {
        $this->feed = $feed;
        return $this;
    }

    /**
     * Get the feed type
     * 
     * @return false|string
     */
    public function getFeedType()
    {
        if ($this->type) {
            return $this->type;
        }

        $options   = $this->getOptions();
        if (isset($options['feed_type'])) {
            $this->type = $options['feed_type'];
        }
        return $this->type;
    }
}
