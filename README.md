#Flannel Core Framework

## Overview
The Flannel Core Framework has been an internal project for years being primarily used to rapidly deploy websites and REST APIs without the overhead.

## Quick Setup
Easy install script is in the works

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

## Credits
Admin UI: https://github.com/ColorlibHQ/AdminLTE/releases/tag/v3.1.0
