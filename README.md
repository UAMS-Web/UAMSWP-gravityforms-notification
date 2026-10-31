# UAMSWP Gravity Forms Notification

## Tests

```bash
composer install
composer test:unit
```

The supported PHP floor is 8.2 (`composer.json` and the plugin header). Development and the Unit
suite run on PHP 8.4 with Pest 5 and Brain Monkey; `config.platform.php` pins the resolver to
8.4.27. The WordPress Integration suite runs on PHPUnit 9.6 from its own Composer setup in
`tests/Integration/` (WordPress' test suite supports PHPUnit 9 only):

```bash
composer setup:integration
composer test:integration
```

It needs a WordPress install to boot (this checkout inside one, or `WP_TESTS_ABSPATH`) and a
MySQL test database; see `tests/Integration/wp-tests-config.php`.

## Remote gate

Pull requests to `master` run [`.github/workflows/ci.yml`](.github/workflows/ci.yml). Its `CI` job
needs every other job and fails unless each one succeeds, so `CI` is the check to require. Today it
covers Composer lock freshness, the synced-files and no-emoji checks, the floor lint on PHP 8.2, and the Unit suite. The
`Attribution footer` and `Closing keywords` workflows check the pull request body.
