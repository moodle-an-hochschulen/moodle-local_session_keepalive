moodle-local_session_keepalive
==============================

Changes
-------

### v3.9-r1

* 2020-09-01 - Fixed broken Behat scenario due to upstream changes for modal dialogues.
* 2020-09-01 - Prepare compatibility for Moodle 3.9.

### v3.8-r1

* 2020-02-26 - Fix a behat test and added some details to the plugin settings page after the 'Extend session' popup was introduced upstream in MDL-34498.
* 2020-02-26 - Ship the simple Ajax endpoint sessionkeepalive_ajax.php with this plugin as it was removed upstream in MDL-34498.
* 2020-02-26 - Prepare compatibility for Moodle 3.8.

### v3.7-r1

* 2019-08-05 - Added Behat tests.
* 2019-08-05 - Prepare compatibility for Moodle 3.7.

### v3.6-r1

* 2019-01-23 - Check compatibility for Moodle 3.6, no functionality change.
* 2018-12-05 - Changed travis.yml due to upstream changes.

### v3.5-r1

* 2018-06-01 - Check compatibility for Moodle 3.5, no functionality change.

### v3.4-r2

* 2018-05-16 - Implement Privacy API.

### v3.4-r1

* 2017-12-21 - Check compatibility for Moodle 3.4, no functionality change.

### v3.3-r1

* 2017-12-12 - Check compatibility for Moodle 3.3, no functionality change.
* 2017-12-05 - Added Workaround to travis.yml for fixing Behat tests with TravisCI.
* 2017-11-08 - Updated travis.yml to use newer node version for fixing TravisCI error.

### v3.2-r4

* 2017-06-30 - Improvement: Add really short keepalive intervals - Credits to Alexey Lustin

### v3.2-r3

* 2017-05-19 - Bugfix: String in language pack didn't work for Moodle installed in subdirectories - Credits to David Mudrák
* 2017-05-29 - Add Travis CI support

### v3.2-r2

* 2017-05-19 - Bugfix: Plugin didn't work for Moodle installed in subdirectories - Credits to David Mudrák

### v3.2-r1

* 2017-04-25 - Initial version
