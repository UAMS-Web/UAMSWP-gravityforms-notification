<?php

/**
 * Pins `uamswp_gform_notification_url()`, the `gform_notification` callback.
 */

declare(strict_types=1);

covers('uamswp_gform_notification_url');

const UAMSWP_GFORM_SIGNATURE = "\n".'<font style="font-family: sans-serif; font-size:12px;">Submitted at {embed_url}</font>';

it('appends the submitted-at signature to the notification message', function (): void {
    $result = uamswp_gform_notification_url(['message' => 'Hello'], [], []);

    expect($result['message'])->toBe('Hello'.UAMSWP_GFORM_SIGNATURE);
});

it('keeps the existing message first and puts the signature after a newline', function (): void {
    $result = uamswp_gform_notification_url(['message' => ''], [], []);

    expect($result['message'])->toBe(UAMSWP_GFORM_SIGNATURE)
        ->and($result['message'][0])->toBe("\n");
});

it('leaves every other notification key unchanged', function (): void {
    $notification = ['message' => 'Body', 'subject' => 'Subject', 'to' => 'a@example.test'];

    $result = uamswp_gform_notification_url($notification, ['id' => 1], ['id' => 2]);

    expect($result)->toHaveKeys(['message', 'subject', 'to'])
        ->and($result['subject'])->toBe('Subject')
        ->and($result['to'])->toBe('a@example.test');
});

it('does not read the form or the entry', function (): void {
    $withData = uamswp_gform_notification_url(['message' => 'M'], ['title' => 'T'], ['1' => 'x']);
    $without = uamswp_gform_notification_url(['message' => 'M'], [], []);

    expect($withData)->toBe($without);
});

it('adds the embed_url merge tag Gravity Forms replaces at send time', function (): void {
    $result = uamswp_gform_notification_url(['message' => 'M'], [], []);

    expect($result['message'])->toContain('{embed_url}');
});
