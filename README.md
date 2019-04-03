# JIRA API Automaton

## Installation

PHP dependencies are managed via Composer and are committed into this
repository because they're deployed to the server via the repository.

This is only runnable on terminal.

#### Composer Installation
~~~
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
~~~

You can then install the project dependencies using the following command: 
~~~
composer install
~~~

### Before Starting
Create a permission scheme for the archive folders.
You can visit : http://{JIRA HOST URI}/secure/admin/ViewPermissionSchemes.jspa

#### Env File
Note: ARCHIVED_PERMISSION_SCHEME_ID can be found in the uri of permission scheme section.
(e.g http://{JIRA HOST URI}/secure/admin/EditPermissions!default.jspa?schemeId=10100)

~~~
JIRA_HOST="{JIRA HOST URI}"
JIRA_USER=""
JIRA_PASS=""

ARCHIVED_PERMISSION_SCHEME_ID=
~~~

#### Run script
To create project in jira:
~~~
php create.php
~~~

To archive project in jira
~~~
php archive.php
~~~

**Enjoy !**