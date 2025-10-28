Feature: Basic User Authentication
Goal
Implement a simple username/password login system to protect access to the CRM dashboard.

Requirements
Login Form: Create a page with fields for username and password, and a submit button.

Credentials: Use credentials stored securely in the .env file (e.g., CRM_USERNAME, CRM_PASSWORD). You can use the index.php file you provided earlier as a reference for the logic, but implement it using Laravel's standard authentication practices (e.g., using Sessions).

Validation: If login fails, show an error message.

Success: If login succeeds, redirect the user to the main lead filtering dashboard and establish a session.

Protection: Ensure all other CRM pages/routes require the user to be logged in. Redirect unauthenticated users to the login page.

Logout: Provide a logout button/link that destroys the session and redirects to the login page.

Implementation Notes
Use Laravel's built-in session management.

Consider using Laravel's basic authentication scaffolding (php artisan make:auth or similar, depending on the Laravel version) as a starting point if appropriate, but simplify it to use .env credentials instead of a users database table for this initial version.