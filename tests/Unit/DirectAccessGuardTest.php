<?php

/**
 * Pins the direct-access guard at the top of the plugin file.
 *
 * The guard ends the process, so each case loads the plugin file in a child PHP
 * process. Pest's mutation run cannot see into a child process: these pins are
 * mutation-verified by hand, recorded on the coverage ticket.
 */

declare(strict_types=1);

/**
 * Include the plugin file in a child process and return what it printed.
 *
 * @param  string  $prelude  PHP run before the include.
 */
function uamswp_gform_run_plugin_file(string $prelude): string
{
    $file = dirname(__DIR__, 2).'/uamswp-gravityforms-notification.php';
    $code = $prelude.' require '.var_export($file, true)."; echo 'LOADED';";

    return (string) shell_exec(escapeshellarg(PHP_BINARY).' -r '.escapeshellarg($code).' 2>&1');
}

it('loads past the guard when WPINC is defined', function (): void {
    $output = uamswp_gform_run_plugin_file("define('WPINC', 'wp-includes'); function add_filter() {}");

    expect($output)->toBe('LOADED');
});

it('ends the process silently when the file is requested directly', function (): void {
    $output = uamswp_gform_run_plugin_file('function add_filter() {}');

    expect($output)->toBe('');
});
