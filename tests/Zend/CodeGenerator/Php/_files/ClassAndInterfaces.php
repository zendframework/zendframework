<?php

class Zend_CodeGenerator_Php_ClassWithInterface implements Zend_Code_Generator_Php_OneInterface, Zend_Code_Generator_Php_TwoInterface
{

}

class Zend_CodeGenerator_Php_NewClassWithInterface extends Zend_CodeGenerator_Php_ClassWithInterface implements Zend_Code_Generator_Php_ThreeInterface
{

}

interface Zend_Code_Generator_Php_OneInterface
{

}

interface Zend_Code_Generator_Php_TwoInterface
{

}

interface Zend_Code_Generator_Php_ThreeInterface
{

}