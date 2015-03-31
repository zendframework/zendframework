![Logo](https://raw.githubusercontent.com/zendframework/zf2/234b554f2ca202095aea32e4fa557553f8849664/resources/ZendFramework-logo.png)

# Welcome to the *Zend Framework 2.4* Release!

Master:
[![Build Status](https://secure.travis-ci.org/zendframework/zf2.svg?branch=master)](http://travis-ci.org/zendframework/zf2)
[![Coverage Status](https://coveralls.io/repos/zendframework/zf2/badge.svg?branch=master)](https://coveralls.io/r/zendframework/zf2)
Develop:
[![Build Status](https://secure.travis-ci.org/zendframework/zf2.svg?branch=develop)](http://travis-ci.org/zendframework/zf2)
[![Coverage Status](https://coveralls.io/repos/zendframework/zf2/badge.svg?branch=develop)](https://coveralls.io/r/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.4.0*

This is the fourth minor (feature) release for the version 2 series.

31 March 2015

### UPDATES IN 2.4.0

#### BC Breaks

- [#4122](https://github.com/zendframework/zf2/pull/4122) and
  [#6613](https://github.com/zendframework/zf2/pull/6613) deprecate
  `Zend\EventManager\EventManager::triggerUntil()` and alias it to
  `trigger()`. As `triggerUntil()` now emits a deprecation notice, you may
  need to update your code; this can be done by changing the method name to
  `trigger()`.
- [#6073](https://github.com/zendframework/zf2/pull/6073)
  `Zend\Mvc\Controller\Plugin\FlashMessenger::addMessage()`, `hasMessages()`,
  `getMessages()`, `clearMessages()`, `hasCurrentMessages()`,
  `getCurrentMessages()`, and `clearCurrentMessages()` each now take one
  additional optional argument, `$namespace`. This will only affect those
  extending the class and overriding those methods, and the default value
  retains existing default behavior.
- [#6108](https://github.com/zendframework/zf2/pull/6108) Enables exception
  trace reporting by default in `Zend\Test`.
- [#6154](https://github.com/zendframework/zf2/pull/6154)
  `Zend\InputFilter\BaseInputFilter::isValid()` now has one optional argument,
  `$context`, allowing passing the context of validation along to the input
  filter during validation. This allows individual inputs to test against other
  inputs as part of validation. This change should only affect those overriding
  the `isValid()` method in an extension of `BaseInputFilter`.
- [#6151](https://github.com/zendframework/zf2/pull/6151) updates
  `Zend\Filter\Word\SeparatorToCamelCase` to properly identify non-whitespace
  characters following the separator. This should not affect most use cases.
- [#6464](https://github.com/zendframework/zf2/pull/6464) modifies the
  `autocomplete` form attribute to be a string value instead of a boolean value
  in order to follow the WHATWG HTML 5 specification. If you relied on the
  "boolean" strings "on" or "off", you may need to update your client-side code.
- [#6552](https://github.com/zendframework/zf2/pull/6552) modifies the
  `ModuleManager`'s config caching to eliminate the "double-dot" (`..`) that
  occurs in cache file names if no cache key or an empty cache key is provided.
  If you use config caching, you will need to remove your cache files before
  deployment to ensure all works correctly.
- [#6560](https://github.com/zendframework/zf2/pull/6560) modifies the
  `Zend\Cache\Storage\Adapter\AbstractAdapter::getItem()` method to ensure it
  *always* returns null in the case of a cache miss; previously, in some
  adapters, it would return another value, and, because `$success === null` is
  the check for a cache miss, register a false positive. You may have been
  mistakenly relying on this previously; test carefully.
- [#6572](https://github.com/zendframework/zf2/pull/6572) removes
  `Zend\Mvc\Service\ViewJsonRendererFactory` and
  `Zend\Mvc\Service\ViewFeedRendererFactory`, as the classes each instantiates
  have empty constructors and can be referenced as `ServiceManager` invokables.
  This change should not affect anyone.
- [#6581](https://github.com/zendframework/zf2/pull/6581)
  `Zend\Validator\Between` now raises an exception during instantiation if
  either the `min` or `max` options are missing in the provided `$options`
  array.
- [#6809](https://github.com/zendframework/zf2/pull/6809) modifies the behavior
  of `Zend\Paginator\Paginator::$firstItemCount` to equal `0` if no items are
  present; previously, it returned `1`.
- [#6817](https://github.com/zendframework/zf2/pull/6817) modifies the behavior
  of `Zend\Paginator\Paginator::getItems()` to ensure it always returns an
  array.
- [#7181](https://github.com/zendframework/zf2/pull/7181) removes code that was
  checking for cache tags in `Zend\Paginator\Paginator`'s caching support, as
  not all cache adapters support tags. This likely will not affect users.
- [#7242](https://github.com/zendframework/zf2/pull/7242) adds an optional `$hops`
  parameter to `Zend\Mvc\Controller\Plugin\FlashMessenger::addMessage()`,
  allowing you to specify the number of hops for the specific message. If you
  were extending the plugin previously and overriding this method, you will have
  to update your signature.
- [#7255](https://github.com/zendframework/zf2/pull/7255) adds the parameter
  `$data` to `Zend\Mvc\Controller\AbstractRestfulController::deleteList()`. If
  you were extending this class previously and overriding this method, you will
  need to update your signature. The `$data` parameter provides the deserialized
  request body content.
- [#7267](https://github.com/zendframework/zf2/pull/7267) modifies
  `Zend\Cache\Storage\Adapter\DbaOptions::setHandler()` to raise an exception
  if the `inifile` handler is specified, as this handler is (a) not performant
  for cache purposes, and (b) may or may not work reliably for write operations
  based on the version of libdb used.

#### Features

##### Zend\Authentication

- [#6080](https://github.com/zendframework/zf2/pull/6080) Adds
  `Zend\Authentication\AuthenticationServiceInterface` to allow substitutions.
- [#7310](https://github.com/zendframework/zf2/pull/7310) Adds
  `Zend\Authentication\Adapter\Callback`, allowing usage of arbitrary PHP
  callables for authentication purposes.
- [#7334](https://github.com/zendframework/zf2/pull/7334) Adds bcrypt support
  for HTTP Basic authentication.

##### Zend\Cache

- [#6078](https://github.com/zendframework/zf2/pull/6078) Adds a MongoDB cache
  adapter.

##### Zend\Code

- [#6339](https://github.com/zendframework/zf2/pull/6339) Adds scanning and
  generator support for PHP traits.

##### Zend\Console

- [#6646](https://github.com/zendframework/zf2/pull/6646) Adds a password prompt
  to the `Console` component.
- [#7091](https://github.com/zendframework/zf2/pull/7091) Adds a checkbox prompt
  to the `Console` component.

##### Zend\Crypt

- [#6438](https://github.com/zendframework/zf2/pull/6438) Adds
  `Zend\Crypt\FileCipher` for purposes of encrypting or decrypting a file
  using a symmetric cipher.
- [#7141](https://github.com/zendframework/zf2/pull/7141) Adds
  `Zend\Crypt\Password\BcryptSha` to allow safely pre-hashing long (&gt; 72
  byte) passwords; this functionality is opt-in.
- [#7334](https://github.com/zendframework/zf2/pull/7334) Adds bcrypt support
  for Apache password hashing.

##### Zend\Db

Many pull requests that improve code quality by abstracting common code, easing
maintenance and tightening security.

- [#5142](https://github.com/zendframework/zf2/pull/5142) Implements combine
  operators in order to allow creation of SQL statements with multiple unions
  and subselects.
- [#5505](https://github.com/zendframework/zf2/pull/5505) Support for nested
  transactions.
- [#6740](https://github.com/zendframework/zf2/pull/6740) Adds the ability to
  use aliased tables to `Zend\Db\TableGateway`.
- [#6800](https://github.com/zendframework/zf2/pull/6800) Adds the ability to
  add native predicates to queries, while respecting combine order.
- [#6890](https://github.com/zendframework/zf2/pull/6890) Adds the ability to
  specify an alternate adapter/platform when generating a SQL statement.
- [#6931](https://github.com/zendframework/zf2/pull/6931) Adds constants for
  specifying left and right outer joins to `Zend\Db\Sql\Select`.

##### Zend\Di

- [#6746](https://github.com/zendframework/zf2/pull/6746) and
  [#6747](https://github.com/zendframework/zf2/pull/6747) provide performance
  optimizations for the component.

##### Zend\Feed

- [#7125](https://github.com/zendframework/zf2/pull/7125) adds
  `Zend\Feed\Reader\ReaderImportInterface`, defining the methods `import()`,
  `importRemoteFeed()`, `importString()`, and `importFile()` common to the
  concrete `Reader` implementation.
- [#7254](https://github.com/zendframework/zf2/pull/7254) Adds
  `Zend\Feed\Reader\StandaloneExtensionManager`, which provides a
  `Zend\Feed\Reader\ExtensionManagerInterface` implementation with no
  dependencies. This is now used as the default extension manager.

##### Zend\Filter

- [#6545](https://github.com/zendframework/zf2/pull/6545) Adds
  `Zend\Filter\UpperCaseWords`, which will essentially invoke `ucwords()` on the
  provided value, or, if an alternate encoding is used, utilize
  `mb_convert_case()`.
- [#6758](https://github.com/zendframework/zf2/pull/6758) Adds the method
  `getAdapterInstance()` to `Zend\Filter\Encrypt` to allow retrieving the
  attached encryption adapter.
- [#6962](https://github.com/zendframework/zf2/pull/6962) Adds
  `Zend\Filter\Blacklist` and `Zend\Filter\Whitelist`.
- [#7104](https://github.com/zendframework/zf2/pull/7104) Adds
  `Zend\Filter\DataUnitFormatter`, which will binary and decimal numbers to
  data units (e.g., `5ki`, `6G`, etc.).
- [#7277](https://github.com/zendframework/zf2/pull/7277) Adds
  `Zend\Filter\DateSelect`, `Zend\Filter\DateTimeSelect`, and
  `Zend\Filter\MonthSelect`, and modifies the corresponding `Zend\Form` elements
  to use the appropriate filter.

##### Zend\Form

- [#6271](https://github.com/zendframework/zf2/pull/6271) Ensures all Form view
  helpers allow the full spectrum of HTML5 attributes.
- [#6656](https://github.com/zendframework/zf2/pull/6656) Adds a `getElements()`
  method to each of `DateSelect` and `MonthSelect` to return a list of each
  composed `Select` element (as each composes multiple elements).
- [#6754](https://github.com/zendframework/zf2/pull/6754) Adds a new
  `preserveDefinedOrder` flag for `Zend\Form\Annotation\AnnotationBuilder`; if
  `true`, elements will be created and listed in the order in which they were
  defined in the class parsed. [#7276](https://github.com/zendframework/zf2/pull/7276)
  adds support for the `preserve_defined_order` configuration flag to the
  `FormAnnotationBuilderFactory`.
- [#6783](https://github.com/zendframework/zf2/pull/6783) Adds 
  `@ContinueIfEmpty` as an available form annotation.
- [#7171](https://github.com/zendframework/zf2/pull/7171) modifies
  `Zend\Form\Form::prepare()` to reset (i.e., clear) password values, ensuring
  they are not displayed in rendered forms.
- [#7181](https://github.com/zendframework/zf2/pull/7181) updates
  `Zend\Form\View\Helper\FormButton` to translate the button content.

##### Zend\Http

- [#6571](https://github.com/zendframework/zf2/pull/6571) Adds the ability to
  (optionally) pass HTTP client configuration to `Zend\Http\ClientStatic`'s
  `get()` and `post()` methods.
- [#7121](https://github.com/zendframework/zf2/pull/7121) Adds the ability to
  specify the cURL `sslverifypeer` option when using the cURL adapter.
- [#7181](https://github.com/zendframework/zf2/pull/7181) Adds detection of SSL
  on reverse proxies.
- [#7259](https://github.com/zendframework/zf2/pull/7259) Modifies
  `Zend\Http\Response::setStatusCode()` such that it clears the reason phrase;
  this is done to ensure the status code and reason phrase are kept in sync.
- [#7329](https://github.com/zendframework/zf2/pull/7329) Adds support for
  Digest authentication with cURL adapters.

##### Zend\InputFilter

- [#6431](https://github.com/zendframework/zf2/pull/6431) Adds the ability to
  merge input filters to `Zend\InputFilter\BaseInputFilter`, via a new `merge()`
  method.
- [#7247](https://github.com/zendframework/zf2/pull/7247) Adds
  `Zend\InputFilter\InputFilterAbstractServiceFactory`, allowing
  configuration-driven creation of named input filter instances using the
  top-level `input_filter_specs` configuration key.

##### Zend\Log

- [#6058](https://github.com/zendframework/zf2/pull/6058) Adds a `Timestamp` log
  filter.
- [#6138](https://github.com/zendframework/zf2/pull/6138) Adds a `ReferenceId`
  processor.
- [#7294](https://github.com/zendframework/zf2/pull/7294) Adds
  `Zend\Log\Writer\Mail` for sending log messages via email.

##### Zend\Mail

- [#6570](https://github.com/zendframework/zf2/pull/6570) Adds the ability to
  set a transport envelope on the SMTP transport.

##### Zend\Mvc

- [#6246](https://github.com/zendframework/zf2/pull/6246) Adds a
  `TranslatorPluginManager` to allow modules to register their own translation
  plugins. This adds a new `translator_plugins` top-level configuration key,
  using the standard service manager configuration scheme.
- [#6615](https://github.com/zendframework/zf2/pull/6615)
  `Zend\Mvc\Controller\AbstractController` now also registers any implemented
  interfaces as identifiers with the composed `EventManager` instance.
- [#6951](https://github.com/zendframework/zf2/pull/6951) Adds the ability to
  override `display_exceptions` and `display_not_found_reason` specifically for
  the console view manager.
- [#7240](https://github.com/zendframework/zf2/pull/7240) Adds
  `Zend\Mvc\HttpMethodListener`, which is also now registered by default; you
  can specify via the `http_methods_listener` top-level configuration key an
  array of HTTP methods your application will allow, and this listener will
  return early if the current method is not present in that list.
- [#7336](https://github.com/zendframework/zf2/pull/7336) Adds the ability to
  specify that the controller in the route match should be used instead of the
  controller class name for purposes of creating the template string.

##### Zend\Navigation

- [#7245](https://github.com/zendframework/zf2/pull/7245) Adds
  `Zend\Navigation\NavigationAbstractServiceFactory`, allowing
  configuration-driven creation of named `Navigation` instances using the
  `navigation` top-level configuration key.

##### Zend\Paginator

- [#5518](https://github.com/zendframework/zf2/pull/5518) Allow specifying a
  custom query for determining the item count in the `DbSelect` adapter.
- [#7122](https://github.com/zendframework/zf2/pull/7122) Adds
  `Zend\Paginator\PaginatorIterator`, to allow iterating an entire paginated set
  at once.

##### Zend\Permissions

- [#7327](https://github.com/zendframework/zf2/pull/7327) Adds
  `Zend\Permissions\Rbac\Assertion\Callback`, allowing usage of arbitrary PHP
  callables for providing RBAC assertions.
- [#7328](https://github.com/zendframework/zf2/pull/7328) Adds
  `Zend\Permissions\Acl\Assertion\Callback`, allowing usage of arbitrary PHP
  callables for providing ACL assertions.

##### Zend\ServiceManager

- [#6175](https://github.com/zendframework/zf2/pull/6175) adds
  `Zend\ServiceManager\MutableCreationOptionsTrait` to simplify implementing
  `MutableCreationOptionsInterface`.

##### Zend\Stdlib

- [#6091](https://github.com/zendframework/zf2/pull/6091) Adds
  `Zend\Stdlib\Hydrator\NamingStrategy\MapNamingStrategy`, which allows
  creating a map of `object property => serialized property` names.
- [#6194](https://github.com/zendframework/zf2/pull/6194) Adds
  `Zend\Stdlib\Hydrator\Strategy\StrategyChain`, to allow chaining multiple
  strategies together to mutate a value during hydration and/or extraction; this
  is similar to filter chains, only for hydrators.
- [#6227](https://github.com/zendframework/zf2/pull/6227) Adds
  `Zend\Stdlib\Hydrator\Strategy\ExplodeStrategy`, which can explode a value
  using a provided delimiter on hydration, and implode with the delimter on
  extraction.
- [#6289](https://github.com/zendframework/zf2/pull/6289) Adds
  `Zend\Stdlib\Hydrator\Strategy\DateTimeFormatterStrategy`, which will
  hydrate a formatted date-time string to a `DateTime` instance, or serialize a
  `DateTime` value to the given string format.
- [#6367](https://github.com/zendframework/zf2/pull/6367) Adds
  `Zend\Stdlib\Hydrator\Strategy\CompositeNamingStrategy`, which allows
  providing a set of naming strategies to compare a key against, with a default
  naming strategy to use if unmatched. The main purpose is to be able to use a
  single strategy for multiple properties.
- [#6523](https://github.com/zendframework/zf2/pull/6523) Adds
  `Zend\Stdlib\Hydrator\Strategy\BooleanStrategy`, providing a way to specify
  "boolean" strings or integers that can be cast to booleans, and vice versa on
  extraction.
- [#6894](https://github.com/zendframework/zf2/pull/6894) Adds
  `Zend\Stdlib\Hydrator\DelegatingHydrator`. This hydrator will attempt to
  find a hydrator registered in the `HydratorPluginManager` using the name of
  the class to be hydrated or extracted.
- [#6899](https://github.com/zendframework/zf2/pull/6899) and
  [#6903](https://github.com/zendframework/zf2/pull/6903) add the ability to
  remove and/or replace keys, respectively, when calling
  `Zend\Stdlib\ArrayUtils::merge()`.
- [#7315](https://github.com/zendframework/zf2/pull/7315) adds
  `Zend\Stdlib\ArrayUtils::filter()`, which is a shim for supporting PHP's
  `array_filter()` function, and, specifically, the 3rd `$flag` argument added
  in PHP 5.6 for filtering on key or both key and value.

##### Zend\Uri

- [#6886](https://github.com/zendframework/zf2/pull/6886) Adds the ability to
  specify the `user-info` for a URI as a single string, while ensuring that both
  `user` and `password` are still accessible.

##### Zend\Validator

- [#6496](https://github.com/zendframework/zf2/pull/6496) Implements a
  `PriorityQueue` for `Zend\Validator\ValidatorChain`, adding an optional
  `$priority` argument to each of `attach()`, `addValidator()`, and
  `attachByName()`.
- [#6496](https://github.com/zendframework/zf2/pull/6496) Modifies
  `Zend\Validator\ValidatorChain::attach()` to allow accepting a PHP callable as
  the validator argument, omitting the need to wrap it in a `Zend\Validator\Callback`.
- [#6678](https://github.com/zendframework/zf2/pull/6678) Adds
  `Zend\Validator\Timezone`.

##### Zend\View

- [#6196](https://github.com/zendframework/zf2/pull/6196) Adds
  `Zend\View\Resolver\RelativeFallbackResolver`, which allows resolving a
  template based on the location of the template requesting the render. As an
  example, if you call `$this->render('foo');` from within a template referenced
  as `foo-bar/baz-bat/quz`, this new resolver _can_ resolve it to
  `foo-bar/baz-bat/foo`.
- [#6709](https://github.com/zendframework/zf2/pull/6709) Adds
  `Zend\View\Helper\HtmlTag`, for generating the opening and closing `<html>`
  tag, along with attributes and namespaces.

#### Misc

- [#6903](https://github.com/zendframework/zf2/pull/6903) Adds the ability to
  specify extensions to scan when using `bin/templatemap_generator.php`.
- Updated to PHPUnit 4 and php-cs-fixer 1.
- Updated the framework and all components to specify
  [PSR-4](http://www.php-fig.org/psr/psr-4/) autoloading rules (instead of
  PSR-0, which is now deprecated).
- Added PHP 7 nightly as an optional build target; tests currently pass.

Please see [CHANGELOG.md](CHANGELOG.md).

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.23 or later; we recommend using the
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

## Reporting Potential Security Issues

If you have encountered a potential security vulnerability in Zend Framework, please report it to us at [zf-security@zend.com](mailto:zf-security@zend.com). We will work with you to verify the vulnerability and patch it.

When reporting issues, please provide the following information:

- Component(s) affected
- A description indicating how to reproduce the issue
- A summary of the security vulnerability and impact

We request that you contact us via the email address above and give the project contributors a chance to resolve the vulnerability and issue a new release prior to any public exposure; this helps protect Zend Framework users and provides them with a chance to upgrade and/or update in order to protect their applications.

For sensitive email communications, please use [our PGP key](http://framework.zend.com/zf-security-pgp-key.asc).

### LICENSE

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in [LICENSE.txt](LICENSE.txt).

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the [contributors](https://github.com/zendframework/zf2/contributors) to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
