# What Is Required (Pending Integrations and Inputs)

This document lists the remaining items needed to finish the project end-to-end.

## Destination API
- Base URL: set `DEST_API_BASE_URL` in `.env`.
- Auth: API key in `DEST_API_KEY` (or confirm OAuth/headers if different).
- Endpoints:
  - POST `/api/leads/import` (placeholder): accepts `{ records: [ { ...mappedFields } ] }`.
  - Define response shape: `{ success, failed, errors: [] }`.
- Constraints:
  - Max payload size (bytes) and max records per request.
  - Rate limiting and recommended concurrency.
  - Idempotency strategy (unique key, deduplication rules).
- Error taxonomy: which errors are retryable vs permanent.

## Field Mapping
- Provide final mapping from BigQuery columns to destination fields.
- Source: `columns mappings - columns.pdf` (column 1 → BigQuery; column 2 → destination; column 3 notes).
- Update `config/lead_mapping.php` accordingly.
- Notes for multi-source fields:
  - When >1 BigQuery columns map to one destination field, values are concatenated with a full stop and space (`. `).
  - Dropdown fields: “select nearest option” requires a defined option set or mapping rules (TBD) to normalize values.

## BigQuery Access
- Install the package: `composer require google/cloud-bigquery`.
- Service account:
  - Role: “BigQuery User”.
  - Credentials JSON stored outside the repo; set `GOOGLE_APPLICATION_CREDENTIALS` to the absolute path.
  - Project, dataset, table: `GOOGLE_PROJECT_ID`, `BQ_DATASET`, `BQ_TABLE`.
- Cost guardrails: confirm limits (`PULL_MAX_ROWS`, per-page size) to manage spend.

## Operational Settings
- Batch size: `DEST_BATCH_SIZE` (default 200). Adjust based on destination limits.
- Pull cap: `PULL_MAX_ROWS` to avoid accidental huge pulls.
- Queue: using `database` driver; configure worker/cron for `php artisan queue:work`.
- Logging: confirm if we should redact PII in logs.

## Company Email Logic (Refinement)
- Current heuristic: company email assumed if `Company_Website` and `Emails` are not blank.
- Desired: validate email domain matches company domain; decide which column is the trusted email.

## UI/UX
- Confirm additional required toggles (“not blank” fields) beyond phone/email/company email.
- Confirm column order and visibility for preview.

## Security
- Rotate any previously committed GCP key.
- Store secrets in environment (never commit). Add repository secrets for deployment.

## Repository & Deployment
- Create GitHub repository and push code (excluding secrets).
- Configure CI to run tests and optionally Lint (`./vendor/bin/pint`).
- Deployment environment variables: see `.env.example` entries for BigQuery and Destination API.

