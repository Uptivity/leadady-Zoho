## Feature: Contact Management Interface

### Goal
Provide an interface to view, edit, and manage processed contacts stored in the `crm_contacts` table, similar to the Zoho CRM layout.

### Requirements
1.  **Contact List View:**
    * Create a page displaying contacts from the `crm_contacts` table.
    * Make it searchable and sortable, similar to the Zoho "listing page" screenshot. Key sortable/filterable columns: `company_name`, `email`, `lead_status`, `present_status`, `next_action_date`, `allocated_to`, `industry`.
    * Display key information directly in the list (e.g., Name, Company, Status, Next Action Date, Allocated To).
    * Use pagination.
    * Provide a link/button to view/edit the full contact details.
2.  **Contact Detail/Edit View:**
    * Create a form similar to the Zoho "Edit Lead" screenshots to display and allow editing of **all** fields in the `crm_contacts` table.
    * **Dropdown Fields:** For fields marked as *(Dropdown?)* in the `README.md` schema (e.g., `lead_status`, `industry`, `sector`, `country_location`, `size_of_company`, `lead_classification`, `lead_source`, `fit`, `allocated_to`), the form should render `<select>` dropdowns.
    * **Populate Dropdowns:** Fetch the available options for each dropdown from the `dropdown_options` table (See `FEATURE_Dropdown_Admin.md`).
    * **Save Functionality:** Implement logic to save changes made in the form back to the `crm_contacts` table. Use Laravel validation.
3.  **Activity Logging (Basic):**
    * Add a section in the Contact Detail view to manually log interactions (e.g., "Log Email Sent", "Log Call Made", "Log Meeting").
    * This might require a new table like `contact_activities` (`id`, `contact_id`, `activity_type`, `notes`, `activity_date`).
    * When an activity is logged, update the `crm_contacts.last_contacted` field.
4.  **Next Actions:**
    * Allow users to easily set/update the `next_action` and `next_action_date` fields on the contact record.

### Database Interaction
* `SELECT * FROM crm_contacts WHERE ... ORDER BY ... LIMIT ... OFFSET ...` for the list view.
* `SELECT * FROM crm_contacts WHERE id = ?` for the detail view.
* `SELECT DISTINCT option_value FROM dropdown_options WHERE field_name = ? AND is_active = TRUE` to populate dropdowns.
* `UPDATE crm_contacts SET ... WHERE id = ?` to save changes.
* `INSERT INTO contact_activities (...) VALUES (...)` to log activities.

### Implementation Notes
* Use Laravel Eloquent models for `CrmContact` and potentially `DropdownOption` and `ContactActivity`.
* Use Blade templates for the views.
* Use Laravel validation for the edit form.
* Consider using AJAX for saving activity logs or quick updates to status/next action without full page reloads.