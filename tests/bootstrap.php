<?php

/**
 * Unit bootstrap, for Pest.
 *
 * The Unit suite runs with no WordPress: WordPress functions are mocked per
 * test with Brain Monkey. The Integration suite does not come through here; it
 * has its own Composer setup and bootstrap under tests/Integration.
 */

declare(strict_types=1);

require_once dirname(__DIR__).'/vendor/autoload.php';

/*
 * The Unit suite.
 *
 * Patchwork first: it can only redefine functions declared in files loaded
 * after it, so loading it before the plugin is what lets a test mock one of the
 * plugin's own functions, not only WordPress'.
 */
require_once dirname(__DIR__).'/vendor/antecedent/patchwork/Patchwork.php';

/*
 * Plugin files open with `defined('ABSPATH') || exit;`. Define it so they load,
 * pointing at a directory that is not a WordPress install: code that requires a
 * core file through ABSPATH fails loudly here, which is the sign it belongs in
 * the Integration suite.
 */
if (! defined('ABSPATH')) {
    define('ABSPATH', sys_get_temp_dir().'/uamswp_gform_notification-tests-no-wordpress/');
}

/*
 * Load the files that DEFINE functions and classes, never the main plugin file:
 * it registers hooks at file scope, and Brain Monkey's add_action() and
 * add_filter() exist only inside a test. Hook registration is tested in the
 * Integration suite, or in a unit test that calls a registering function.
 * Composer-autoloaded classes need no line here.
 *
 * When the main file must load in the Unit suite (definitions and registrations
 * still mixed), wrap that require in a Brain Monkey session and stub every
 * WordPress function it calls at file scope; see the tests skill.
 */

/*
 * Definitions and registrations still mixed in the main file: load it inside
 * one Brain Monkey session with load-time WordPress calls stubbed. Functions
 * the file defines outlive the session; hooks it registered are discarded by
 * tearDown(). Do not define permanent WordPress function stubs in this file —
 * Patchwork treats this bootstrap as too early and Brain Monkey cannot
 * redefine them in tests (DefinedTooEarly).
 */
Brain\Monkey\setUp();

Brain\Monkey\Functions\when('plugin_dir_path')->justReturn(dirname(__DIR__).'/');
Brain\Monkey\Functions\when('plugin_dir_url')->justReturn('https://example.test/wp-content/plugins/'.basename(dirname(__DIR__)).'/');
Brain\Monkey\Functions\when('plugin_basename')->alias(static function ($file) {
    return basename(dirname($file)).'/'.basename($file);
});
Brain\Monkey\Functions\when('is_multisite')->justReturn(false);
Brain\Monkey\Functions\when('get_site_option')->alias(static function ($key, $default = false) {
    return $default;
});
Brain\Monkey\Functions\when('get_option')->alias(static function ($key, $default = false) {
    return $default;
});
Brain\Monkey\Functions\when('update_option')->justReturn(null);
Brain\Monkey\Functions\when('register_activation_hook')->justReturn(null);
Brain\Monkey\Functions\when('register_deactivation_hook')->justReturn(null);
Brain\Monkey\Functions\when('register_uninstall_hook')->justReturn(null);
Brain\Monkey\Functions\when('load_plugin_textdomain')->justReturn(null);
Brain\Monkey\Functions\when('is_admin')->justReturn(false);
Brain\Monkey\Functions\when('wp_enqueue_script')->justReturn(null);
Brain\Monkey\Functions\when('wp_enqueue_style')->justReturn(null);
Brain\Monkey\Functions\when('wp_register_script')->justReturn(null);
Brain\Monkey\Functions\when('wp_register_style')->justReturn(null);
Brain\Monkey\Functions\when('wp_localize_script')->justReturn(null);
Brain\Monkey\Functions\when('esc_html')->returnArg();
Brain\Monkey\Functions\when('esc_attr')->returnArg();
Brain\Monkey\Functions\when('esc_url')->returnArg();
Brain\Monkey\Functions\when('__')->returnArg();
Brain\Monkey\Functions\when('_e')->justReturn(null);
Brain\Monkey\Functions\when('esc_html__')->returnArg();
Brain\Monkey\Functions\when('esc_attr__')->returnArg();

/*
 * The main plugin file opens with `defined('WPINC') || die`, which ends the whole
 * process silently, exit status 0 and no tests run. Define it so the file loads.
 */
if (! defined('WPINC')) {
    define('WPINC', 'wp-includes');
}

if (is_readable(dirname(__DIR__).'/uamswp-gravityforms-notification.php')) {
    require_once dirname(__DIR__).'/uamswp-gravityforms-notification.php';
}

Brain\Monkey\tearDown();


// cspell:ignore ABSPATH autoloaded
