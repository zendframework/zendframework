![Logo](https://raw.githubusercontent.com/zendframework/zf2/234b554f2ca202095aea32e4fa557553f8849664/resources/ZendFramework-logo.png)

# Welcome to the *Zend Framework 3.0* Release!

## RELEASE INFORMATION

*Zend Framework 3.0.1dev*

This is the first maintenance release for the Zend Framework 3 series.

DD MMM YYYY

### UPDATES IN 3.0.1

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

We recommend using either the zend-mvc skeleton application:

```bash
$ composer create-project zendframework/skeleton-application project
```

or the Expressive skeleton application:

```bash
$ composer create-project zendframework/zend-expressive-skeleton project
```

The primary use case for installing the entire framework is when upgrading from
a version 2 release.

If you decide you still want to install the entire framework:

```console
$ composer require zendframework/zendframework
```

#### GETTING STARTED

A great place to get up-to-speed quickly is the [Zend Framework
QuickStart](https://docs.zendframework.com/tutorials/getting-started/overview/).

The QuickStart covers some of the most commonly used components of ZF.
Since Zend Framework is designed with a use-at-will architecture and
components are loosely coupled, you can select and use only those
components that are needed for your project.

#### MIGRATION

For detailed information on migration from v2 to v3, please [read our Migration
Guide](https://docs.zendframework.com/tutorials/migration/to-v3/overview/).

### COMPONENTS

This package is a metapackage aggregating the following components:

- [zend-authentication](https://github.com/zendframework/zend-authentication)
- [zend-barcode](https://github.com/zendframework/zend-barcode)
- [zend-cache](https://github.com/zendframework/zend-cache)
- [zend-captcha](https://github.com/zendframework/zend-captcha)
- [zend-code](https://github.com/zendframework/zend-code)
- [zend-config](https://github.com/zendframework/zend-config)
- [zend-console](https://github.com/zendframework/zend-console)
- [zend-crypt](https://github.com/zendframework/zend-crypt)
- [zend-db](https://github.com/zendframework/zend-db)
- [zend-debug](https://github.com/zendframework/zend-debug)
- [zend-di](https://github.com/zendframework/zend-di)
- [zend-diactoros](https://github.com/zendframework/zend-diactoros)
- [zend-dom](https://github.com/zendframework/zend-dom)
- [zend-escaper](https://github.com/zendframework/zend-escaper)
- [zend-eventmanager](https://github.com/zendframework/zend-eventmanager)
- [zend-feed](https://github.com/zendframework/zend-feed)
- [zend-file](https://github.com/zendframework/zend-file)
- [zend-filter](https://github.com/zendframework/zend-filter)
- [zend-form](https://github.com/zendframework/zend-form)
- [zend-http](https://github.com/zendframework/zend-http)
- [zend-hydrator](https://github.com/zendframework/zend-hydrator)
- [zend-i18n](https://github.com/zendframework/zend-i18n)
- [zend-i18n-resources](https://github.com/zendframework/zend-i18n-resources)
- [zend-inputfilter](https://github.com/zendframework/zend-inputfilter)
- [zend-json](https://github.com/zendframework/zend-json)
- [zend-json-server](https://github.com/zendframework/zend-json-server)
- [zend-loader](https://github.com/zendframework/zend-loader)
- [zend-log](https://github.com/zendframework/zend-log)
- [zend-mail](https://github.com/zendframework/zend-mail)
- [zend-math](https://github.com/zendframework/zend-math)
- [zend-memory](https://github.com/zendframework/zend-memory)
- [zend-mime](https://github.com/zendframework/zend-mime)
- [zend-modulemanager](https://github.com/zendframework/zend-modulemanager)
- [zend-mvc](https://github.com/zendframework/zend-mvc)
- [zend-mvc-console](https://github.com/zendframework/zend-mvc-console)
- [zend-mvc-form](https://github.com/zendframework/zend-mvc-form)
- [zend-mvc-i18n](https://github.com/zendframework/zend-mvc-i18n)
- [zend-mvc-plugins](https://github.com/zendframework/zend-mvc-plugins)
- [zend-navigation](https://github.com/zendframework/zend-navigation)
- [zend-paginator](https://github.com/zendframework/zend-paginator)
- [zend-permissions-acl](https://github.com/zendframework/zend-permissions-acl)
- [zend-permissions-rbac](https://github.com/zendframework/zend-permissions-rbac)
- [zend-progressbar](https://github.com/zendframework/zend-progressbar)
- [zend-psr7bridge](https://github.com/zendframework/zend-psr7bridge)
- [zend-serializer](https://github.com/zendframework/zend-serializer)
- [zend-server](https://github.com/zendframework/zend-server)
- [zend-servicemanager](https://github.com/zendframework/zend-servicemanager)
- [zend-servicemanager-di](https://github.com/zendframework/zend-servicemanager-di)
- [zend-session](https://github.com/zendframework/zend-session)
- [zend-soap](https://github.com/zendframework/zend-soap)
- [zend-stdlib](https://github.com/zendframework/zend-stdlib)
- [zend-stratigility](https://github.com/zendframework/zend-stratigility)
- [zend-tag](https://github.com/zendframework/zend-tag)
- [zend-test](https://github.com/zendframework/zend-test)
- [zend-text](https://github.com/zendframework/zend-text)
- [zend-uri](https://github.com/zendframework/zend-uri)
- [zend-validator](https://github.com/zendframework/zend-validator)
- [zend-view](https://github.com/zendframework/zend-view)
- [zend-xml2json](https://github.com/zendframework/zend-xml2json)
- [zend-xmlrpc](https://github.com/zendframework/zend-xmlrpc)
- [zendxml](https://github.com/zendframework/zendxml)

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
