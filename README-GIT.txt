CLONING THE REPOSITORY
======================
Anonymous cloning may be done from:

    git://git.zendframework.com/zf2test.git

If you have commit rights, you will use the following:

    ssh://git@git.zendframework.com:21652/zf2test.git

You must make sure your author email is specified exactly as it appears in JIRA.
To do so, do the following in your git repository:

    git config user.email <your email address>

If it does not match, any commits made with you as an author will be rejected.

If you are unsure of what email address is tied to your JIRA account, visit the
following site:

    http://framework.zend.com/crowd

and log in with your JIRA credentials. Click the "My Profile" link in the upper
right corner of the page. You will then see your email address listed.

CONTRIBUTORS AND COMMITTERS
===========================
For the immediate future, and until we create a community process team, only the
Zend team will be committers. If you have a patch or feature-set you wish to
have incorporated into the repository, please issue a pull request to a
committer. A pull request may be done by using git's "git-send-email"
functionality, or by sending a request to a committer indicating the URL of your
repository, the branch that should be pulled, and/or the specific revision(s) to
pull.
