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
 * Local plugin "Session keepalive" - Settings
 *
 * @package   local_session_keepalive
 * @copyright 2017 Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

if ($hassiteconfig) {
    // Create new settings page.
    $page = new admin_settingpage('local_session_keepalive',
            get_string('pluginname', 'local_session_keepalive', null, true));

    if ($ADMIN->fulltree) {
        // Add general functionality heading.
        $page->add(new admin_setting_heading('local_session_keepalive/generalfunctionalityheading',
                get_string('setting_generalfunctionalityheading', 'local_session_keepalive', null, true),
                ''));

        // Create enable control widget.
        $page->add(new admin_setting_configcheckbox('local_session_keepalive/enable',
                get_string('setting_enable', 'local_session_keepalive', null, true),
                get_string('setting_enable_desc', 'local_session_keepalive', null, true),
                0));

        // Create keepalive interval control widget.
        $choices = array(1 => 1, 2 => 2, 5 => 5, 10 => 10, 30 => 30, 60 => 60, 90 => 90,
                120 => 120, 180 => 180, 240 => 240, 300 => 300);
        $sessionhandlingurl = new \moodle_url('/admin/settings.php', array('section' => 'sessionhandling'));
        $link = \html_writer::link($sessionhandlingurl, get_string('sessionhandling', 'core_admin'));
        $sessiontimeoutmin = ($CFG->sessiontimeout / 60);
        $page->add(new admin_setting_configselect('local_session_keepalive/keepaliveinterval',
                get_string('setting_keepaliveinterval', 'local_session_keepalive', null, true),
                get_string('setting_keepaliveinterval_desc',
                        'local_session_keepalive',
                        array('minutes' => $sessiontimeoutmin, 'page' => $link),
                        true).
                '<br /><br />'.
                get_string('setting_keepaliveintervalpopupnote',
                        'local_session_keepalive',
                        array('minutes' => floor($sessiontimeoutmin * 0.9)),
                        true),
                60,
                $choices));
        unset($choices);

        // Add advanced settings heading.
        $page->add(new admin_setting_heading('local_session_keepalive/advancedsettingsheading',
                get_string('setting_advancedsettingsheading', 'local_session_keepalive', null, true),
                ''));

        // Create keepalive time control widgets.
        $days = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
        foreach ($days as $day) {
            $choices[$day] = get_string($day, 'calendar', null, false);
                    // Don't use string lazy loading here because the string will be directly used and
                    // would produce a PHP warning otherwise.
        }
        $page->add(new admin_setting_configmulticheckbox2('local_session_keepalive/keepaliveweekdays',
                get_string('setting_keepaliveweekdays', 'local_session_keepalive', null, true),
                get_string('setting_keepaliveweekdays_desc', 'local_session_keepalive', null, true),
                $choices,
                $choices));
        unset($choices);
        $page->add(new admin_setting_configtime('local_session_keepalive/keepalivestart',
                'keepalivestartmin',
                get_string('setting_keepalivestart', 'local_session_keepalive', null, true),
                '',
                array('h' => 0, 'm' => 0)));
        $page->add(new admin_setting_configtime('local_session_keepalive/keepaliveend',
                'keepaliveendmin',
                get_string('setting_keepaliveend', 'local_session_keepalive', null, true),
                get_string('setting_keepaliveend_desc', 'local_session_keepalive', null, true),
                array('h' => 0, 'm' => 0)));
    }

    // Add settings page to navigation tree.
    $ADMIN->add('server', $page);
}
