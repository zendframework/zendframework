test

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

FEEDS AND EMAILS
================
RSS feeds may be found at:

    http://git.zendframework.com/feeds/<branch>.xml

where <branch> is a branch in the repository.

To subscribe to git email notifications, send an email to:

    zf-git-subscribe@lists.zend.com

You will need to reply to the verification email sent to you by this list.

CONTRIBUTORS AND COMMITTERS
===========================
For the immediate future, and until we create a community process team, only the
Zend team will be committers. If you have a patch or feature-set you wish to
have incorporated into the repository, please issue a pull request to a
committer. A pull request may be done by using git's "git-send-email"
functionality, or by sending a request to a committer indicating the URL of your
repository, the branch that should be pulled, and/or the specific revision(s) to
pull.

If you are a contributor, and accept changes from a non-CLA'd developer, you
need to do the following:

 * Verify that the developer is granting you permission to commit the code to
   Zend Framework. If possible, get this permission in writing.

 * If you do not get verification, do not incorporate the code in your own
   branches.

 * Once you have merged the code into your branches, you need to commit it using
   the "-s" or "--signoff" switch to "git commit". The ZF pre-receive hook
   rejects any commits from non-CLA'd authors unless there is a sign-off message
   in the commit.
