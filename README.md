![Logo](https://raw.githubusercontent.com/zendframework/zf2/234b554f2ca202095aea32e4fa557553f8849664/resources/ZendFramework-logo.png)

# Welcome to the *Zend Framework 3.0* Release!

## RELEASE INFORMATION

*Zend Framework 3.0.0dev*

This is the third major release for Zend Framework.

DD MMM YYYY

### UPDATES IN 3.0.0

Please see [CHANGELOG.md](CHANGELOG.md).

### SYSTEM REQUIREMENTS

Zend Framework 3 requires PHP 5.6 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

We no longer recommend installing this package directly. The package is a
metapackage that aggregates all components (and/or integrations) originally
shipped with Zend Framework; in most cases, you will want a subset, and these
may be installed separately; see https://docs.zendframework.com/ for a list of
available packages and installation instructions for each.

The primary use case for installing the entire framework is when upgrading from
a version 2 release.

If you decide you still want to install the entire framework:

```console
$ composer require zendframework/zendframework
```

#### GETTING STARTED

A great place to get up-to-speed quickly is the Zend Framework
QuickStart:

- https://docs.zendframework.com/tutorials/getting-started/overview/

The QuickStart covers some of the most commonly used components of ZF.
Since Zend Framework is designed with a use-at-will architecture and
components are loosely coupled, you can select and use only those
components that are needed for your project.

### COMPONENTS

This package is a metapackage aggregating the following components:

- [zendframework/zend-authentication](https://github.com/zendframework/zendframework/zend-authentication)
- [zendframework/zend-barcode](https://github.com/zendframework/zendframework/zend-barcode)
- [zendframework/zend-cache](https://github.com/zendframework/zendframework/zend-cache)
- [zendframework/zend-captcha](https://github.com/zendframework/zendframework/zend-captcha)
- [zendframework/zend-code](https://github.com/zendframework/zendframework/zend-code)
- [zendframework/zend-config](https://github.com/zendframework/zendframework/zend-config)
- [zendframework/zend-console](https://github.com/zendframework/zendframework/zend-console)
- [zendframework/zend-crypt](https://github.com/zendframework/zendframework/zend-crypt)
- [zendframework/zend-db](https://github.com/zendframework/zendframework/zend-db)
- [zendframework/zend-debug](https://github.com/zendframework/zendframework/zend-debug)
- [zendframework/zend-di](https://github.com/zendframework/zendframework/zend-di)
- [zendframework/zend-diactoros](https://github.com/zendframework/zendframework/zend-diactoros)
- [zendframework/zend-dom](https://github.com/zendframework/zendframework/zend-dom)
- [zendframework/zend-escaper](https://github.com/zendframework/zendframework/zend-escaper)
- [zendframework/zend-eventmanager](https://github.com/zendframework/zendframework/zend-eventmanager)
- [zendframework/zend-feed](https://github.com/zendframework/zendframework/zend-feed)
- [zendframework/zend-file](https://github.com/zendframework/zendframework/zend-file)
- [zendframework/zend-filter](https://github.com/zendframework/zendframework/zend-filter)
- [zendframework/zend-form](https://github.com/zendframework/zendframework/zend-form)
- [zendframework/zend-http](https://github.com/zendframework/zendframework/zend-http)
- [zendframework/zend-hydrator](https://github.com/zendframework/zendframework/zend-hydrator)
- [zendframework/zend-i18n](https://github.com/zendframework/zendframework/zend-i18n)
- [zendframework/zend-i18n-resources](https://github.com/zendframework/zendframework/zend-i18n-resources)
- [zendframework/zend-inputfilter](https://github.com/zendframework/zendframework/zend-inputfilter)
- [zendframework/zend-json](https://github.com/zendframework/zendframework/zend-json)
- [zendframework/zend-json-server](https://github.com/zendframework/zendframework/zend-json-server)
- [zendframework/zend-loader](https://github.com/zendframework/zendframework/zend-loader)
- [zendframework/zend-log](https://github.com/zendframework/zendframework/zend-log)
- [zendframework/zend-mail](https://github.com/zendframework/zendframework/zend-mail)
- [zendframework/zend-math](https://github.com/zendframework/zendframework/zend-math)
- [zendframework/zend-memory](https://github.com/zendframework/zendframework/zend-memory)
- [zendframework/zend-mime](https://github.com/zendframework/zendframework/zend-mime)
- [zendframework/zend-modulemanager](https://github.com/zendframework/zendframework/zend-modulemanager)
- [zendframework/zend-mvc](https://github.com/zendframework/zendframework/zend-mvc)
- [zendframework/zend-mvc-console](https://github.com/zendframework/zendframework/zend-mvc-console)
- [zendframework/zend-mvc-form](https://github.com/zendframework/zendframework/zend-mvc-form)
- [zendframework/zend-mvc-i18n](https://github.com/zendframework/zendframework/zend-mvc-i18n)
- [zendframework/zend-mvc-plugins](https://github.com/zendframework/zendframework/zend-mvc-plugins)
- [zendframework/zend-navigation](https://github.com/zendframework/zendframework/zend-navigation)
- [zendframework/zend-paginator](https://github.com/zendframework/zendframework/zend-paginator)
- [zendframework/zend-permissions-acl](https://github.com/zendframework/zendframework/zend-permissions-acl)
- [zendframework/zend-permissions-rbac](https://github.com/zendframework/zendframework/zend-permissions-rbac)
- [zendframework/zend-progressbar](https://github.com/zendframework/zendframework/zend-progressbar)
- [zendframework/zend-psr7bridge](https://github.com/zendframework/zendframework/zend-psr7bridge)
- [zendframework/zend-serializer](https://github.com/zendframework/zendframework/zend-serializer)
- [zendframework/zend-server](https://github.com/zendframework/zendframework/zend-server)
- [zendframework/zend-servicemanager](https://github.com/zendframework/zendframework/zend-servicemanager)
- [zendframework/zend-servicemanager-di](https://github.com/zendframework/zendframework/zend-servicemanager-di)
- [zendframework/zend-session](https://github.com/zendframework/zendframework/zend-session)
- [zendframework/zend-soap](https://github.com/zendframework/zendframework/zend-soap)
- [zendframework/zend-stdlib](https://github.com/zendframework/zendframework/zend-stdlib)
- [zendframework/zend-stratigility](https://github.com/zendframework/zendframework/zend-stratigility)
- [zendframework/zend-tag](https://github.com/zendframework/zendframework/zend-tag)
- [zendframework/zend-test](https://github.com/zendframework/zendframework/zend-test)
- [zendframework/zend-text](https://github.com/zendframework/zendframework/zend-text)
- [zendframework/zend-uri](https://github.com/zendframework/zendframework/zend-uri)
- [zendframework/zend-validator](https://github.com/zendframework/zendframework/zend-validator)
- [zendframework/zend-view](https://github.com/zendframework/zendframework/zend-view)
- [zendframework/zend-xml2json](https://github.com/zendframework/zendframework/zend-xml2json)
- [zendframework/zend-xmlrpc](https://github.com/zendframework/zendframework/zend-xmlrpc)
- [zendframework/zendxml](https://github.com/zendframework/zendframework/zendxml)

### CONTRIBUTING

If you wish to contribute to Zend Framework, please read the
[CONTRIBUTING.md](CONTRIBUTING.md) and [CONDUCT.md](CONDUCT.md) files.

### QUESTIONS AND FEEDBACK

Online documentation can be found at https://docs.zendframework.com/.
Questions that are not addressed in the manual should be directed to the
relevant repository, as linked above.

If you find code in this release behaving in an unexpected manner or
contrary to its documented behavior, please create an issue with the relevant
repository, as linked above.

## Reporting Potential Security Issues

If you have encountered a potential security vulnerability in Zend Framework,
please report it to us at [zf-security@zend.com](mailto:zf-security@zend.com).
We will work with you to verify the vulnerability and patch it.

When reporting issues, please provide the following information:

- Component(s) affected
- A description indicating how to reproduce the issue
- A summary of the security vulnerability and impact

We request that you contact us via the email address above and give the project
contributors a chance to resolve the vulnerability and issue a new release prior
to any public exposure; this helps protect Zend Framework users and provides
them with a chance to upgrade and/or update in order to protect their
applications.

For sensitive email communications, please use
[our PGP key](http://framework.zend.com/zf-security-pgp-key.asc).

### LICENSE

The files in this archive are released under the Zend Framework license.
You can find a copy of this license in [LICENSE.md](LICENSE.md).

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the
[contributors](https://github.com/zendframework/zendframework/contributors) to
the Zend Framework project; our corporate sponsor, Zend Technologies / Rogue
Wave Software; and you, the Zend Framework user.

Please visit us sometime soon at http://framework.zend.com.
