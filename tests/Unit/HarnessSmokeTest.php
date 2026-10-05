<?php

/**
 * First unit test pinning harness load for UAMSWP Gravity Forms Notification.
 */

declare(strict_types=1);

namespace UamswpGravityformsNotification\Tests\Unit;

use UamswpGravityformsNotification\Tests\Support\UnitTestCase;

final class HarnessSmokeTest extends UnitTestCase
{
    /**
     * @return void
     */
    public function test_harness_loads_plugin_surface(): void
    {
        $this->assertTrue(\function_exists('uamswp_gform_notification_url'));
    }
}
