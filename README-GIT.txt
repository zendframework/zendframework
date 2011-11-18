USING THE GIT REPOSITORY
========================

Initial Setup
-------------

First, make sure you know the email address associated with your JIRA
credentials. All commits pushed to the master repository are checked
against these addresses, so your repository will need to be configured
to use that address. The following will give you that information:

 * Visit the ZF Crowd install: http://framework.zend.com/crowd

 * Log in, if you aren't.

 * Find the "My Profile" link in the upper right of the page, and follow
   it.

 * The resulting page will display your profile, including the _email_
   address with which you are registered. Make a note of it.

Setup your own public repository
--------------------------------

Your next step is to establish a public repository from which we can
pull your work into the master repository. You have two options: use
github or other public site, or setup/use your own repository.

  Option 1: GitHub
  ----------------

  * Setup a GitHub account (http://github.com/), if you haven't yet

  * Fork the ZF2 respository (http://github.com/zendframework/zf2)

  * Clone your fork locally and enter it (use your own GitHub username
    in the statement below)

      % git clone git@github.com:<username>/zf2.git
      % cd zf2

  * Configure git to use the email address with which you are registered
    in JIRA:

      % git config user.email <your email address>

  * Add a remote to the canonical ZF repository, so you can keep your fork
    up-to-date:

      % git remote add zf2 https://github.com/zendframework/zf2.git
      - AND -
      % git fetch zf2

  Option 2: Personal Repository
  -----------------------------

  We assume you will use gitosis (http://progit.org/book/ch4-7.html) or gitolite
  (http://progit.org/book/ch4-8.html) to host your own repository.  If
  you go this route, we will assume you have the knowledge to do so, or
  know where to obtain it. We will not assist you in setting up such a
  repository.

  * Create a new repository

      % git init

  * Configure git to use the email address with which you are registered
    in JIRA:

      % git config user.email <your email address>

  * Add an "origin" remote pointing to your gitosis/gitolite repo:

      % git remote add origin git://yourdomain/yourrepo.git

  * Add a remote for the ZF repository and fetch it

      % git remote add zf2 https://github.com/zendframework/zf2.git
      % git fetch zf2

  * Create a new branch for the ZF repository (named "zf/master" here)

      % git branch -b zf/master zf2/master

  * Create your master branch off the ZF branch, and push to your
    repository

      % git branch -b master
      % git push origin HEAD:master

Keeping Up-to-Date
------------------

Periodically, you should update your fork or personal repository to
match the canonical ZF repository. In each of the above setups, we have
added a remote to the Zend Framework repository, which allows you to do
the following:


  % git checkout master
  % git pull zf2 master
  - OPTIONALLY, to keep your remote up-to-date -
  % git push origin

Working on Zend Framework
-------------------------

When working on Zend Framework, we recommend you do each new feature or
bugfix in a new branch. This simplifies the task of code review as well
as of merging your changes into the canonical repository.

A typical work flow will then consist of the following:

  * Create a new local branch based off your master branch.

  * Switch to your new local branch. (This step can be combined with the
    previous step with the use of "git checkout -b".)

  * Do some work, commit, repeat as necessary.

  * Push the local branch to your remote repository.

  * Send a pull request.

The mechanics of this process are actually quite trivial. Below, we will
create a branch for fixing an issue in the tracker.

  % git checkout -b zf9295
  Switched to a new branch 'zf9295'
  
  ... do some work ...
  
  % git commit
  
  ... write your log message ...
  
  % git push origin HEAD:zf9295
  Counting objects: 38, done.
  Delta compression using up to 2 threads.
  Compression objects: 100% (18/18), done.
  Writing objects: 100% (20/20), 8.19KiB, done.
  Total 20 (delta 12), reused 0 (delta 0)
  To ssh://git@github.com/weierophinney/zf2.git
     b5583aa..4f51698  HEAD -> master


To send a pull request, you have two options.

If using GitHub, you can do the pull request from there. Navigate to
your repository, select the branch you just created, and then select the
"Pull Request" button in the upper right. Select the user
"zendframework" as the recipient.

If using your own repository - or even if using GitHub - you can send an
email indicating you have changes to pull:

  * Send to mailto:zf-devteam@zend.com

  * In your message, specify:
    * The URL to your repository (e.g., "git://mwop.net/zf2.git")
    * The branch containing the changes you want pulled (e.g., "zf9295")
    * The nature of the changes (e.g., "implements
      Zend_Service_Twitter", "fixes ZF-9295", etc.)

Branch Cleanup
--------------

As you might imagine, if you are a frequent contributor, you'll start to
get a ton of branches both locally and on your remote.

Once you know that your changes have been accepted to the master
repository, we suggest doing some cleanup of these branches.

  * Local branch cleanup

      % git branch -d <branchname>

  * Remote branch removal

      % git push origin :<branchname>


FEEDS AND EMAILS
================
RSS feeds may be found at:

    https://github.com/zendframework/zf2/commits/<branch>.atom

where <branch> is a branch in the repository.

To subscribe to git email notifications, send an email to:

    zf-git-subscribe@lists.zend.com

You will need to reply to the verification email sent to you by this
list.

Should you wish to filter emails from the list, they will use the
"subject" line of commit messages, preceded by "[branch] ", and come
from "zf-git@lists.zend.com".

CONTRIBUTORS AND COMMITTERS
===========================
For the immediate future, and until we create a community process team,
only the Zend team will be committers. If you have a patch or
feature-set you wish to have incorporated into the repository, please
issue a pull request to a committer. A pull request may be done by using
git's "git-send-email" functionality, or by sending a request to a
committer indicating the URL of your repository, the branch that should
be pulled, and/or the specific revision(s) to pull.

