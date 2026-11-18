<!-- cspell:ignore multisite uamswp -->

Written against v0.2.0.

- [The notification has no Submitted at line](#the-notification-has-no-submitted-at-line)
- [The line reads Submitted at with no address](#the-line-reads-submitted-at-with-no-address)
- [The line appears on notifications that should not have it](#the-line-appears-on-notifications-that-should-not-have-it)

## The notification has no Submitted at line

**Cause:** the plugin's callback did not run for that notification. Either the plugin is not active on the site that sent it (on a multisite network, check the individual site as well as the network), Gravity Forms is not active, or other code removed the callback from `gform_notification`.

**Fix:** confirm both plugins are active on the site the form lives on. Search the site's must-use plugins and theme for `remove_filter('gform_notification', 'uamswp_gform_notification_url'`, which [Removing the signature](Removing-the-signature.md) describes doing on purpose.

## The line reads Submitted at with no address

**Cause:** Gravity Forms had no page address recorded for the entry, so `{embed_url}` was replaced with nothing. An entry that was not submitted from a page carrying the form has no address to report.

**Example:** `Submitted at` followed by the end of the email.

**Fix:** submit the form from a page that shows it, as in [Your first notification](Your-first-notification.md). The plugin cannot supply an address Gravity Forms did not record.

## The line appears on notifications that should not have it

**Cause:** the plugin appends the line to every notification of every form on the site, admin and user notifications alike. It has no per-form or per-notification setting.

**Fix:** to stop it on a whole site, follow [Removing the signature](Removing-the-signature.md).
