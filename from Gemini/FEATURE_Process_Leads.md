---

## 2. Updated `FEATURE_Process_Leads.md`

Added the more detailed `crm_contacts` schema and the `INSERT ... SELECT` mapping.

```markdown
## Feature: Process Tagged Leads

### Goal
Move selected data from leads marked as 'tagged' into a separate, cleaner `crm_contacts` table and update their original status.

### Requirements
1.  **UI Element:** Button "Process Tagged Leads".
2.  **Action:** Triggers a backend process (direct or background job).
3.  **Backend Logic:**
    * **Step 1: Create `crm_contacts` table (if not exists):**
        Use the schema defined in `README.md`. Ensure relevant columns (`email`, `company_name`, `country_location`, `industry`, `sector`, `lead_classification`, `lead_status`, `present_status`, `next_action_date`) have database indexes.
    * **Step 2: Copy Data:** Execute an `INSERT INTO crm_contacts (...) SELECT ... FROM leads WHERE status = 'tagged'` query.
        * Map available fields from `leads` to `crm_contacts`:
            * `original_lead_id` <- `leads.id`
            * `first_name` <- `leads.First Name`
            * `last_name` <- `leads.Last Name`
            * `job_title` <- `leads.Job title`
            * `email` <- `leads.Emails`
            * `mobile` <- `leads.Mobile`
            * `phone` <- `leads.Phone numbers`
            * `company_name` <- `leads.Company Name`
            * `industry` <- `leads.Industry` *(Note: May need mapping to dropdown options later)*
            * `size_of_company` <- `leads.Company Size` *(Note: May need mapping)*
            * `website` <- `leads.Company Website`
            * `city` <- Extract from `leads.Location` or specific company location fields if possible.
            * `postal_code` <- `leads.Company Location Postal Code`
            * `country_location` <- `leads.Location Country` *(Note: May need mapping)*
            * *(Other fields in `crm_contacts` will likely be `NULL` or default initially)*
    * **Step 3: Update Original Status:** Execute `UPDATE leads SET status = 'processed' WHERE status = 'tagged'`.
4.  **Feedback:** Notify user of success/failure and number processed. Refresh lead list.

### Database Interaction
* `CREATE TABLE IF NOT EXISTS crm_contacts ...` (with indexes)
* `INSERT INTO crm_contacts (original_lead_id, first_name, last_name, ..., country_location) SELECT id, \`First Name\`, \`Last Name\`, ..., \`Location Country\` FROM leads WHERE status = 'tagged'`
* `UPDATE leads SET status = 'processed' WHERE status = 'tagged'`

### Implementation Notes
* Use a database transaction.
* Consider a background job (Laravel Queues) if processing many leads takes > 30 seconds.
* Ensure the `CREATE TABLE` command includes necessary indexes for `crm_contacts`.