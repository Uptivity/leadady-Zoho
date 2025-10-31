# Project: Lead Filtering and Processing CRM (LeadSpark)

## 1. Goal

Build a web application using the Laravel framework to:
1.  Filter a large list of raw leads (`leads` table).
2.  Allow users to tag relevant leads.
3.  Process tagged leads into a structured `crm_contacts` table.
4.  Provide a CRM interface to manage these contacts, track interactions, and manage follow-ups.
5.  (Future) Integrate email sending and funnel tracking.

## 2. Core Workflow

1.  **Login:** User authenticates.
2.  **Filter Leads:** User applies filters to the `leads` table, sees live counts.
3.  **Select & View Leads:** User selects refined criteria, views a paginated list.
4.  **Tag Leads:** User marks interesting leads (`status` = 'tagged').
5.  **Process Tagged Leads:** User triggers action to copy selected data into `crm_contacts` table, cleaning and structuring it. Original lead `status` becomes 'processed'.
6.  **Manage Contacts:** User views `crm_contacts` in a list/detail view. They can edit fields (many using predefined dropdowns), log activities (emails, calls), set next actions, and update statuses.
7.  **(Future) Email Outreach:** Integrate email services to send personalized emails from the contact view.
8.  **(Future) Track Funnel:** Dashboard showing email responses and contact stages.

## 3. Technology Stack

* **Backend:** PHP / Laravel (Latest stable version)
* **Database:** Google Cloud SQL (MySQL 8.x)
* **Frontend:** HTML, CSS, JavaScript (Laravel Blade, potentially Alpine.js/Vue.js for interactivity)

## 4. Database Connection (`.env` file)

```dotenv
DB_CONNECTION=mysql
DB_HOST=[Your Cloud SQL Public IP Address]
DB_PORT=3306
DB_DATABASE=crm_db
DB_USERNAME=root
DB_PASSWORD=[Your Root Password]