<?php

/**
 * First unit test pinning harness load for UAMSWP Gravity Forms Notification.
 */

declare(strict_types=1);

it('loads the plugin surface', function (): void {
    expect(function_exists('uamswp_gform_notification_url'))->toBeTrue();
});
