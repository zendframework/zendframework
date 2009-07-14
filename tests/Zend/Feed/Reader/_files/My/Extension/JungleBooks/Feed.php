<?php

require_once 'Zend/Feed/Reader/Extension/FeedAbstract.php';

class My_FeedReader_Extension_JungleBooks_Feed extends Zend_Feed_Reader_Extension_FeedAbstract
{

    public function getDaysPopularBookLink()
    {
        if (isset($this->_data['dayPopular'])) {
            return $this->_data['dayPopular'];
        }
        $dayPopular = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/jungle:dayPopular)');
        if (!$dayPopular) {
            $dayPopular = null;
        }
        $this->_data['dayPopular'] = $dayPopular;
        return $this->_data['dayPopular'];
    }

    protected function _registerNamespaces()
    {
        $this->_xpath->registerNamespace('jungle', 'http://example.com/junglebooks/rss/module/1.0/');
    }
}
