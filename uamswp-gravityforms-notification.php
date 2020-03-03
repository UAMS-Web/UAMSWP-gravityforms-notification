<?php
/*
Plugin Name: UAMSWP Gravity Forms Notification
Plugin URI: https://www.uams.edu
Description: UAMSWP append url of where the form was submitted
Author: Todd McKee, MEd
Version: 0.1
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
add_filter( 'gform_notification', 'uamswp_gform_notification_url', 10, 3 );
function uamswp_gform_notification_url( $notification, $form, $entry ) {
       // append a signature to the existing notification
       // message with .=
    $notification['message'] .= "\n" . '<font style="font-family: sans-serif; font-size:12px;">Submitted at {embed_url}</font>';
 
    return $notification;
}