<?php

/**
 * Integration bootstrap: boots WordPress through wp-phpunit.
 *
 * tests/Integration/phpunit.xml's bootstrap. The Integration suite has its own
 * Composer setup in this directory (PHPUnit 9.6, wp-phpunit, the polyfills),
 * separate from the root's Pest 5, so this file loads that setup's autoloader
 * and never the root's. Configuration comes from WP_TESTS_* environment
 * variables, read in tests/Integration/wp-tests-config.php.
 */

declare(strict_types=1);

// Validate explicit database credentials before loading any integration code.
require_once __DIR__.'/wp-tests-config.php';

if (! is_readable(__DIR__.'/vendor/autoload.php')) {
    fwrite(
        STDERR,
        PHP_EOL."Refusing to run: the Integration suite's own Composer setup is not installed.".PHP_EOL
        .'Run `composer setup:integration` (or `composer install` in tests/Integration) first.'.PHP_EOL.PHP_EOL
    );

    exit(1);
}

require_once __DIR__.'/vendor/autoload.php';

/*
 * Locate WordPress. Unset, WP_TESTS_ABSPATH defaults to the install this
 * checkout sits in (<wordpress>/wp-content/plugins/<plugin>/tests/Integration),
 * five levels up. The configuration resolves ABSPATH for the installer too.
 *
 * A checkout that is not inside a WordPress install must set WP_TESTS_ABSPATH
 * to a core copy, for example one fetched with:
 *   wp core download --path=/tmp/wordpress
 *   WP_TESTS_ABSPATH=/tmp/wordpress composer test:integration
 */
$wordpress = ABSPATH;

putenv('WP_TESTS_ABSPATH='.$wordpress);

if (! is_readable($wordpress.'wp-settings.php')) {
    fwrite(
        STDERR,
        PHP_EOL."Refusing to run: no WordPress at {$wordpress}.".PHP_EOL
        .'Check this plugin out inside a WordPress install, or set WP_TESTS_ABSPATH to one'.PHP_EOL
        .'(for example a copy fetched with `wp core download --path=<dir>`).'.PHP_EOL.PHP_EOL
    );

    exit(1);
}

/*
 * Reject a known working-database match as a secondary, best-effort check.
 *
 * This recognizes a literal DB_NAME in the core-local config only. Parent
 * configs and dynamic PHP values may not be detected. Restricted database
 * grants, not configuration parsing, isolate the destructive test-table setup.
 */
$testDatabase = DB_NAME;
$liveConfig = $wordpress.'wp-config.php';

if (is_readable($liveConfig)) {
    $matched = preg_match(
        '/define\(\s*[\'"]DB_NAME[\'"]\s*,\s*[\'"]([^\'"]*)[\'"]/',
        (string) file_get_contents($liveConfig),
        $liveDatabase
    );

    if ($matched === 1 && $liveDatabase[1] === $testDatabase) {
        fwrite(
            STDERR,
            PHP_EOL."Refusing to run: WP_TESTS_DB_NAME is '{$testDatabase}', the database the".PHP_EOL
            ."WordPress install at {$wordpress} uses. The test installer rebuilds its test tables.".PHP_EOL
            .'Use a disposable database and an account restricted to that database.'.PHP_EOL.PHP_EOL
        );

        exit(1);
    }
}

/*
 * Point core's bootstrap at this configuration. Left undefined, it looks in its
 * own package directory and fails with a list of missing constants.
 */
define('WP_TESTS_CONFIG_FILE_PATH', __DIR__.'/wp-tests-config.php');

// Set by wp-phpunit's own autoloaded file.
$wpPhpunit = (string) getenv('WP_PHPUNIT__DIR');

require_once $wpPhpunit.'/includes/functions.php';

/*
 * Load the plugin the way WordPress would, before anything runs: on
 * muplugins_loaded, the earliest hook the test bootstrap fires. A plugin that
 * depends on another plugin requires that one first, in the same closure.
 */
tests_add_filter('muplugins_loaded', static function (): void {
    require dirname(__DIR__, 2).'/uamswp-gravityforms-notification.php';
});

require $wpPhpunit.'/includes/bootstrap.php';

require_once __DIR__.'/../Support/IntegrationTestCase.php';

// cspell:ignore ABSPATH autoloaded fwrite getenv muplugins preg putenv rtrim
