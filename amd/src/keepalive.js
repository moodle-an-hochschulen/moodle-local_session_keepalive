/**
 * Local plugin "Session keepalive" - JS Code
 *
 * @module    local_session_keepalive/keepalive
 * @copyright 2017 Alexander Bias, University of Ulm <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/config', 'core/log'], function($, config, log) {
    "use strict";

    // Global variable for keepalive interval.
    var keepaliveInterval = null;

    // Global variable for holding the form elements within the page's main region.
    var formElements = null;

    // Global variable for holding the dirty status.
    var dirty = false;

    /**
     * Function to tell the server to keep the session alive.
     */
    function doKeepAlive() {
        // Keep session alive by AJAX.
        // We know about the benefits of the core/ajax module (https://docs.moodle.org/dev/AJAX),
        // but for this very lightweight request we only use a simple jQuery AJAX call.
        $.ajax({
            url: config.wwwroot + '/local/session_keepalive/sessionkeepalive_ajax.php',
            dataType: 'json',
            type: 'POST',
            data: {
                // Add the session key.
                'sesskey': config.sesskey,
                // Add a query string to prevent older versions of IE from using the cache.
                'time': Date.now()
            },
            headers: {
                'Cache-Control': 'no-cache',
                'Expires': '-1'
            },
            /* This section exists for understanding the code, but it is commented because it does nothing.
            success: function(result) {
                // The AJAX call was successful.
                if (result.status == 200) {
                    // Don't care about the result (especially as there isn't any result sent back).
                }
            },
            */
            error: function(request) {
                // The AJAX call returned 403, we have to assume that the session was terminated and can't be kept alive anymore.
                if (request.status == 403) {
                    // Stop doing any more requests.
                    clearInterval(keepaliveInterval);

                    // The AJAX call was cached somewhere.
                } else if (request.status >= 300 && request.status <= 399) {
                    // Warn the developer, but don't do anything else.
                    log.debug('moodle-local_session_keepalive-keepalive: ' +
                            'A cached copy of the keepalive answer was returned so it\'s reliablity cannot be guaranteed');
                }
            }
        });
    }

    /**
     * Function which performs the continuous keepalive check.
     */
    function checkKeepAlive() {
        // If the page is currently marked dirty.
        if (dirty === true) {
            // Keep the Moodle session of this user alive.
            doKeepAlive();
        }
    }

    /**
     * Function to mark the page as currently dirty.
     */
    function markDirty() {
        // Remember that the page is now dirty.
        dirty = true;

        // Now that the page is marked dirty, it has to be dirty until it is sent to the server.
        // We don't need the event listeners anymore.
        formElements.off('change keyup keydown', markDirty);
    }

    return {
        init: function(params) {
            // Initialize continuous keepalive check.
            if (params.keepaliveinterval !== null && params.keepaliveinterval > 0) {
                // Search all existing form elements within the page's main region.
                formElements = $('#region-main input, #region-main textarea, #region-main select');

                // If there are any form elements.
                if (formElements.length > 0) {
                    // Add the event listeners to the form elements.
                    formElements.on('change keyup keydown', markDirty);

                    // Keepalive every params.keepaliveinterval minutes.
                    keepaliveInterval = setInterval(checkKeepAlive, params.keepaliveinterval * 1000 * 60);
                }
            }
        }
    };
});
