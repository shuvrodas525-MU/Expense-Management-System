EXPENSEFLOW - COMPLETE LOCAL XAMPP BUILD

1. Copy this entire folder to C:\xampp1\htdocs\project (or your XAMPP htdocs folder).
2. Start Apache and MySQL.
3. Open phpMyAdmin and select/import sql_cleanup.sql. It contains USE expense; so the #1046 database-not-selected error is avoided.
4. Open http://localhost/project/welcome.php
5. Create the first account. To make it admin, run in phpMyAdmin:
   UPDATE reg_users SET is_admin=1 WHERE user_name='your_username';
   Then log out and log in again.

IMPORTANT:
- This package contains ALL PHP/CSS/JS/SQL files needed by the rebuilt project.
- No demo expense rows are inserted.
- Dashboard totals, recent expenses and reports are calculated only from expense_info.
- Each expense belongs to the logged-in user through expense_info.user_id.
- Forgot password is a local XAMPP username/password reset flow, not email recovery.
