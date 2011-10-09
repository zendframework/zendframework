<?php
namespace Zend\Module;

interface Installable
{
    /**
     * get details of what this module provides
     * 
     * @return array
     */
    public function getProvides();

    /**
     * get current version of Module
     * 
     * @return float|string
     */
    public function getVersion();

    /**
     * run a first installation
     * 
     * @return bool if install was sucessful
     */
    public function install();

    /**
     * run an inremental upgrade
     * 
     * @return bool if upgrade was sucessful
     **/
    public function upgrade();

}