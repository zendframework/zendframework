<?php

namespace ZendTest\Acl\TestAsset\UseCase1;

class Acl extends \Zend\Acl\Acl
{

    public $customAssertion = null;

    public function __construct()
    {
        $this->customAssertion = new UserIsBlogPostOwnerAssertion();

        $this->addRole(new \Zend\Acl\Role\GenericRole('guest'));                    // $acl->addRole('guest');
        $this->addRole(new \Zend\Acl\Role\GenericRole('contributor'), 'guest');     // $acl->addRole('contributor', 'guest');
        $this->addRole(new \Zend\Acl\Role\GenericRole('publisher'), 'contributor'); // $acl->addRole('publisher', 'contributor');
        $this->addRole(new \Zend\Acl\Role\GenericRole('admin'));                    // $acl->addRole('admin');
        $this->addResource(new \Zend\Acl\Resource\GenericResource('blogPost'));     // $acl->addResource('blogPost');
        $this->allow('guest', 'blogPost', 'view');
        $this->allow('contributor', 'blogPost', 'contribute');
        $this->allow('contributor', 'blogPost', 'modify', $this->customAssertion);
        $this->allow('publisher', 'blogPost', 'publish');
    }
}
