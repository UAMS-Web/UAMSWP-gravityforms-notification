# UAMSWP Gravity Forms Notification

## Tests

```bash
composer install
composer test:unit
```

PHP floor is 7.4, so the harness is PHPUnit 9 (Pest needs PHP 8.2+).

## Remote gate

Pull requests to `master` run [`.github/workflows/ci.yml`](.github/workflows/ci.yml). Its `CI` job
needs every other job and fails unless each one succeeds, so `CI` is the check to require. Today it
covers Composer lock freshness, the synced-files and no-emoji checks, and the Unit suite. The
`Attribution footer` and `Closing keywords` workflows check the pull request body.
