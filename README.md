### Welcome to the *Zend Framework 2.2* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)
Develop: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=develop)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.2.0*

This is the second minor (feature) release for the version 2 series.

15 May 2013

### UPDATES IN 2.2.0

- **Addition of many more plugin managers and abstract service factories.**
  In order to simplify usage of the `ServiceManager` as an 
  [Inversion of Control](http://en.wikipedia.org/wiki/Inversion_of_Control)
  container, as well as to provide more flexibility in and consistency in how various
  framework components are consumed, a number of plugin managers and service factories
  were created and enabled. 

  Among the various plugin managers created are Translator loader manager, a Hydrator
  plugin manager (allowing named hydrator instances), and an InputFilter manager.

  New factories include a Translator service factory, and factories for 
  both the Session configuration and SessionManager.
    
  New abstract factories include one for the DB component (allowing you to manage
  multiple named adapters), Loggers (for having multiple Logger instances),
  Cache storage (for managing multiple cache backends), and Forms (which makes use
  of the existing FormElementsPluginManager, as well as the new Hydrator and InputFilter
  plugin managers).

- **Data Definition Language (DDL) support in Zend\Db.** DDL 
  provides the ability to create, alter, and drop tables in a relational 
  database system. Zend\Db now offers abstraction around DDL, and 
  specifically MySQL and ANSI SQL-92; we will gradually add this 
  capability for the other database vendors we support.

- **Addition of Zend\Stdlib\Hydrator\Aggregate.** The AggregateHydrator provides
  an event-driven way to hydrate/extract hierarchical structures, as well as for
  mapping entities to hydrators for general purpose use.

- **Simplification of dependencies in Zend\Feed.** We either removed or made
  optional several dependencies in Zend\Feed, making it easier to use standalone
  and/or with third-party components. We plan a larger story around this for
  2.3.0.

- **Authentication:** The DB adapter now supports non-RDBMS credential validation.

- **Cache:** New storage backend: Redis.

- **Code:** The ClassGenerator now has a removeMethod() method.

- **Console:** Incremental improvements to layout and colorization of banners
  and usage messages; fixes for how literal and non-literal matches are
  returned.

- **DB:** New DDL support (noted earlier); many incremental improvements.

- **Di:** Improvements around the handling of Aware interfaces to prevent
  attempts to instantiate interfaces when preferences have not been provided.

- **Filter:** New DateTimeFormatter filter.

- **Form:** Many incremental improvements to selected elements; new
  FormAbstractServiceFactory for defining form services; minor improvements to
  make the form component work with the DI service factory.

- **InputFilter**: new CollectionInputFilter for working with form Collections;
  new InputFilterPluginManager providing integration and services for the
  ServiceManager.

- **I18n:** We removed ext/intl as a hard requirement, and made it only a
  suggested requirement; the Translator has an optional dependency on the
  EventManager, providing the ability to tie into "missing message" and "missing
  translations" events; new country-specific PhoneNumber validator.

- **ModuleManager:** Now allows passing actual Module instances (not just names).

- **Navigation:** Incremental improvements, particularly to URL generation.

- **MVC:** You can now configure the initial set of MVC event listeners in the
  configuration file; the MVC stack now detects generic HTTP responses when
  detecting event short circuiting; the default ExceptionStrategy now allows
  returning JSON; opt-in translatable segment routing; many incremental
  improvements to the AbstractRestfulController to make it more configurable and
  extensible; the Forward plugin was refactored to no longer require a
  ServiceLocatorAware controller, and instead receive the ControllerManager via
  its factory.

- **Paginator:** Support for TableGateway objects.

- **ServiceManager:** Incremental improvements; performance optimizations;
  delegate factories, which provide a way to write factories for objects that
  replace a service with a decorator; "lazy" factories, allowing the ability to
  delay factory creation invocation until the moment of first use.

- **Stdlib:** Addition of a HydratorAwareInterface; creation of a
  HydratorPluginManager.

- **SOAP:** Major refactor of WSDL generation to make it more maintainable.

- **Validator:** New Brazilian IBAN format for IBAN validator; validators now
  only return unique error messages; improved Maestro detection in CreditCard
  validator.

- **Version:** use the ZF website API for finding the latest version, instead of
  GitHub.

- **View:** Many incremental improvements, primarily to helpers; deprecation of
  the Placeholder Registry and removal of it from the implemented placeholder
  system; new explicit factory classes for helpers that have collaborators
  (making them easier to override/replace).

Please see [CHANGELOG.md](CHANGELOG.md).

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

Please see [INSTALL.md](INSTALL.md).

### CONTRIBUTING

If you wish to contribute to Zend Framework, please read both the
[CONTRIBUTING.md](CONTRIBUTING.md) and [README-GIT.md](README-GIT.md) file.

### QUESTIONS AND FEEDBACK

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/archives/subscribe/

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in our GitHub
issue tracker:

https://github.com/zendframework/zf2/issues

If you would like to be notified of new releases, you can subscribe to
the fw-announce mailing list by sending a blank message to
<fw-announce-subscribe@lists.zend.com>.

### LICENSE

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in [LICENSE.txt](LICENSE.txt).

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the [contributors](https://github.com/zendframework/zf2/contributors) to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
