<?php

namespace Zend\Db\Sql\Predicate;

abstract class AbstractPredicate implements \Zend\Db\DbPreparable, \Zend\Db\Sql\PreparableStatement
{
    protected $db = null;
    protected $subject = null;
    
    public function __construct($subject = null)
    {
        (empty($subject)) ?: $this->setSubject($subject);
    }
    
    public function setDb(\Zend\Db\Db $db)
    {
        $this->db = $db;
        return $this;
    }
    
    public function platform()
    {
        
    }
    
    public function getSubject()
    {
        return $this->subject;
    }
    
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }
    
}
