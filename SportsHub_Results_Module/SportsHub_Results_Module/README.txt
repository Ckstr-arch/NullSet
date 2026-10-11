NSBM SportsHub - Results Module
================================

Files:
- database.sql: creates a sample database and tables for the Results module.
- db.php: database connection.
- results.php: public/student page to view match results.
- admin_results.php: simple admin page to add/update/delete results.
- style.css: styling.

Setup:
1. Install XAMPP and start Apache + MySQL.
2. Copy the SportsHub_Results_Module folder into htdocs.
3. Open http://localhost/phpmyadmin and import database.sql.
4. If needed, edit db.php with your MySQL username/password.
5. Open http://localhost/SportsHub_Results_Module/results.php

Admin page:
- Open http://localhost/SportsHub_Results_Module/admin_results.php
- Demo admin password is `admin123` (change it before submission/deployment).
- This demo uses a simple password check to make the module runnable by itself.
- When integrating with the group project, replace the demo check with your existing login/session role check.

Note:
This is a self-contained demo schema. If your group already has a Matches table, adapt the SQL/query field names to that shared schema rather than importing duplicate tables.
