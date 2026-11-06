<?php

/**
 * Verify integration configuration without loading WordPress or a database.
 *
 * @command  composer test:unit -- tests/Unit/IntegrationDatabaseConfigTest.php
 */

declare(strict_types=1);

use Symfony\Component\Process\Process;

/**
 * Load the integration entry point in an isolated PHP process.
 *
 * @param  array<string, string|false>  $environment  Override or unset fake credentials.
 * @param  string  $entryPoint  Configuration or bootstrap filename.
 * @return Process Completed process with its output and exit status.
 */
function uamswp_gform_run_database_config(
    array $environment,
    string $entryPoint = 'wp-tests-config.php',
): Process {
    // Use fake credentials and a nonexistent core path to prevent WordPress boot.
    $environment = array_replace([
        'WP_TESTS_DB_NAME' => 'isolated_notification_tests',
        'WP_TESTS_DB_USER' => 'notification_test_runner',
        'WP_TESTS_DB_PASSWORD' => 'fixture-password',
        'WP_TESTS_DB_HOST' => 'database.invalid:3307',
        'WP_TESTS_ABSPATH' => __DIR__.'/../Support/no-wordpress/',
    ], $environment);

    // Read only configuration constants; neither entry point may reach a database.
    $file = __DIR__.'/../Integration/'.$entryPoint;
    $code = 'require '.var_export($file, true).'; echo json_encode([DB_NAME, DB_USER, DB_PASSWORD, DB_HOST]);';
    $process = new Process([PHP_BINARY, '-r', $code], null, $environment, null, 5);
    $process->run();

    return $process;
}

it('refuses missing or blank integration connection values', function (
    string $variable,
    string|false $value,
): void {
    $process = uamswp_gform_run_database_config([$variable => $value]);

    expect($process->getExitCode())->toBe(1)
        ->and($process->getErrorOutput())->toContain("set {$variable} explicitly")
        ->and($process->getOutput())->toBe('');
})->with(['WP_TESTS_DB_NAME', 'WP_TESTS_DB_USER', 'WP_TESTS_DB_PASSWORD', 'WP_TESTS_DB_HOST'])
    ->with(['unset' => false, 'empty' => '', 'blank' => ' ']);

it('refuses the root database account', function (string $user): void {
    $process = uamswp_gform_run_database_config(['WP_TESTS_DB_USER' => $user]);

    expect($process->getExitCode())->toBe(1)
        ->and($process->getErrorOutput())->toContain('not root.');
})->with(['root', ' RoOt ']);

it('preserves explicit connection values without connecting', function (string $password): void {
    $process = uamswp_gform_run_database_config(['WP_TESTS_DB_PASSWORD' => $password]);

    expect($process->getExitCode())->toBe(0)
        ->and($process->getErrorOutput())->toBe('')
        ->and(json_decode($process->getOutput(), true))->toBe([
            'isolated_notification_tests',
            'notification_test_runner',
            $password,
            'database.invalid:3307',
        ]);
})->with(['0', ' fixture-password ']);

it('refuses incomplete credentials before loading the integration harness', function (): void {
    $process = uamswp_gform_run_database_config(
        ['WP_TESTS_DB_PASSWORD' => false],
        'bootstrap.php',
    );

    expect($process->getExitCode())->toBe(1)
        ->and($process->getErrorOutput())->toContain('set WP_TESTS_DB_PASSWORD explicitly');
});
