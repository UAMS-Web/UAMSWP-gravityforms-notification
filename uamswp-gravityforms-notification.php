<?php

declare(strict_types=1);
/*
Plugin Name: UAMSWP Gravity Forms Notification
Plugin URI: https://www.uams.edu
Description: UAMSWP append url of where the form was submitted
Author: Todd McKee, MEd
Version: 0.1.1
Requires PHP: 8.2
*/

// If this file is called directly, abort.
if (! defined('WPINC')) {
    exit;
}

add_filter('gform_notification', 'uamswp_gform_notification_url', 10, 1);
/**
 * Append the submitted-at signature to a Gravity Forms notification message.
 *
 * @param  array<string, mixed>  $notification  The notification being sent.
 * @return array<string, mixed>
 */
function uamswp_gform_notification_url(array $notification): array
{
    // read the existing message, treating an absent or non-string one as empty
    $message = is_string($notification['message'] ?? null) ? $notification['message'] : '';

    // append the signature to the message
    $notification['message'] = $message."\n".'<font style="font-family: sans-serif; font-size:12px;">Submitted at {embed_url}</font>';

    return $notification;
}
