### Welcome to the *Zend Framework 2.0* Release!

Master: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=master)](http://travis-ci.org/zendframework/zf2)
Develop: [![Build Status](https://secure.travis-ci.org/zendframework/zf2.png?branch=develop)](http://travis-ci.org/zendframework/zf2)

## RELEASE INFORMATION

*Zend Framework 2.0.4*

This is the fourth maintenance release for the 2.0 series.

20 Nov 2012

### UPDATES IN 2.0.4

*Security Changes*

By default, the JsonStrategy and FeedStrategy were selecting their
associated renderers based on two criteria: if a ViewModel of
appropriate type was present, *OR* if the Accept header matched certain
criteria. It was pointed out that this latter is undesirable when the
strategies are enabled globally, as any matching route could be forced
to return JSON or a feed -- and potentially expose information not meant
for that particular format, or raise exceptions due to containing
content not compatible with the format.

In this release, we removed the Accept header detection. To mitigate
this, however, a new controller plugin, AcceptableViewModelSelector, was
added. This plugin may be invoked from a controller, and based on
criteria passed to it, return an appropriate view model type based on
matching the Accept header. As an example:

```php
class SomeController extends AbstractActionController
{
    protected $acceptCriteria = array(
        'Zend\View\Model\JsonModel' => array(
            'application/json',
        ),
        'Zend\View\Model\FeedModel' => array(
            'application/rss+xml',
        ),
    );

    public function apiAction()
    {
        $viewModel = $this->acceptableViewModelSelector($this->acceptCriteria);
        
        // Potentially vary execution based on model returned
        if ($viewModel instanceof JsonModel) {
            // ...
        }
    }
}
```

You will still enable the JsonStrategy or FeedStrategy at the global
level, but they will only be selected now if an appropriate view model
is returned by the controller; the above plugin can help you select the
appropriate view model based on Accept header on an as-needed basis.

For more changes, please see CHANGELOG.md.

### SYSTEM REQUIREMENTS

Zend Framework 2 requires PHP 5.3.3 or later; we recommend using the
latest PHP version whenever possible.

### INSTALLATION

Please see INSTALL.md.

### CONTRIBUTING

If you wish to contribute to Zend Framework 2.0, please read both the
CONTRIBUTING.md and README-GIT.md file.

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
You can find a copy of this license in LICENSE.txt.

### ACKNOWLEDGEMENTS

The Zend Framework team would like to thank all the [contributors](https://github.com/zendframework/zf2/contributors) to the Zend
Framework project, our corporate sponsor, and you, the Zend Framework user.
Please visit us sometime soon at http://framework.zend.com.
