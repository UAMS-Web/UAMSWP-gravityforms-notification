<?php

/**
 * Configuration for the WordPress test bootstrap.
 *
 * Require explicit credentials for an account restricted to a disposable test
 * database. No database connection is made by this configuration file.
 *
 * WordPress rebuilds its test tables on every run. Database-scoped grants are
 * the isolation boundary; the bootstrap's live-database check is best effort.
 * See the README for provisioning and grant verification before running tests.
 *
 * Multisite: run with WP_MULTISITE=1, wp-phpunit's own switch.
 */

declare(strict_types=1);

// Refuse incomplete credentials before WordPress can start its installer.
foreach (['WP_TESTS_DB_NAME', 'WP_TESTS_DB_USER', 'WP_TESTS_DB_PASSWORD', 'WP_TESTS_DB_HOST'] as $variable) {
    $value = getenv($variable);

    if ($value === false || trim($value) === '') {
        fwrite(STDERR, "Refusing to run: set {$variable} explicitly for a restricted test database account.".PHP_EOL);

        exit(1);
    }
}

// Reject the privileged account previously used by the default configuration.
if (strtolower(trim((string) getenv('WP_TESTS_DB_USER'))) === 'root') {
    fwrite(STDERR, 'Refusing to run: WP_TESTS_DB_USER must be a restricted test database account, not root.'.PHP_EOL);

    exit(1);
}

// Resolve the core path for both the harness and WordPress' installer process.
define('ABSPATH', rtrim((string) (getenv('WP_TESTS_ABSPATH') ?: dirname(__DIR__, 5)), '/').'/');

define('DB_NAME', (string) getenv('WP_TESTS_DB_NAME'));
define('DB_USER', (string) getenv('WP_TESTS_DB_USER'));
define('DB_PASSWORD', (string) getenv('WP_TESTS_DB_PASSWORD'));
define('DB_HOST', (string) getenv('WP_TESTS_DB_HOST'));
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

$table_prefix = 'wptests_';

define('WP_TESTS_DOMAIN', 'example.org');
define('WP_TESTS_EMAIL', 'admin@example.org');
define('WP_TESTS_TITLE', 'UAMSWP Gravity Forms Notification Tests');
define('WP_PHP_BINARY', 'php');

/*
 * Keep the tests off the network. WordPress' HTTP API would otherwise reach out
 * during install (update checks), which makes runs slow and their failures
 * depend on connectivity rather than on the code.
 */
define('WP_HTTP_BLOCK_EXTERNAL', true);

// cspell:ignore ABSPATH wptests
