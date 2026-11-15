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

namespace local_session_keepalive\local\hook\output;

/**
 * Local plugin "Session keepalive" - Tests for the before_footer_html_generation hook callback.
 *
 * @package   local_session_keepalive
 * @copyright 2026 Alexander Bias <bias@alexanderbias.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_session_keepalive\local\hook\output\before_footer_html_generation
 */
final class before_footer_html_generation_test extends \advanced_testcase {
    /**
     * Test that the keepalive weekdays and the keepalive time slot are evaluated in the server timezone
     * and not in the timezone of the current user.
     *
     * @dataProvider server_timezone_provider
     * @param bool $differentweekdays True if the server and the user should be on different weekdays (25 hours apart),
     *                                false if it should be noon in server time and midnight in user time.
     * @param string $weekdays The keepalive weekdays as string of seven 0/1 flags, starting with sunday.
     * @param string|null $serverweekdayflag The flag to be set for the current server weekday within the keepalive weekdays,
     *                                       or null to leave the keepalive weekdays untouched.
     * @param int $starthour The hour of the keepalive start time.
     * @param int $endhour The hour of the keepalive end time.
     * @param bool $expected True if the keepalive is expected to be active.
     */
    public function test_server_timezone(
        bool $differentweekdays,
        string $weekdays,
        ?string $serverweekdayflag,
        int $starthour,
        int $endhour,
        bool $expected
    ): void {
        global $PAGE;

        $this->resetAfterTest();

        // Pick the server timezone and the user timezone, both as offset to UTC in hours.
        // As the callback uses the real current time, the timezones are picked relative to the current time.
        // This way, the test is deterministic regardless of the time when it is run.
        if ($differentweekdays) {
            // These two timezones (UTC-12 and UTC+13) are 25 hours apart,
            // so the server weekday and the user weekday always differ.
            $serveroffset = -12;
            $useroffset = 13;
        } else {
            // It is currently between 12:00 and 12:59 in server time and between 0:00 and 0:59 in user time.
            $serveroffset = 12 - (int) gmdate('G');
            $useroffset = ($serveroffset > 0) ? $serveroffset - 12 : $serveroffset + 12;
        }

        // Set the server timezone.
        // Please note that the sign of the Etc/GMT timezones is inverted, i.e. Etc/GMT-2 is UTC+2.
        if ($serveroffset > 0) {
            $servertimezone = 'Etc/GMT-' . $serveroffset;
        } else if ($serveroffset < 0) {
            $servertimezone = 'Etc/GMT+' . abs($serveroffset);
        } else {
            $servertimezone = 'Etc/GMT';
        }
        $this->setTimezone($servertimezone, $servertimezone);

        // Log in as a user with the user timezone.
        // The timezone is set as numeric offset as the Etc/GMT timezone names are not accepted as user timezone.
        $user = $this->getDataGenerator()->create_user(['timezone' => (string) $useroffset]);
        $this->setUser($user);

        // Make sure that the user has an active session key, otherwise the callback would do nothing at all.
        sesskey();

        // Set the flag for the current server weekday, if requested.
        // The current server weekday is only known at runtime, so it can't be part of the data provider.
        if ($serverweekdayflag !== null) {
            $serverweekday = (int) (new \DateTime('now', \core_date::get_server_timezone_object()))->format('w');
            $weekdays = substr_replace($weekdays, $serverweekdayflag, $serverweekday, 1);
        }

        // Enable the plugin with the given keepalive weekdays and keepalive time slot.
        set_config('enable', 1, 'local_session_keepalive');
        set_config('keepaliveinterval', 10, 'local_session_keepalive');
        set_config('keepaliveweekdays', $weekdays, 'local_session_keepalive');
        set_config('keepalivestart', $starthour, 'local_session_keepalive');
        set_config('keepalivestartmin', 0, 'local_session_keepalive');
        set_config('keepaliveend', $endhour, 'local_session_keepalive');
        set_config('keepaliveendmin', 0, 'local_session_keepalive');

        // Run the hook callback.
        $PAGE->set_url('/');
        $PAGE->set_context(\context_system::instance());
        $hook = new \core\hook\output\before_footer_html_generation($PAGE->get_renderer('core'));
        before_footer_html_generation::callback($hook);

        // Check if the keepalive JS code was added to the page.
        $keepaliveadded = str_contains($PAGE->requires->get_end_code(), 'local_session_keepalive/keepalive');
        $this->assertSame($expected, $keepaliveadded);
    }

    /**
     * Data provider for test_server_timezone().
     *
     * @return array
     */
    public static function server_timezone_provider(): array {
        return [
            'Time slot matches server time only' => [
                'differentweekdays' => false,
                'weekdays' => '1111111',
                'serverweekdayflag' => null,
                'starthour' => 10,
                'endhour' => 15,
                'expected' => true,
            ],
            'Time slot matches user time only' => [
                'differentweekdays' => false,
                'weekdays' => '1111111',
                'serverweekdayflag' => null,
                'starthour' => 0,
                'endhour' => 2,
                'expected' => false,
            ],
            'Weekday matches server time only' => [
                'differentweekdays' => true,
                'weekdays' => '0000000',
                'serverweekdayflag' => '1',
                'starthour' => 0,
                'endhour' => 0,
                'expected' => true,
            ],
            'Weekday matches user time only' => [
                'differentweekdays' => true,
                'weekdays' => '1111111',
                'serverweekdayflag' => '0',
                'starthour' => 0,
                'endhour' => 0,
                'expected' => false,
            ],
        ];
    }
}
