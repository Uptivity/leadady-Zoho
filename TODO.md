# LeadSpark CRM – Continuation Checklist

## Immediate Next Steps
1. **Lead Filtering Dashboard**
   - Build `resources/views/leads/index.blade.php` (or reuse `dashboard.blade.php`) with inputs for Industry, Company Industry, and Location plus “has” checkboxes.
   - Wire front-end JS (Vite entry in `resources/js/app.js`) to debounce input changes, call a new route (e.g., `Route::post('/leads/counts')`) via Fetch, and update live count elements.
   - Create controller (e.g., `app/Http/Controllers/LeadFilterController.php`) with `counts()` method querying `leads` table using `LIKE` clauses and aggregate counts.
   - Ensure middleware `crm.auth` wraps all lead routes; reuse CSRF token from meta tag on AJAX calls.

2. **Apply Filters Action & Paginated Results**
   - Add `Route::get('/leads')` that receives all filter params (text + “has” requirements) and returns paginated results (`->paginate(100)`).
   - Render table with columns defined in `FEATURE_Lead_Display.md`; include placeholder tagging button/checkbox per row (data attribute with lead id).
   - Preserve filter state across pagination (append query string).

3. **Tagging Endpoint**
   - Implement `Route::post('/leads/{lead}/tag')` (AJAX) calling `LeadTagController@store`; update `status` to `tagged`.
   - Return JSON payload so UI can disable or restyle the tagged row.
   - Add Feature tests covering tag endpoint and middleware protection.

## Subsequent Milestones
4. **Process Tagged Leads**
   - Create migration for `crm_contacts` using schema from `from gemini/database schema.txt` (include indexes).
   - Build service/command (e.g., `ProcessTaggedLeadsAction`) triggered by UI button to run transaction: insert into `crm_contacts` via `INSERT ... SELECT`, then mark processed leads.
   - Add progress feedback (flash message or JSON response) with counts.

5. **Dropdown Admin**
   - Migration + model `DropdownOption`; seeder `DropdownOptionsSeeder` with baseline values.
   - Admin screens for CRUD (list grouped by field, toggle `is_active`, no hard deletes).
   - Protect routes with `crm.auth`; add feature tests ensuring option visibility.

6. **Contact Management**
   - Index + detail controllers for `crm_contacts`; integrate dropdown options and validation.
   - Activity log table (`contact_activities`), model, and UI components for logging interactions; update `last_contacted`.
   - Add coverage for list filters, edits, and activity creation.

## Housekeeping
- Update `database/seeders/DatabaseSeeder.php` to register new seeders.
- Consider extracting filter/query logic into dedicated classes under `app/Services/Leads`.
- Once Vite assets exist, remove the testing guard in `resources/views/components/layouts/app.blade.php` if desired or document the requirement.
- Document final commands and architecture additions in `README.md` before hand-off.
