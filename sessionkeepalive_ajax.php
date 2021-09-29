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
// @codingStandardsIgnoreStart
// Let codechecker ignore the next line because otherwise it would complain about a missing login check
// after requiring config.php which is really not needed.
require(__DIR__ . '/../../config.php');
// @codingStandardsIgnoreEnd

// Require the session key - want to make sure that this isn't called
// maliciously to keep a session alive longer than intended.
require_sesskey();

// Update the session.
\core\session\manager::touch_session(session_id());
