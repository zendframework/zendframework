If you wish to contribute to Zend Framework, please be sure to
read/subscribe to the following resources:

 * ZF2 Requirements:
   http://framework.zend.com/wiki/display/ZFDEV2/Zend+Framework+2.0+Requirements

 * Coding Standards:
   http://framework.zend.com/manual/en/coding-standard.html

 * ZF Git Guide:
   README-GIT.txt

 * Contributor's Guide:
   http://framework.zend.com/wiki/display/ZFDEV/Contributing+to+Zend+Framework

 * ZF Contributor's mailing list:
   Archives: http://zend-framework-community.634137.n4.nabble.com/ZF-Contributor-f680267.html
   Subscribe: zf-contributors-subscribe@lists.zend.com

 * ZF Contributor's IRC channel:
   #zftalk.dev on Freenode.net

If you are working on new features, or refactoring an existing
component, please create a proposal. You can do this in on the proposals
page, http://framework.zend.com/wiki/display/ZFPROP/Home. 

RUNNING TESTS
=============
The full test suite currently does not run! This is due to some
components not yet being migrated to namespaces, as well as to some
issues we've encountered in refactoring.

To run tests:

 * Make sure you have a recent version of PHPUnit installed; 3.5.0
   minimally.

 * Enter the tests/ subdirectory.

 * Execute PHPUnit, providing a path to a component directory for which
   you wish to run tests, or a specific test class file.

   % phpunit ZendTest/Application
   % phpunit ZendTest/Application/Resource/CacheManagerTest.php

 * You may also provide the "--group" switch; in such cases, provide the
   top-level component name:

   % phpunit --group Zend_Application

   This will likely lead to errors, so it's usually best to specify a
   specific component in which to run test:

   % phpunit --group ZF-XYZ Zend/Application

You can turn on conditional tests with the TestConfiguration.php file.
To do so:

 * Enter the tests/ subdirectory.

 * Copy TestConfiguration.php.dist file to TestConfiguration.php
 
 * Edit TestConfiguration.php to enable any specific functionality you
   want to test, as well as to provide test values to utilize.
