# Digi Wish — Setup Guide (XAMPP + PHP + MySQL)

## Folder overview

```
digiwish/
├── index.html          Page 1: Login (+ link to signup)
├── signup.html         Page 1b: Sign up
├── dashboard.php       Page 2: Choose occasion + Order Details form
│                        (this one is .php so it can check the session)
├── style.css             All styling, golden/red/blue theme, dark+light mode
├── theme.js              Dark/light mode toggle
├── main.js               Form validation, festival buttons, details toggle
├── config.php            MySQL connection settings (edit if needed)
├── signup.php            Creates a new user
├── login.php             Verifies login, starts session
├── submit_order.php      Saves a wish order
├── logout.php            Ends the session
└── digiwish.sql          Database + table creation script
```

## 1. Install into XAMPP

1. Install/open **XAMPP** and start the **Apache** and **MySQL** modules from the XAMPP Control Panel.
2. Copy the whole `digiwish` folder into your XAMPP `htdocs` directory, e.g.:
   - Windows: `C:\xampp\htdocs\digiwish`
   - macOS: `/Applications/XAMPP/htdocs/digiwish`
   - Linux: `/opt/lampp/htdocs/digiwish`

## 2. Create the database

1. Open `http://localhost/phpmyadmin` in your browser.
2. Click **Import** → choose the file `digiwish.sql` → click **Go**.
   - This creates the `digiwish` database with the `users` and `orders` tables.
   - Alternatively, run it from a terminal: `mysql -u root -p < digiwish.sql`

## 3. Check the DB connection settings

Open `config.php`. The defaults match a fresh XAMPP install:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');       // set this if your MySQL root user has a password
define('DB_NAME', 'digiwish');
```

## 4. Run it

Visit: `http://localhost/digiwish/index.html`

- **Sign up** → creates a row in `users` (password is stored hashed, never plain text).
- **Log in** → checks username + gmail + password, starts a PHP session, redirects to `dashboard.php`.
- **Dashboard** → pick an occasion, click **Order Details**, fill the form, submit → saves a row in `orders` linked to your account.
- **Log out** → clears the session, back to the login page.

## 5. Deploy to InfinityFree

1. In the InfinityFree control panel, open **MySQL Databases** and create a database. Copy the exact **MySQL hostname**, database username, database name, and password shown there. The hostname usually looks like `sqlXXX.infinityfree.com`; it is not your website URL and not `dash.infinityfree.com`.
2. Open `config.php` on the uploaded site and replace the fallback values after `?:` with those MySQL values. For example, the connection settings should resolve to:

```php
define('DB_HOST', 'sqlXXX.infinityfree.com');
define('DB_USER', 'if0_XXXXXXXX');
define('DB_PASS', 'your-database-password');
define('DB_NAME', 'if0_XXXXXXXX_digiwish');
```

Keep `DB_PORT` as `3306`. Do not use the InfinityFree dashboard or domain URL as `DB_HOST`. 3. Open InfinityFree phpMyAdmin, select the database you created, choose **Import**, and upload `digiwish online.sql`. This file contains the tables and does not try to create a database, which is required on shared hosting. 4. Upload the project files with FileZilla into the domain’s `htdocs` folder. Open `https://your-domain/` after the upload and test sign-up, login, and order submission.

For local XAMPP, leave the environment variables unset: `config.php` uses `localhost`, `root`, an empty password, and the `digiwish` database by default. Never reuse or publish a hosting database password in a public repository.

## Dark / Light mode

The toggle button (top-right, every page) flips a `data-theme="dark"` attribute on `<html>` and remembers the choice in `localStorage`, so it persists across pages and future visits. It also respects the visitor's OS-level preference on first visit.

## Adding real file uploads (image / video / audio)

Right now the "Need image / video / audio?" fields only record **Yes/No** — no file is uploaded yet, keeping the form simple. To accept real files later:

1. In `dashboard.php`, add `enctype="multipart/form-data"` to `<form id="orderForm" ...>`.
2. Add `<input type="file" name="imageFile">` (and similarly for video/audio) next to the Yes/No selects.
3. In `submit_order.php`, use `move_uploaded_file($_FILES['imageFile']['tmp_name'], $destinationPath)` to save the file, and add an `image_path` column to the `orders` table to store where it went.

## Optional: Google Sign-In

The "Continue with Google" button on `index.html` is a placeholder (`js/main.js` just shows an alert). To make it real:

1. Create OAuth credentials in the [Google Cloud Console](https://console.cloud.google.com/).
2. Add the Google Identity Services script and initialize it with your Client ID.
3. On success, send the returned Google email to a new PHP endpoint (e.g. `google_login.php`) that looks up or creates a user by that gmail, then starts the session like `login.php` does.

## Adding more festivals/occasions

In `dashboard.php`, copy one line inside `<div class="festival-grid">` and change the emoji, label, and `data-festival` value — no CSS or JS changes needed.
