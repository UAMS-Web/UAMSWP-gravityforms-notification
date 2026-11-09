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

    public function test_the_registered_callback_accepts_one_argument(): void
    {
        global $wp_filter;

        $this->assertIsArray($wp_filter);
        $hook = $wp_filter['gform_notification'] ?? null;
        $this->assertInstanceOf(\WP_Hook::class, $hook);

        $callbacks = $hook->callbacks[10] ?? null;
        $this->assertIsArray($callbacks);
        $callback = $callbacks['uamswp_gform_notification_url'] ?? null;
        $this->assertIsArray($callback);

        $this->assertSame(1, $callback['accepted_args'] ?? null);
    }

    public function test_applying_the_filter_appends_the_signature(): void
    {
        $result = apply_filters('gform_notification', ['message' => 'Hello'], [], []);

        $this->assertIsArray($result);
        $this->assertSame(
            "Hello\n".'<font style="font-family: sans-serif; font-size:12px;">Submitted at {embed_url}</font>',
            $result['message'],
        );
    }
}
