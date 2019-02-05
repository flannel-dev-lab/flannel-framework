#Flannel Core Framework

## Overview
The Flannel Core Framework has been an internal project for years being primarily used to rapidly deploy websites and REST APIs without the overhead.

*Please note - we are currently in the process of making this a public framework, so please bear with us over the next few weeks as we pull everything together.*

## Quick Setup
After downloading the framework, enter the conf directory and copy conf.php.template to conf.php, then edit the file to point to one of the configuration files. This is to support any number of configurations. Typically, we copy the dev directory to the local username and poing conf.php to it. After that, edit the conf file inside to match your settings. You should be all set to go at that point.

#### Please note - the framework expects HTTPS and it will cause a redirect loop if not. You can disable HTTPS, but it is not recommended. If cost is a concern, please check out [CloudFlare](http://www.cloudflare.com), which will include a free edge certificate as well as free origin certificates.

## Keeping your codebase updated
1. <code>git remote add flannel git@github.com:flannel-dev-lab/flannel-framework.git</code>
2. <code>git fetch flannel</code>
3. <code>git checkout -b framework flannel/master</code>
4. <code>git checkout master</code>
5. <code>git merge framework</code>
6. <code>git add -A</code>
7. <code>git commit -m "Merging Flannel Core Framework"</code>
8. <code>git push origin master</code>

Now, when you need to pull the most recent version, you just need to run the following:
1. <code>git checkout framework</code>
2. <code>git pull framework master</code>
3. <code>git checkout master</code>
4. <code>git merge framework</code>
5. <code>git push origin master</code>

## Web Request Data Flow
All web requests (aside from existing files, like images or css) route through www/index.php. Once a web request is received, the following steps are taken to respond to the request:
1. bootstrap.php is loaded
    * Environment variables from env.php are set
    * Autoloaders are set up
    * Configuration files are loaded
    * Error handlers are loaded
2. vendor/Flannel/Core/Router.php is instantiated
    * The default error response is loaded
    * A decision between website vs API is determined (configuration setting)
3. vendor/Flannel/Core/Router/Standard.php (or Api) is instantiated
    * The Controller is determined from the URI path
        * Standard: /CONTROLLER/MODULE/ACTION
            * Everything after the action turns into Input key/value pairs
        * Api: /VERSION/RESOURCE/RESOURCE_ID/SUBRESOURCE/SUBRESOURCE_ID
    * The corresponding Controller is loaded
        * The naming convention follows the directory structure
            * ex: /contact/sales/send will load app/Controller/Contact/Sales.php and run the method named `send`

## Using Models
Each Model is named according to the database table name. For example, a table called `user` would have a corresponding model found at /app/Model/User.php. To use the model, call `$user = (new Model_User())->load($id)`. The load method also takes an array of key/value pairs which are added to the where clause.
