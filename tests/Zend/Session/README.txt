The most recently published version of Zend_Session documentation:
http://framework.zend.com/wiki/x/iVc

Comments may be added by readers to each wiki page, by first registering
for a wiki/issue tracker account, and then emailing your username to
cla@zend.com with a request to enable posting privileges.

To run the unit tests for Zend_Session*, use a CLI version of PHP:

$ cd /path/to/zend_framework/tests/Zend/Session
$ php AllTests.php

Simulation of multiple, sequential requests required the use of exec() using the
CLI version of PHP.  Additionally, issues discussed on the headers_sent() manual
page also pose issues when trying to combine multiple test suites and avoid
problems associated with output buffering, and headers "already sent".

If you would like to help implement a solution, please start here:
http://framework.zend.com/issues/browse/ZF-700
