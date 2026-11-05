<?php

/**
 * Pins how the plugin hooks `gform_notification`, against real WordPress.
 */

declare(strict_types=1);

namespace UamswpGravityformsNotification\Tests\Integration;

use UamswpGravityformsNotification\Tests\Support\IntegrationTestCase;

final class NotificationFilterTest extends IntegrationTestCase
{
    public function test_the_callback_is_registered_on_gform_notification_at_priority_10(): void
    {
        $this->assertSame(10, has_filter('gform_notification', 'uamswp_gform_notification_url'));
    }

    public function test_the_registered_callback_accepts_three_arguments(): void
    {
        global $wp_filter;

        $callbacks = $wp_filter['gform_notification']->callbacks[10];

        $this->assertSame(3, $callbacks['uamswp_gform_notification_url']['accepted_args']);
    }

    public function test_applying_the_filter_appends_the_signature(): void
    {
        $result = apply_filters('gform_notification', ['message' => 'Hello'], [], []);

        $this->assertSame(
            "Hello\n".'<font style="font-family: sans-serif; font-size:12px;">Submitted at {embed_url}</font>',
            $result['message'],
        );
    }
}
