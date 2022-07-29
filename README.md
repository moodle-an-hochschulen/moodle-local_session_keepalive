moodle-local_session_keepalive
==============================

[![Moodle Plugin CI](https://github.com/moodle-an-hochschulen/moodle-local_session_keepalive/workflows/Moodle%20Plugin%20CI/badge.svg?branch=master)](https://github.com/moodle-an-hochschulen/moodle-local_session_keepalive/actions?query=workflow%3A%22Moodle+Plugin+CI%22+branch%3Amaster)

Moodle plugin which checks for unsent user input in the browser and keeps the user session alive as long as needed to prevent that user input which is sent to the server after the user session has expired will be lost


Requirements
------------

This plugin requires Moodle 4.0+


Motivation for this plugin
--------------------------

Moodle has a built-in session timeout which is described on https://docs.moodle.org/en/Session_handling#Timeout. The documentation tells you about the timeout:

> If users don't load a new page during the amount of time set here, Moodle will end their session and log them out. Be sure this time frame is long enough to cover the longest test your teachers may offer. If a student is logged out while they are taking a test, their responses to the test questions may be lost.

Having read that, as an admin, you are in the difficult situation of, on the one hand, setting the timeout long enough to cover all cases when users input content into a Moodle page without reloading the page for a long time (for example taking a huge quiz or writing a long forum post) and, on the other hand, of setting the timeout short enough to prevent account abuse if a user forgets to log out of Moodle on a public computer.

To solve this dilemma, this plugin adds a keepalive check to the Moodle page which checks for unsent user input in the browser and keeps the user session alive as long as needed even if there is no full page load.


Installation
------------

Install the plugin like any other plugin to folder
/local/session_keepalive

See http://docs.moodle.org/en/Installing_plugins for details on installing Moodle plugins


Usage & Settings
----------------

After installing the plugin, it does not do anything to Moodle yet.

To configure the plugin and its behaviour, please visit:
Site administration -> Server -> Session keepalive.

There, you find two sections:

### 1. General functionality

With the "Enable functionality" setting, you can enable session keepalive. As long as session keepalive is not enabled, this plugin does not do anything and user sessions expire normally.

And with the "Keepalive interval" setting, you can set the interval (in minutes) in which the users' browsers will check for unsent user input and keep the session alive if needed. Even if this request is very lightweight on the server side, you shouldn't set the interval shorter as necessary because unnecessary short intervals, together with a large amount of concurrent active users, might generate perceivable additional load on your server. For normal Moodle setups, the default setting should be fine.

### 2. Advanced settings

With the "Keepalive weekdays" and "Keepalive start/end time" settings, you can control the weekdays and the daytime when session keepalive should be active. By default, session keepalive will be active 24/7. However, if you are sure that you will never need session keepalive on certain weekdays or daytimes, you can limit session keepalive weekdays and daytimes to save the load on the server side.


Capabilities
------------

This plugin does not add any additional capabilities.


How this plugin works
---------------------

To keep the user session alive, this plugin adds a lightweight piece of JavaScript to the Moodle page which sets a dirty flag as soon as the user changed a textarea, input or select element on the page. Additionally, the JavaScript code checks in the configured interval if the dirty flag is set and, if yes, asks then the Moodle server to keep the user session alive.

To be honest, there is already a similar mechanism in Moodle core on /lib/yui/src/checknet which keeps the session alive only on a certain gradebook page (/grade/grading/form/guide/edit.php) and which has a server-side AJAX script in /lib/sessionkeepalive_ajax.php. This plugin does not re-use the existing client-side code (especially because it is still written in YUI and because it always keeps the session alive regardless if really needed or not), but it re-uses the existing server-side AJAX script to keep the session alive.

Additionally, the functionality of this plugin is achieved by abusing the *_extend_navigation() hook which allows plugin developers to extend Moodle's global navigation tree at runtime, but can also be abused to insert HTML code to each Moodle page. Please note that due to the way this plugin is built, session keepalive is active on as much Moodle pages as possible, but not on really all Moodle pages - for example, a SCORM activity instance opened in a new window will never use session keepalive.


Theme support
-------------

This plugin is developed and tested on Moodle Core's Boost theme.
It should also work with Boost child themes, including Moodle Core's Classic theme. However, we can't support any other theme than Boost.


Plugin repositories
-------------------

This plugin is published and regularly updated in the Moodle plugins repository:
http://moodle.org/plugins/view/local_session_keepalive

The latest development version can be found on Github:
https://github.com/moodle-an-hochschulen/moodle-local_session_keepalive


Bug and problem reports / Support requests
------------------------------------------

This plugin is carefully developed and thoroughly tested, but bugs and problems can always appear.

Please report bugs and problems on Github:
https://github.com/moodle-an-hochschulen/moodle-local_session_keepalive/issues

We will do our best to solve your problems, but please note that due to limited resources we can't always provide per-case support.


Feature proposals
-----------------

Due to limited resources, the functionality of this plugin is primarily implemented for our own local needs and published as-is to the community. We are aware that members of the community will have other needs and would love to see them solved by this plugin.

Please issue feature proposals on Github:
https://github.com/moodle-an-hochschulen/moodle-local_session_keepalive/issues

Please create pull requests on Github:
https://github.com/moodle-an-hochschulen/moodle-local_session_keepalive/pulls

We are always interested to read about your feature proposals or even get a pull request from you, but please accept that we can handle your issues only as feature _proposals_ and not as feature _requests_.


Moodle release support
----------------------

Due to limited resources, this plugin is only maintained for the most recent major release of Moodle as well as the most recent LTS release of Moodle. Bugfixes are backported to the LTS release. However, new features and improvements are not necessarily backported to the LTS release.

Apart from these maintained releases, previous versions of this plugin which work in legacy major releases of Moodle are still available as-is without any further updates in the Moodle Plugins repository.

There may be several weeks after a new major release of Moodle has been published until we can do a compatibility check and fix problems if necessary. If you encounter problems with a new major release of Moodle - or can confirm that this plugin still works with a new major release - please let us know on Github.

If you are running a legacy version of Moodle, but want or need to run the latest version of this plugin, you can get the latest version of the plugin, remove the line starting with $plugin->requires from version.php and use this latest plugin version then on your legacy Moodle. However, please note that you will run this setup completely at your own risk. We can't support this approach in any way and there is an undeniable risk for erratic behavior.


Translating this plugin
-----------------------

This Moodle plugin is shipped with an english language pack only. All translations into other languages must be managed through AMOS (https://lang.moodle.org) by what they will become part of Moodle's official language pack.

As the plugin creator, we manage the translation into german for our own local needs on AMOS. Please contribute your translation into all other languages in AMOS where they will be reviewed by the official language pack maintainers for Moodle.


Right-to-left support
---------------------

This plugin has not been tested with Moodle's support for right-to-left (RTL) languages.
If you want to use this plugin with a RTL language and it doesn't work as-is, you are free to send us a pull request on Github with modifications.


Maintainers
-----------

The plugin is maintained by\
Moodle an Hochschulen e.V.


Copyright
---------

The copyright of this plugin is held by\
Moodle an Hochschulen e.V.

Individual copyrights of individual developers are tracked in PHPDoc comments and Git commits.


Initial copyright
-----------------

This plugin was initially built, maintained and published by\
Ulm University\
Communication and Information Centre (kiz)\
Alexander Bias

It was contributed to the Moodle an Hochschulen e.V. plugin catalogue in 2022.
