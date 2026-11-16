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
 * Local plugin "Session keepalive" - Ajax Endpoint.
 *
 * @package   local_session_keepalive
 * @copyright 2020 Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @copyright based on code from 2014 by Andrew Nicols
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

// phpcs:disable moodle.Files.RequireLogin.Missing
// Let codechecker ignore the next line because otherwise it would complain about a missing login check
// after requiring config.php which is really not needed.
require(__DIR__ . '/../../config.php');

// Require the session key - want to make sure that this isn't called
// maliciously to keep a session alive longer than intended.
require_sesskey();

// Guests and not-logged-in users only get the keepalive if the admin enabled it for them.
// Otherwise, answer with a 403 status which tells the client to stop doing any more keepalive requests.
// This way, pages which were loaded before the setting was disabled stop their keepalive requests as well.
if ((!isloggedin() || isguestuser()) && get_config('local_session_keepalive', 'keepaliveguests') != true) {
    http_response_code(403);
    echo json_encode(['status' => 403]);
    die;
}

// Update the session.
\core\session\manager::touch_session(session_id());

// Send a valid JSON answer back, as the client expects a JSON response.
echo json_encode(['status' => 200]);
