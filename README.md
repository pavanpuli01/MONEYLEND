MONEYLEND - CYBERSECURITY PROJECT
----------------------------------

MoneyLend is a PHP-based loan management web app enhanced with strong cybersecurity features.  
It not only manages users and loans but also protects against brute-force login attempts, tracks IP addresses, logs locations, and manages secure sessions.

---------------------------
🔐 CYBERSECURITY FEATURES
---------------------------

1. Login attempt monitoring
2. Account lockout after multiple failed attempts (5 minutes cooldown)
3. IP logging for failed login attempts
4. Geolocation tracking of suspicious IPs (using IP APIs)
5. Protection against brute-force attacks
6. 🧠 **Session management** with secure login/logout tracking

-------------------
🚀 LOCAL SETUP GUIDE
-------------------

1. Requirements:
   - PHP (>= 7.4)
   - MySQL or MariaDB
   - Apache server (use XAMPP/WAMP)

2. Clone or copy this project into your web directory:
   Example: C:/xampp/htdocs/MONEYLEND

3. Start Apache and MySQL using XAMPP Control Panel

4. Open browser and visit:
   http://localhost/MONEYLEND/setup.php

   ✅ This will automatically:
     - Create the 'money_lend' database
     - Create required tables including 'users' and 'login_attempts'

5. Done! You can now use:
   - http://localhost/MONEYLEND/1login.html

--------------------------
🗃️ KEY FILES & STRUCTURE
--------------------------

- setup.php            : Auto creates DB and tables
- login.php            : User login logic with brute-force prevention
- register.php         : New user registration
- dashboard.php        : Main interface after login
- login_attempts table : Stores failed login IPs and timestamps

--------------------------
📌 HOW BRUTE-FORCE BLOCK WORKS
--------------------------

- After 5 failed login attempts from the same IP or for the same user,
  the account is temporarily locked for 5 minutes.

- Each failed attempt is logged with:
    - IP address
    - Attempt time
    - Approximated location (using IP Geolocation)

- This helps in tracking suspicious activity and blocking repeat offenders.

-------------------------
  ## 🔐 SESSION MANAGEMENT
-------------------------

- Sessions are used to:
  - Maintain secure login across pages
  - Protect restricted pages like `dashboard.php`
  - Prevent unauthorized access via `session_start()` and checks

-------------------
📧 AUTHOR
-------------------

Name      : Pavan Puli
Email     : paonepuli999@gmail.com
GitHub    : https://github.com/pavanpuli01
LinkedIn  : https://www.linkedin.com/in/pavan-puli-2003bl

--------------------
📄 LICENSE
--------------------

This project is open source and licensed under the MIT License.

--------------------
