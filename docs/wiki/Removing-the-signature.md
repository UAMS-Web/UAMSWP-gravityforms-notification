<!-- cspell:ignore uamswp -->

Written against v0.2.0.

A site that has the plugin active but does not want the `Submitted at` line in its notifications can remove the plugin's filter, or deactivate the plugin. The plugin has no setting for this.

1. **To turn it off everywhere on a site,** deactivate the plugin under *Plugins*. Nothing else needs undoing: the plugin stores no options and changes no forms.
2. **To keep the plugin active but stop the line,** remove its callback from the `gform_notification` filter once the plugin has loaded, for example from a must-use plugin or the theme's `functions.php`:

   ```php
   add_action('plugins_loaded', function (): void {
       remove_filter('gform_notification', 'uamswp_gform_notification_url', 10);
   });
   ```

   `remove_filter()` must name the same priority the plugin registered, `10`, or it removes nothing.
3. **Submit a form and check the email.** The notification ends with the form's own message and no `Submitted at` line.

**Note:** removing the filter affects every form on the site. The plugin passes only the notification to its callback, not the form, so it cannot be limited to some forms from inside the plugin.

## Reference

- [Notification signature](Notification-signature.md): the callback name, the hook and the priority used above.
