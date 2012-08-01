### Welcome to the *Zend Framework 2.0.0* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.0.0rc2*

This is the second release candidate for 2.0.0. We will be releasing RCs
on a weekly basis until we feel all critical issues are addressed. At
this time, we anticipate no API changes before the stable release, and
recommend testing your production applications against it.

01 August 2012

### UPDATES IN RC2

 - REALLY removed Zend\Markup from the repository (we reported it
   removed for RC1, and had in fact created the repository for it, but
   not removed it from the zf2 repository).
 - Addition of Hydrator strategies, to allow easier hydration of
   composed objects. The HydratorInterface remains unchanged, but the
   shipped hydrators now all implement both that and the new
   StrategyEnabledInterface.
 - Zend\View\Model\ViewModel::setVariables() no longer overwrites the
   internal variables container by default. If you wish to do so, it
   does provide an optional $overwrite argument; passing a boolean true
   will cause the method to overwrite the container.
 - Zend\Validator\Iban was expanded to include Single Euro Payments Area
   (SEPA) support
 - Zend\Mvc\Controller\ControllerManager now allows fetching controllers
   via DI. This is done via a new DiStrict abstract service factory,
   which only fetches services in a provided whitelist.
 - Zend\Json\Encoder now accepts IteratorAggregates.
 - Controller, Filter, and Validator plugin managers were fixed to no
   longer share instances.
 - Zend\Form was updated to only bind values that were actually provided
   in the data. Additionally, if a Collection has no entries, it will be
   removed from the validation group. Finally, elements with the name
   "0" (zero) are now allowed.
 - Zend\View\Helper\Doctype was updated to respond true to isRdfa() when
   the doctype is an HTML5 variant.
 - Zend\Navigation was fixed to ensure the navigation services is passed
   correctly between helpers. Additionally, a bug in Mvc::isActive() was
   fixed to ensure routes were properly seeded.
 - The GreaterThan and LessThan validators were updated to pass
   constructor arguments to the parents, for consistency with other
   validators.
 - Log formatters are now responsible for formatting DateTime values in
   the log events.
 - The Console ViewManager was updated to extend from the standard HTTP
   version, and to use Config instead of Configuration, fixing several
   minor issues.
 - Zend\Version was moved to Zend\Version\Version (for consistency)
 - Zend\Debug was moved to Zend\Debug\Debug (for consistency)
 - All protected members that still had underscore prefixes were
   refactored to remove that prefix.
 - Identified and fixed all CS issues as identified by php-cs-fixer, and
   added php-cs-fixer to the Travis-CI build tasks.
 - ServiceManagerAwareInterface was removed from all but the most
   necessary locations, and replaced with ServiceLocatorAwareInterface.
 - Zend\Feed\Reader, Zend\Dom, Zend\Serializer\Wddx, and Zend\Soap were
   not properly protecting against XXE injections; these situations have
   now been corrected.

Around *70* pull requests for a variety of features and bugfixes were handled
since beta5!

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

Please see [INSTALL.md](INSTALL.md).

### CONTRIBUTING

If you wish to contribute to Zend Framework 2.0, please read both the
[README-DEV.md](README-DEV.md) and [README-GIT.md](README-GIT.md) file.

### QUESTIONS AND FEEDBACK

Online documentation can be found at http://framework.zend.com/manual.
Questions that are not addressed in the manual should be directed to the
appropriate mailing list:

http://framework.zend.com/wiki/display/ZFDEV/Mailing+Lists

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue in the Zend
Framework issue tracker at:

http://framework.zend.com/issues/browse/ZF2

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
