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
 * Local plugin "Session keepalive" - Library
 *
 * @package   local_session_keepalive
 * @copyright 2017 Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Insert the necessary JS code to each page by abusing Moodle's *_extend_navigation() hook.
 *
 * @param global_navigation $navigation
 */
function local_session_keepalive_extend_navigation(global_navigation $navigation) {
    global $PAGE, $USER;

    // Fetch local_session_keepalive config.
    $config = get_config('local_session_keepalive');

    // Check if the plugin's functionality is enabled.
    // We have to check explicitely if the configurations are set because this function will already be
    // called at installation time and would then throw PHP notices otherwise.
    if (isset($config->enable) && $config->enable == true &&
            isset($config->keepaliveinterval) && $config->keepaliveinterval > 0) {

        // Do only if there is an active session, because otherwise there is nothing we could keep alive.
        if ($USER->sesskey) {

            // Do only if the keepalive is configured to be run on any weekday and if the keepalive times are configured.
            if (isset($config->keepaliveweekdays) && strpos($config->keepaliveweekdays, "1") !== false &&
                    isset($config->keepalivestart) && isset($config->keepalivestartmin) &&
                    isset($config->keepaliveend) && isset($config->keepaliveendmin)) {

                // Get the time according to the server timezone.
                $now = time();
                $date = usergetdate($now);

                // Do only if the current server day is a configured keepalive day.
                $keepalivetoday = substr($config->keepaliveweekdays, $date['wday'], 1);
                if ($keepalivetoday == 1) {

                    // Do only if keepalive start time == keepalive end time (which is the meaning for the whole day) or
                    // if the current server time is within the configured keepalive times.
                    $keepalivestart = make_timestamp($date['year'], $date['mon'], $date['mday'],
                            $config->keepalivestart, $config->keepalivestartmin);
                    $keepaliveend = make_timestamp($date['year'], $date['mon'], $date['mday'],
                            $config->keepaliveend, $config->keepaliveendmin);
                    if (($keepalivestart == $keepaliveend) || ($now >= $keepalivestart && $now <= $keepaliveend)) {

                        // Insert the necessary JS code to the page.
                        $jsoptions = ['keepaliveinterval' => $config->keepaliveinterval];
                        $PAGE->requires->js_call_amd('local_session_keepalive/keepalive', 'init', [$jsoptions]);
                    }
                }
            }
        }
    }
}
