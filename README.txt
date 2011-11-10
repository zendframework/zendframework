Welcome to the Zend Framework 2.0.0 Release! 

RELEASE INFORMATION
---------------
Zend Framework 2.0.0beta1

THIS RELEASE IS A DEVELOPMENT RELEASE AND NOT INTENDED FOR PRODUCTION USE.
PLEASE USE AT YOUR OWN RISK.

This is the first in a series of planned beta releases. The beta release
cycle will follow the "gmail" style of betas, whereby new features will
be added in each new release, and BC will not be guaranteed; beta
releases will happen no less than every six weeks. 

Once the established milestones have been reached and the featureset has
reached maturity and reasonable stability, we will freeze the API and
prepare for Release Candidate status.

NEW FEATURES
------------

- New and refactored autoloaders:
  - Zend\Loader\StandardAutoloader
  - Zend\Loader\ClassMapAutoloader
  - Zend\Loader\AutoloaderFactory
- New plugin broker strategy
  - Zend\Loader\Broker and Zend\Loader\PluginBroker
- Reworked Exception system
  - Allow catching by specific Exception type
  - Allow catching by component Exception type
  - Allow catching by SPL Exception type
  - Allow catching by base Exception type
- Rewritten Session component
- Refactored View component
  - Split helpers into a PluginBroker
  - Split variables into a Variables container
  - Split script paths into a TemplateResolver
  - Renamed base View class "PhpRenderer"
  - Refactored helpers to utilize __invoke() when possible
- Refactored HTTP component
- New Zend\Cloud\Infrastructure component
- New EventManager component
- New Dependency Injection (Zend\Di) component
- New Code component
  - Incorporates refactored versions of former Reflection and
    CodeGenerator components.
  - Introduces Scanner component.
  - Introduces annotation system.
- New MVC layer
  - Zend\Module, for developing modular application architectures.
  - Zend\Mvc, a completely reworked MVC layer built on top of HTTP,
    EventManager, and Di.
- Introduces new packaging system, allowing the usage of Pyrus
  (http://pear2.php.net) to install individual components and/or groups
  of components.

SYSTEM REQUIREMENTS
-------------------

Zend Framework 2 requires PHP 5.3 or later; we recommend using the
latest PHP version whenever possible.

INSTALLATION
------------

Please see INSTALL.txt.

CONTRIBUTING
------------

If you wish to contribute to Zend Framework 2.0, please read both the
README-DEV.txt and README-GIT.txt file.

QUESTIONS AND FEEDBACK
----------------------

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in the Zend
Framework issue tracker at:

http://framework.zend.com/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
fw-announce-subscribe@lists.zend.com.

LICENSE
-------

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in LICENSE.txt.

ACKNOWLEDGEMENTS
----------------

The Zend Framework team would like to thank all the contributors to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
