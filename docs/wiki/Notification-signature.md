<!-- cspell:ignore uamswp gravityforms -->

Written against v0.2.0.

The plugin is one PHP file, `uamswp-gravityforms-notification.php`, with one function and no settings, options, database tables or admin screens.

## The hook

| Item | Value |
| --- | --- |
| Filter | `gform_notification` (Gravity Forms) |
| Callback | `uamswp_gform_notification_url` |
| Priority | `10` |
| Arguments accepted | `1` (the notification array) |
| Applies to | every notification of every form on the site |

Gravity Forms applies `gform_notification` to each notification it is about to send, so the callback runs once per notification per submission.

## What it appends

The callback reads the notification's `message` and appends a newline and this markup:

```html
<font style="font-family: sans-serif; font-size:12px;">Submitted at {embed_url}</font>
```

- **The existing message comes first**, unchanged, and the signature follows it after a newline.
- **An absent message, or one that is not a string, is treated as empty**, so the result is the signature alone.
- **Every other key of the notification** (`subject`, `to` and the rest) is returned unchanged.

## The merge tag

`{embed_url}` is a Gravity Forms merge tag. Gravity Forms replaces it when it sends the notification with the address of the page the entry was submitted from, which it records with the entry. The plugin itself does not read or store the address.

## Requirements

| Requirement | Version |
| --- | --- |
| PHP | 8.2 or later (`Requires PHP` in the plugin header) |
| WordPress | 6.8 or later (`Requires at least`, declared on `master` after v0.2.0; v0.2.0 itself declares no minimum) |
| Gravity Forms | active on the same site; without it the filter never runs and the plugin does nothing |
