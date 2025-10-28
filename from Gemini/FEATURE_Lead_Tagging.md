## Feature: Tag Leads

### Goal
Allow users to mark individual leads in the displayed list as 'tagged' for later processing.

### Requirements
1.  **UI Element:** Add a button or checkbox (e.g., "Tag for Processing") to each lead row displayed in `FEATURE_Lead_Display.md`.
2.  **Action:** When the user clicks the "Tag" button/checkbox for a specific lead:
    * Send an AJAX request to the backend.
    * Include the unique `id` of the lead being tagged.
3.  **Backend Logic:**
    * Receive the lead `id`.
    * Update the `status` column of that specific lead in the `leads` table to `'tagged'`.
4.  **Feedback (Optional but Recommended):** Update the UI element visually to indicate the lead has been tagged (e.g., change button text to "Tagged", disable checkbox).

### Database Interaction
* Use an `UPDATE leads SET status = 'tagged' WHERE id = ?` query. Leverage the `id` (Primary Key) and `status` indexes.

### Implementation Notes
* Use JavaScript to handle the click event and make the AJAX `POST` or `PUT` request.
* Create a dedicated Laravel route and controller method to handle the update.
* Ensure CSRF protection is used for the AJAX request.