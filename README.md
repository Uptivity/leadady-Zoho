# Project: Lead Filtering and Processing CRM (LeadSpark)

## 1. Goal

Build a Laravel application that filters a large list of leads stored in Google BigQuery (read-only) and pulls filtered results into another system via API. No CSV workflows; users filter, preview, and trigger a background pull that sends batches to a destination API.

## 2. Core Workflow

1. **Login:** User authenticates (simple `.env` password).
2. **Filter Leads:** User applies filters (Industry, Company Industry, Location) with live counts for subsets (has phone, has email, has company email). Required toggles further constrain results without writing to BigQuery.
3. **Preview:** Paginated preview (100/page) of matching rows from BigQuery.
4. **Pull to Destination:** User clicks “Pull to Destination”. The app starts a background job that streams from BigQuery and POSTs records in batches (default 200) to a destination API. Users can view progress and results. BigQuery remains read-only.

## 3. Technology Stack

- Backend: PHP / Laravel 11
- Data Source: Google BigQuery (read-only)
- UI: Blade + vanilla JS (Vite)

## 4. BigQuery Details

- Project ID: `leads-bigquery`
- Dataset ID: `crm_data`
- Table ID: `leads`
- Columns: see `columns.txt` for exact BigQuery column names (e.g., `Industry`, `Company_Industry`, `Location`, `Emails`, `Mobile`, `Phone_numbers`, `Company_Website`).

## 5. Laravel Setup

1) Install BigQuery client library (needed to execute queries):
```bash
composer require google/cloud-bigquery
```

2) Service Account:
- Create a Google Cloud Service Account with the “BigQuery User” role.
- Download its JSON key file and store it OUTSIDE the repository (never commit).
- Set `GOOGLE_APPLICATION_CREDENTIALS` to the absolute path of that file.

3) .env variables (see `.env.example`):
```dotenv
# App Auth
CRM_USERNAME=admin
CRM_PASSWORD=your_secure_password

# BigQuery (read-only)
GOOGLE_PROJECT_ID=leads-bigquery
GOOGLE_APPLICATION_CREDENTIALS=/absolute/path/outside/repo/leadspark-gcp-key.json
BQ_DATASET=crm_data
BQ_TABLE=leads

# Destination API (batches default 200)
DEST_API_BASE_URL=
DEST_API_KEY=
DEST_BATCH_SIZE=200
PULL_MAX_ROWS=50000
```

## 6. Current Endpoints and Flow

- `POST /leads/counts` → JSON { total, with_phone, with_email, with_company_email } for current filters.
- `GET /leads` → JSON { data, total, page, per_page } preview under filters + required toggles.
- `POST /pull/start` → starts a background pull job with current filters + toggles.
- `GET /pull/{id}/status` → job status and progress (processed/failed/total).

## 7. Implementation Notes

- Required toggles:
  - Phone: `Mobile` or `Phone_numbers` not blank.
  - Email: `Emails` not blank.
  - Company Email (temporary heuristic): `Company_Website` and `Emails` not blank. This will be refined later with proper domain matching and mapping.
- BigQuery queries are parameterized and built in `app/Services/BigQuery/BigQueryService.php`. If the BigQuery library is not installed, methods safely return empty results so the UI can load without errors.
- Pull jobs use `app/Jobs/PullLeadsJob.php`, batching via `app/Services/DestinationApiClient.php`. Payload mapping is handled by `app/Services/LeadTransformer.php` reading from `config/lead_mapping.php`.

## 8. Pending Integrations (To Finish)

- Destination API: endpoint path, payload schema, auth requirements, and rate/concurrency limits. Update `DestinationApiClient` accordingly.
- Field Mapping: complete `config/lead_mapping.php` with mappings from BigQuery column names (`columns.txt`) to destination fields.
- BigQuery Client Package: run `composer require google/cloud-bigquery` in your environment to enable live queries.
- Company Email Logic: refine detection to ensure emails match company domains instead of the current heuristic.
- Limits and Retries: tune `DEST_BATCH_SIZE`, `PULL_MAX_ROWS`, and add retry/backoff/idempotency once destination constraints are known.
- Security: rotate any previously committed keys; keep service account keys out of the repo per `.gitignore`.
