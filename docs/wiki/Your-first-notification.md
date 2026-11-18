Written against v0.2.0.

At the end of this walkthrough a form's notification email ends with a line naming the page the form was submitted from.

1. **Install and activate the plugin** as the [README](https://github.com/UAMS-Web/UAMSWP-gravityforms-notification#installation) describes. Gravity Forms must be active on the same site. The plugin has no settings page, and nothing appears in the admin menu.
2. **Open a form that sends a notification.** Any form with at least one active notification under *Settings*, *Notifications* will do; the plugin works on every notification of every form.
3. **Put the form on a page** and publish it, for example at `https://example.com/contact/`.
4. **Submit the form from that page.**
5. **Open the notification email.** After the message the form's notification already sends, a new line reads `Submitted at https://example.com/contact/`, in a small sans-serif font.

**Note:** the address is the page the visitor submitted from, which Gravity Forms records with the entry. A form placed on several pages reports whichever page each submission came from.

Going further:

- [Notification signature](Notification-signature.md) for exactly what is appended and when.
- [Removing the signature](Removing-the-signature.md) to turn the line off on one site.
- [Troubleshooting](Troubleshooting.md) when the line is missing or empty.
