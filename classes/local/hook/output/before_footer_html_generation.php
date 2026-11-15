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
 * Local plugin "Session keepalive" - Hook: Allows plugins to add any elements to the page footer.
 *
 * @package   local_session_keepalive
 * @copyright 2026 Alexander Bias <bias@alexanderbias.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_session_keepalive\local\hook\output;

/**
 * Hook to allow plugins to add any elements to the page footer.
 *
 * @package   local_session_keepalive
 * @copyright 2026 Alexander Bias <bias@alexanderbias.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_footer_html_generation {
    /**
     * Callback to insert the necessary JS code to each page.
     *
     * @param \core\hook\output\before_footer_html_generation $hook
     */
    public static function callback(\core\hook\output\before_footer_html_generation $hook): void {
        global $USER;

        // Fetch local_session_keepalive config.
        $config = get_config('local_session_keepalive');

        // Check if the plugin's functionality is enabled.
        // We have to check explicitely if the configurations are set because this function will already be
        // called at installation time and would then throw PHP notices otherwise.
        if (
            isset($config->enable) && $config->enable == true &&
                isset($config->keepaliveinterval) && $config->keepaliveinterval > 0
        ) {
            // Do only if there is an active session, because otherwise there is nothing we could keep alive.
            if (!empty($USER->sesskey)) {
                // Do only if the keepalive is configured to be run on any weekday and if the keepalive times are configured.
                if (
                    isset($config->keepaliveweekdays) && strpos($config->keepaliveweekdays, "1") !== false &&
                        isset($config->keepalivestart) && isset($config->keepalivestartmin) &&
                        isset($config->keepaliveend) && isset($config->keepaliveendmin)
                ) {
                    // Get the time according to the server timezone.
                    // The timezone has to be passed explicitly as usergetdate() and make_timestamp() would use
                    // the timezone of the current user otherwise.
                    $servertz = \core_date::get_server_timezone();
                    $now = time();
                    $date = usergetdate($now, $servertz);

                    // Check if the current server day and the previous server day are configured keepalive days.
                    $keepalivetoday = substr($config->keepaliveweekdays, $date['wday'], 1) == 1;
                    $keepaliveyesterday = substr($config->keepaliveweekdays, ($date['wday'] + 6) % 7, 1) == 1;

                    // Get the keepalive start time and the keepalive end time on the current server day.
                    $keepalivestart = make_timestamp(
                        $date['year'],
                        $date['mon'],
                        $date['mday'],
                        $config->keepalivestart,
                        $config->keepalivestartmin,
                        0,
                        $servertz
                    );
                    $keepaliveend = make_timestamp(
                        $date['year'],
                        $date['mon'],
                        $date['mday'],
                        $config->keepaliveend,
                        $config->keepaliveendmin,
                        0,
                        $servertz
                    );

                    // If keepalive start time == keepalive end time (which is the meaning for the whole day).
                    if ($keepalivestart == $keepaliveend) {
                        // The keepalive is active if the current server day is a keepalive day.
                        $keepaliveactive = $keepalivetoday;

                        // Otherwise, if the keepalive start time is before the keepalive end time.
                    } else if ($keepalivestart < $keepaliveend) {
                        // The keepalive is active if the current server day is a keepalive day and
                        // if the current server time is within the configured keepalive times.
                        $keepaliveactive = $keepalivetoday && $now >= $keepalivestart && $now <= $keepaliveend;

                        // Otherwise, if the keepalive start time is after the keepalive end time
                        // (which means that the keepalive time slot spans midnight).
                    } else {
                        // The keepalive time slot belongs to the day on which it starts.
                        // Thus, the keepalive is active if the current server day is a keepalive day and the time slot
                        // has started today, or if the previous server day is a keepalive day and the time slot which
                        // started yesterday has not ended yet.
                        $keepaliveactive = ($keepalivetoday && $now >= $keepalivestart) ||
                                ($keepaliveyesterday && $now <= $keepaliveend);
                    }

                    // Do only if the keepalive is active now.
                    if ($keepaliveactive) {
                        // Insert the necessary JS code to the page.
                        $jsoptions = ['keepaliveinterval' => $config->keepaliveinterval];
                        $hook->renderer->get_page()->requires->js_call_amd(
                            'local_session_keepalive/keepalive',
                            'init',
                            [$jsoptions]
                        );
                    }
                }
            }
        }
    }
}
