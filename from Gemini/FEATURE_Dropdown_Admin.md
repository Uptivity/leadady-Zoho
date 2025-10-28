## Feature: Dropdown Options Administration

### Goal
Create a simple admin interface to allow adding, editing, and disabling options for the various dropdown fields used in the `crm_contacts` form.

### Requirements
1.  **Database Table:**
    * Create a table named `dropdown_options` as defined in `README.md` (`id`, `field_name`, `option_value`, `is_active`).
2.  **Admin UI:**
    * Create a new section in the application (accessible only to an admin user, or perhaps using the same main login for now).
    * Display a list of all current dropdown options, grouped by `field_name`.
    * Allow filtering the list by `field_name`.
    * Provide a form to add a new option (selecting the `field_name` it belongs to and entering the `option_value`).
    * Allow editing the `option_value` of existing options.
    * Allow toggling the `is_active` status (disabling an option prevents it from showing in future dropdowns but preserves it for historical records). Do not allow deletion, only deactivation.
3.  **Seed Initial Options:** Provide a mechanism (e.g., Laravel Seeders) to populate the `dropdown_options` table with initial known values for fields like `lead_status`, `industry`, etc., based on your Zoho setup or expected values.

### Database Interaction
* Standard CRUD (Create, Read, Update, Delete - although we only Update `is_active` for deletion) operations on the `dropdown_options` table.

### Implementation Notes
* Create an Eloquent model `DropdownOption`.
* Create controllers, routes, and Blade views for the admin interface.
* Implement basic authorization to protect this section.
* Use Laravel Seeders to add default dropdown values.