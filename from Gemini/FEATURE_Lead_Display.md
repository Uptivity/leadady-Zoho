## Feature: Display Filtered Leads

### Goal
Show a paginated list of leads matching the user's selected filters (including the "Has/Has Not" criteria).

### Requirements
1.  **Trigger:** When the user clicks the "Apply Filters" button (from `FEATURE_Lead_Filtering.md`).
2.  **Backend Query:** The backend should receive all filter criteria (text inputs and checkbox states).
3.  **Database Query:**
    * Construct a `SELECT * FROM leads WHERE ...` query.
    * Include `LIKE '%value%'` conditions for the text filters.
    * Include `column_name IS NOT NULL` conditions for the checked "Has" boxes.
    * Only select leads where `status` is 'new' (or perhaps allow filtering by status later).
    * Implement **pagination** (e.g., 50 or 100 results per page) using Laravel's Paginator.
4.  **UI Display:**
    * Display the results in a clear table or list format below the filters.
    * Show relevant columns (e.g., `Full name`, `Company Name`, `Industry`, `Job title`, `Emails`, `Mobile`, `Company Website`, `Location`).
    * Display pagination controls (e.g., Next, Previous, Page numbers).
5.  **Tagging Placeholder:** Include a button or checkbox next to each lead row for tagging (See `FEATURE_Lead_Tagging.md`).

### Implementation Notes
* Pass filter data from the frontend to a dedicated Laravel controller method.
* Use Laravel's Paginator for efficient data fetching and rendering pagination links (`->paginate(100)`).
* Render the results using a Blade template.