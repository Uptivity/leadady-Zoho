# LeadSpark CRM – Continuation Checklist

## Immediate Next Steps (BigQuery + API Pull)
1. Lead Filtering Dashboard (BigQuery)
   - [Done] Filter UI + required toggles; counts + preview wiring.
   - Add any extra “not blank” toggles you need.

2. BigQuery Queries
   - [In Code] Parameterized SQL in `BigQueryService` for counts, preview, iterate (requires `google/cloud-bigquery`).
   - Validate WHERE builder against actual data (case-insensitive LIKEs on Industry, Company_Industry, Location).

3. Pull to Destination via API (No CSV)
   - [Scaffolded] `POST /pull/start`, `GET /pull/{id}/status`, job state via `pull_jobs`.
   - Implement retries/backoff (after API contract) and idempotency.

4. Mapping & Transform
   - Fill `config/lead_mapping.php` using the PDF mapping. Use array form for multi-source fields to auto-join with ". ".
   - Add dropdown normalization rules if required (nearest option mapping).

## Next Milestones
5. Reliability & Ops
   - Tune `DEST_BATCH_SIZE` and `PULL_MAX_ROWS` per API/BigQuery limits.
   - Add basic metrics/logging and PII redaction where needed.

6. Tests
   - Unit-test WHERE builder and transformer (single and multi-column joins, trimming, dedupe).
   - Feature-test endpoints with faked services (no real BigQuery/API calls).

## Housekeeping
- Add `docs/WHAT_IS_REQUIRED.md` with pending integrations and inputs. [Done]
- Install `google/cloud-bigquery` in your environment to enable live queries.
- Rotate any committed keys; keep credentials out of the repo.
