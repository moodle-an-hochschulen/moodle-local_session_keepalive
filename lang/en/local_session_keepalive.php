<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Local plugin "Session keepalive" - Language pack
 *
 * @package   local_session_keepalive
 * @copyright 2017 Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Session keepalive';
$string['privacy:metadata'] = 'The session keepalive plugin provides extended functionality to Moodle users, but does not store any personal data.';
$string['setting_advancedsettingsheading'] = 'Advanced settings';
$string['setting_enable'] = 'Enable session keepalive';
$string['setting_enable_desc'] = 'With this setting, you can enable session keepalive. As long as session keepalive is not enabled, this plugin does not do anything and user sessions expire normally. This means that user input which is sent to the server after the user session has expired will be lost. As soon as this feature is enabled, the plugin will keep the session alive as long as needed.';
$string['setting_generalfunctionalityheading'] = 'General functionality';
$string['setting_keepaliveend'] = 'Keepalive end time';
$string['setting_keepaliveend_desc'] = 'With these two settings, you can control the daytime (relating to server time) when session keepalive should be active. If you set both settings to 0:00, session keepalive will be active during the whole day which is also the default. However, if you are sure that you will only want session keepalive during a defined time slot, you can limit session keepalive to this time slot to save the load on the server side during the rest of the day.';
$string['setting_keepaliveinterval'] = 'Keepalive interval';
$string['setting_keepaliveinterval_desc'] = 'With this setting, you can set the interval (in minutes) in which the users\' browsers will check for unsent user input and keep the session alive if needed. Even if this request is very lightweight on the server side, you shouldn\'t set the interval shorter as necessary because unnecessary short intervals, together with a large amount of concurrent active users, might generate perceivable additional load on your server. For normal Moodle setups, the default setting should be fine.<br>
The interval is best set to half of the session timeout configured on the {$a->page} configuration page (currently set to {$a->minutes} minutes). Example: If the session timeout is set to 120 minutes, set the keepalive time to 60 minutes. The users\' browsers will then perform the first keepalive request 60 minutes after the page load which is still plenty of time before the session normally expires.';
$string['setting_keepaliveintervalpopupnote'] = 'Please note: From Moodle 3.8 on, Moodle core shows a popup to the user after 90% of his session lifetime has been reached to give him the possibility to extend his session. This plugin can nicely co-exist with this core functionality as long as you make sure that you set this setting to a considerable lower value than 90% of the configured session lifetime (which means less than {$a->minutes} minutes).';
$string['setting_keepalivestart'] = 'Keepalive start time';
$string['setting_keepaliveweekdays'] = 'Keepalive weekdays';
$string['setting_keepaliveweekdays_desc'] = 'With this setting, you can control the weekdays when session keepalive should be active. By default, all weekdays are enabled. However, if you are sure that you will never need session keepalive on weekends or certain working days, you can disable these weekdays to save the load on the server side on these days.';
