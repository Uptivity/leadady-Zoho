## Feature: Lead Filtering Interface

### Goal
Create the main dashboard interface allowing users to filter leads based on core criteria and see live counts.

### Requirements
1.  **UI Layout:** Create a main dashboard view (e.g., `leads.blade.php`).
2.  **Filter Inputs:**
    * Text input for `Industry`.
    * Text input for `Company Industry`.
    * Text input for `Company Location Name`.
    * *(Consider using dropdowns populated with distinct values later for better UX, but start with text inputs)*
3.  **Live Count Display:** Add areas (e.g., using `<span>` tags with IDs) next to or below the filters to display counts:
    * Total matching records based on the text filters.
    * Number of those matching records that have a non-empty `Emails` field.
    * Number of those matching records that have a non-empty `Mobile` or `Phone numbers` field.
    * Number of those matching records that have a non-empty `Company Name` field.
    * Number of those matching records that have a non-empty `Company Website` field.
4.  **"Has/Has Not" Selection:** Add checkboxes or similar controls for the user to specify if they *require* certain fields to be filled:
    * [ ] Has Email
    * [ ] Has Phone (Mobile or Phone numbers)
    * [ ] Has Company Name
    * [ ] Has Company Website
5.  **Dynamic Updates (Counts):** When the user types in the main filter inputs (`Industry`, `Company Industry`, `Location`), use AJAX (e.g., with Fetch API or Axios) to send the filter values to a Laravel backend route. This route should perform a database query (similar to the `SELECT COUNT(...)` query discussed previously) and return the counts as JSON. Update the display areas on the page with the returned counts without a full page reload.
6.  **Apply Filters Button:** A button to trigger the *full* search (including the "Has/Has Not" checkboxes) which will load the results (See `FEATURE_Lead_Display.md`).

### Database Interaction
* The AJAX endpoint needs to query the `leads` table using `WHERE` clauses based on the input filters. Use `LIKE '%value%'` for text inputs.
* Use `COUNT(*)` and `COUNT(column_name)` to efficiently get the required counts. Leverage the existing indexes.

### Implementation Notes
* Use Laravel routes and controllers to handle the AJAX requests and database queries.
* Use JavaScript to handle input changes, make AJAX calls, and update the count displays.