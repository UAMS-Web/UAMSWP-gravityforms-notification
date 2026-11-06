# UAMSWP Gravity Forms Notification

## Amp orb setup

Amp runs [`.agents/setup`](.agents/setup) when preparing a fresh Debian 12 orb. It installs PHP
8.4 and its required extensions, verifies and installs Composer 2.11.1 when needed, and installs
both committed Composer lockfiles. Repeating setup reuses installed system packages and lets
Composer reconcile dependencies with the lockfiles. PHP patch updates remain on the 8.4 series.
An installed PHP older than 8.4.1 is upgraded to satisfy the locked development tools.
Repository-specific links select PHP and Composer ahead of existing user executables during
setup and in later Bash login shells within the checkout, without replacing those executables.

Setup installs development dependencies only. It does not install WordPress core or a database
server, set database credentials, connect to a database, or execute tests. The isolated integration
dependencies are installed so PHPStan can scan their symbols, not to boot WordPress. No resume
hook or authenticated service is needed. The executable is specific to orbs; macOS and Windows
developers should use their existing PHP 8.4 environment and the Composer commands below.

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
```

It needs a WordPress install to boot (this checkout inside one, or `WP_TESTS_ABSPATH`) and a
disposable MySQL test database with a restricted account. Complete the following setup
before running `composer test:integration`; see `tests/Integration/wp-tests-config.php`.

### Isolate the integration database

The WordPress test installer rebuilds its test tables on every run. Never use a working
site's database or credentials. Prefer a separate local or CI database instance. The
integration configuration requires all four `WP_TESTS_DB_*` values below and refuses
missing, blank, or `root` credentials. No passwordless default is provided.

An administrator must provision a dedicated account with privileges on exactly one
disposable database. The following SQL is a provisioning example, not a command run by
the repository. Replace the password placeholder privately before execution. The example
database name deliberately contains no SQL grant wildcard characters (`_` or `%`).

```sql
CREATE DATABASE `gformnotificationtests`
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE USER 'gform_notification_test'@'localhost'
    IDENTIFIED BY '<generated-password>';

GRANT ALL PRIVILEGES ON `gformnotificationtests`.*
    TO 'gform_notification_test'@'localhost';

SHOW GRANTS FOR 'gform_notification_test'@'localhost';
```

Before provisioning, check that neither the database nor the account is in use. Do not
reuse an application account or add `GRANT OPTION`, global privileges, or privileged
roles. The account host must match the actual connection method; containers and TCP
connections can require a different host entry. Audit the effective account's grants,
including inherited roles, rather than assuming an account name proves isolation.

Configure these variables in the environment that runs the integration suite:

| Variable | Example or requirement |
| --- | --- |
| `WP_TESTS_DB_NAME` | `gformnotificationtests`, containing nothing worth retaining |
| `WP_TESTS_DB_USER` | `gform_notification_test`, restricted to that database |
| `WP_TESTS_DB_PASSWORD` | Nonempty secret supplied privately, never committed |
| `WP_TESTS_DB_HOST` | `localhost`, or the explicit disposable database endpoint |
| `WP_TESTS_ABSPATH` | Path to a WordPress core copy when the checkout is outside WordPress |

Using the test account and the same connection endpoint, verify isolation before running
WordPress:

1. Run `SELECT CURRENT_USER();` and `SHOW GRANTS;` and confirm the effective account has
   no access outside the disposable database. Inspect any active roles as well.
2. Create a temporary probe table inside the disposable database, insert and read a row,
   then drop that probe table. This verifies the required test-table permissions.
3. Have an administrator create a non-sensitive probe in a second disposable database.
   Confirm the test account cannot read that probe. Never use production data as a probe.
4. Only after these checks pass, run `composer test:integration`.

Database permissions are the safety boundary. The bootstrap's additional live-database
comparison recognizes only a literal `DB_NAME` in a core-local `wp-config.php`; it does
not reliably recognize parent-directory configurations or dynamic PHP values. A non-root
username and explicit credentials alone do not prove its grants are restricted. Unknown
live configurations therefore do not establish safety, and the repository does not execute
the working site's configuration to discover them.

The configuration-refusal tests run in the Unit suite using fake credentials and child
PHP processes. They do not connect to a database. Real grant verification and integration
execution require the separately provisioned disposable environment; neither is performed
by the current remote CI workflow.

## Remote gate

Pull requests to `master` run [`.github/workflows/ci.yml`](.github/workflows/ci.yml). Its `CI` job
needs every other job and fails unless each one succeeds, so `CI` is the check to require. Today it
covers Composer lock freshness, the synced-files and no-emoji checks, the floor lint on PHP 8.2, and the Unit suite. The
`Attribution footer` and `Closing keywords` workflows check the pull request body.
