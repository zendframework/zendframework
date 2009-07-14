<?php

require_once 'Zend/Feed/Reader.php';

require_once 'Zend/Feed/Reader/Extension/EntryAbstract.php';

class My_FeedReader_Extension_JungleBooks_Entry extends Zend_Feed_Reader_Extension_EntryAbstract
{

    public function getIsbn()
    {
        if (isset($this->_data['isbn'])) {
            return $this->_data['isbn'];
        }
        $isbn = $this->_xpath->evaluate('string(' . $this->getXpathPrefix() . '/jungle:isbn)');
        if (!$isbn) {
            $isbn = null;
        }
        $this->_data['isbn'] = $title;
        return $this->_data['isbn'];
    }

    protected function _registerNamespaces()
    {
        $this->_xpath->registerNamespace('jungle', 'http://example.com/junglebooks/rss/module/1.0/');
    }
}
