# Aruna Inventory

Laravel 13 and Filament 5 admin starter. The supported local path is Docker Compose: `cp .env.example .env`, then `make setup`.

- Format with `vendor/bin/pint` before committing.
- Tests are Pest and expect MySQL (`inventory_testing`, user `inventory`, password `inventory` on localhost, or the `DB_*` variables from CI).
- Larastan config is `phpstan.neon`.
- Do not commit `.env` or real credentials. Demo passwords in the README are for local use only.
